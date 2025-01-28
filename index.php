<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #project-modal {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans">
    <div class="container mx-auto p-2 sm:p-6">
        <!-- Navigation -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-semibold">Task Prioritization Tool</h1>
            <div class="space-x-4">
                <button onclick="toggleHelpModal(true)" 
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fa fa-question-circle mr-2"></i>Help Guide
                </button>
                <a href="dashboard.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    View Dashboard
                </a>
            </div>
        </div>

        <!-- Help Modal -->
        <div id="help-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
            <div class="bg-white rounded-lg p-6 max-w-2xl mx-auto mt-20">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">How to Use Task Manager</h3>
                    <button onclick="toggleHelpModal(false)" class="text-gray-500 hover:text-gray-700">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4 text-gray-600">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-2">Projects</h4>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Click "Create New Project" to start a new project</li>
                            <li>Add project details and optional initial tasks</li>
                            <li>Manage tasks within each project separately</li>
                        </ul>
                    </div>
                    
                    <div class="border-l-4 border-green-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-2">Tasks</h4>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Add tasks with urgency, importance, and effort levels</li>
                            <li>Higher urgency/importance = higher priority</li>
                            <li>Effort is measured in hours (0.5 - 24)</li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-purple-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-2">Time Management</h4>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Set your working hours and breaks</li>
                            <li>Tasks are automatically scheduled by priority</li>
                            <li>View task allocation in the schedule view</li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-yellow-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-2">Priority System</h4>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Critical (Red): Score 8-10</li>
                            <li>High (Orange): Score 6-7.9</li>
                            <li>Medium (Yellow): Score 4-5.9</li>
                            <li>Low (Green): Score 0-3.9</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-6 max-w-4xl mx-auto">
            <!-- Project Creation Button -->
            <button id="new-project-btn" class="bg-indigo-500 text-white px-6 py-3 rounded-lg hover:bg-indigo-600 transition-colors">
                <i class="fa fa-plus mr-2"></i> Create New Project
            </button>

            <!-- Project Form Modal -->
            <div id="project-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                <div class="bg-white rounded-lg p-6 max-w-4xl mx-auto mt-20">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Create New Project</h3>
                        <button onclick="closeProjectModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <form id="project-form" class="space-y-4" onsubmit="return validateProjectForm(event)">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                            <input type="text" id="project-name" required
                                class="w-full p-3 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="project-description" rows="3"
                                class="w-full p-3 border border-gray-300 rounded-lg"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" id="project-start-date" required
                                    class="w-full p-3 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                <input type="date" id="project-due-date" required
                                    class="w-full p-3 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select id="project-priority" required class="w-full p-3 border border-gray-300 rounded-lg">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        <!-- Project Initial Tasks Section -->
                        <div class="border-t pt-4 mt-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-lg font-medium">Initial Tasks</h4>
                                <span class="text-sm text-gray-500" id="task-count">0 tasks</span>
                            </div>
                            <div id="project-tasks" class="space-y-3">
                                <!-- Task entries will be added here -->
                            </div>
                            <button type="button" onclick="ProjectManager.addProjectTaskField()" 
                                class="mt-3 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fa fa-plus mr-2"></i>Add Task
                            </button>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="ProjectManager.closeProjectModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                Cancel
                            </button>
                            <button type="submit" class="bg-indigo-500 text-white px-6 py-2 rounded-lg hover:bg-indigo-600">
                                Create Project
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            

            <!-- Time Slot Form -->
            <div id="time-slot-definition" class="bg-white p-6 rounded-lg shadow-lg">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label for="start-time" class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input type="time" id="start-time" value="09:00" required
                            class="w-full p-3 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="end-time" class="block text-sm font-medium text-gray-700">End Time</label>
                        <input type="time" id="end-time" value="17:00" required
                            class="w-full p-3 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="break-time" class="block text-sm font-medium text-gray-700">Break (min)</label>
                        <input type="number" id="break-time" value="0" min="0"
                            class="w-full p-3 border border-gray-300 rounded-lg">
                    </div>
                    <div class="flex items-end">
                        <button onclick="allocateTimeSlots(tasks)" class="w-full bg-blue-500 text-white p-3 rounded-lg">
                            Schedule Tasks
                        </button>
                    </div>
                </div>
            </div>

            <!-- Task List -->
            <div id="task-list" class="bg-white p-6 rounded-lg shadow-lg">
                <!-- Priority Analysis Summary -->
                <div id="priority-analysis" class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Priority Analysis</h3>
    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Critical Priority -->
                        <div class="border-l-4 border-red-500 bg-red-50 rounded-r-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-red-700">Critical Priority</h4>
                                <span id="critical-count" class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full"></span>
                            </div>
                            <div id="critical-tasks" class="text-sm space-y-2"></div>
                        </div>

                        <!-- High Priority -->
                        <div class="border-l-4 border-orange-500 bg-orange-50 rounded-r-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-orange-700">High Priority</h4>
                                <span id="high-count" class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full"></span>
                            </div>
                            <div id="high-tasks" class="text-sm space-y-2"></div>
                        </div>

                        <!-- Medium Priority -->
                        <div class="border-l-4 border-yellow-500 bg-yellow-50 rounded-r-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-yellow-700">Medium Priority</h4>
                                <span id="medium-count" class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full"></span>
                            </div>
                            <div id="medium-tasks" class="text-sm space-y-2"></div>
                        </div>

                        <!-- Low Priority -->
                        <div class="border-l-4 border-green-500 bg-green-50 rounded-r-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-green-700">Low Priority</h4>
                    <!-- Task list content will be populated by JavaScript -->
                </div>

                <button onclick="clearAllTasks()" class="w-full sm:w-auto bg-red-600 text-white py-3 px-4 rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600 transition duration-300 mt-6 text-lg">
                    <i class="fa fa-trash mr-2"></i> Clear All Tasks
                </button>
            </div>
        </div>

        <!-- Error message container -->
        <div id="error-message-container" class="fixed top-4 right-4 z-50"></div>
    </div>

    <script>
        // Core utilities
const ErrorHandler = {
    handle(error, context = '') {
        console.error(`${context}:`, error);
        this.showError(error.message || 'An error occurred');
    },

    showError(message) {
        const container = document.getElementById('error-message-container');
        if (!container) return;
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-500 text-white p-4 rounded-lg shadow-lg mb-2';
        errorDiv.textContent = message;
        
        container.appendChild(errorDiv);

        // Safely remove after delay
        setTimeout(() => {
            if (container.contains(errorDiv)) {
                container.removeChild(errorDiv);
            }
        }, 3000);
    }
};

// Data management
const DataManager = {
    save(key, data) {
        try {
            localStorage.setItem(key, JSON.stringify(data));
            return true;
        } catch (error) {
            ErrorHandler.handle(error, 'Save operation failed');
            return false;
        }
    },

    load(key, defaultValue = null) {
        try {
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : defaultValue;
        } catch (error) {
            ErrorHandler.handle(error, 'Load operation failed');
            return defaultValue;
        }
    }
};

// Task Management
let tasks = [];
let achievements = [];

// Add this after the task management variables (tasks and achievements)
function addTask(taskData) {
    if (!taskData.projectId) {
        ErrorHandler.show('Please select a project first');
        return false;
    }

    const project = ProjectManager.projects.find(p => p.id === taskData.projectId);
    if (!project) {
        ErrorHandler.show('Project not found');
        return false;
    }

    const newTask = {
        ...taskData,
        id: Date.now().toString(),
        completedTime: null,
        scheduledTime: null
    };

    project.tasks.push(newTask);
    DataManager.save('projects', ProjectManager.projects);
    ProjectManager.renderProjects();
    return true;
}

// Core functions - keep only the most recent versions
function calculatePriorityScore(task) {
    if (!task.urgency || !task.importance || !task.effort) return 0;
            
    // Updated weights for better balance
    const urgencyWeight = 0.45;     // Increased weight for urgency
    const importanceWeight = 0.45;   // Increased weight for importance
    const effortWeight = 0.10;      // Reduced weight for effort to minimize its impact
    
    // Enhanced effort normalization (1-5 scale)
    const normalizedEffort = task.effort <= 2 ? 1 :
                            task.effort <= 4 ? 2 :
                            task.effort <= 6 ? 3 :
                            task.effort <= 8 ? 4 : 5;
    
    // Calculate base score (0-10 scale)
    const baseScore = (
        (urgencyWeight * task.urgency) +
        (importanceWeight * task.importance) - 
        (effortWeight * normalizedEffort)
    ) * 2;

    // Return score rounded to 2 decimal places
    return Math.min(10, Math.max(0, baseScore)).toFixed(2);
}

// Add helper function for date calculations
function getDaysUntilDue(dueDate) {
    const now = new Date();
    const due = new Date(dueDate);
    const diffTime = due - now;
    const diffDays = diffTime / (1000 * 60 * 60 * 24);
    return Math.max(0, diffDays);
}

// Update the priority class assignment in updateTaskList
function getPriorityClass(score) {
    const numScore = parseFloat(score);
    if (numScore >= 8) return 'text-red-600 font-bold';     // Critical
    if (numScore >= 6) return 'text-orange-500 font-bold';  // High
    if (numScore >= 4) return 'text-yellow-600';            // Medium
    return 'text-green-600';                                // Low
}

// Update the task element creation in updateTaskList function
function updateTaskList(filteredTasks = tasks) {
    try {
        updatePriorityAnalysis();
        const taskListContent = document.getElementById('task-list-content');
        taskListContent.innerHTML = '';

        // Add table header
        taskListContent.innerHTML = `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metrics</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        `;

        const tbody = taskListContent.querySelector('tbody');

        filteredTasks.forEach(task => {
            const priorityScore = calculatePriorityScore(task);
            const priorityClass = getPriorityClass(priorityScore);
            const priorityLabel = getPriorityLabel(priorityScore);

            const tr = document.createElement('tr');
            tr.className = `${task.completedTime ? 'bg-gray-50' : 'hover:bg-gray-50'} transition-colors`;
            
            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900 ${task.completedTime ? 'line-through' : ''}">
                            ${task.name}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="${priorityClass} text-sm">
                            ${priorityLabel} (${priorityScore})
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-700">
                        U:${task.urgency}/5 | I:${task.importance}/5 | E:${task.effort}h
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">
                        ${task.scheduledTime || 'Not scheduled'}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex space-x-2">
                        ${!task.completedTime ? `
                            <button onclick="completeTask('${task.id}')"
                                class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition-colors">
                                <i class="fa fa-check"></i>
                            </button>
                        ` : `
                            <span class="text-green-600">
                                <i class="fa fa-check-circle"></i>
                            </span>
                        `}
                        <button onclick="deleteTask('${task.id}')"
                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-colors">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            
            tbody.appendChild(tr);
        });

        // Show empty state if no tasks
        if (filteredTasks.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        <i class="fa fa-tasks fa-2x mb-2"></i>
                        <p>No tasks found</p>
                    </td>
                </tr>
            `;
        }
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to update task list');
    }
}

// Add helper function for priority labels
function getPriorityLabel(score) {
    const numScore = parseFloat(score);
    if (numScore >= 8) return 'Critical';
    if (numScore >= 6) return 'High';
    if (numScore >= 4) return 'Medium';
    return 'Low';
}

// Dashboard
const dashboard = {
    charts: {
        tasksStatus: null,
        priority: null,
        effort: null
    },
    
    init() {
        try {
            const dashboardContent = document.getElementById('dashboard-content');
            // Only try to show/hide if element exists
            if (dashboardContent) {
                this.showLoading(true);
            }
            
            this.destroyCharts();
            this.createCharts();
            
            if (dashboardContent) {
                this.showLoading(false);
            }
        } catch (error) {
            ErrorHandler.handle(error, 'Dashboard initialization failed');
        }
    },

    showLoading(show) {
        try {
            // Add null checks for loading elements
            const loadingEl = document.getElementById('dashboard-loading');
            const contentEl = document.getElementById('dashboard-content');
            
            if (loadingEl) {
                loadingEl.classList.toggle('hidden', !show);
            }
            if (contentEl) {
                contentEl.classList.toggle('hidden', show);
            }
        } catch (error) {
            console.error('Loading state update failed:', error);
        }
    },

    destroyCharts() {
        Object.values(this.charts).forEach(chart => {
            if (chart) chart.destroy();
        });
    },

    createCharts() {
        this.createTaskStatusChart();
        this.createPriorityChart();
        this.createEffortChart();
    },

    createTaskStatusChart() {
        try {
            const ctx = document.getElementById('task-status-chart')?.getContext('2d');
            if (!ctx) return;
            
            const pendingTasks = tasks.filter(t => !t.completedTime).length;
            const completedTasks = tasks.filter(t => t.completedTime).length;

            this.charts.tasksStatus = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Completed'],
                    datasets: [{
                        data: [pendingTasks, completedTasks],
                        backgroundColor: ['#4299E1', '#48BB78']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        } catch (error) {
            ErrorHandler.handle(error, 'Failed to create status chart');
        }
    },

    createPriorityChart() {
        try {
            const ctx = document.getElementById('task-priority-chart')?.getContext('2d');
            if (!ctx) return;
            
            const priorityData = tasks.reduce((acc, task) => {
                const priority = Math.round(calculatePriorityScore(task));
                acc[priority] = (acc[priority] || 0) + 1;
                return acc;
            }, {});

            this.charts.priority = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(priorityData),
                    datasets: [{
                        label: 'Tasks by Priority',
                        data: Object.values(priorityData),
                        backgroundColor: '#9F7AEA'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } catch (error) {
            ErrorHandler.handle(error, 'Failed to create priority chart');
        }
    },

    createEffortChart() {
        try {
            const ctx = document.getElementById('task-effort-chart')?.getContext('2d');
            if (!ctx) return;
            
            const effortRanges = {
                'Low (0-2h)': tasks.filter(t => t.effort <= 2).length,
                'Medium (2-4h)': tasks.filter(t => t.effort > 2 && t.effort <= 4).length,
                'High (4-8h)': tasks.filter(t => t.effort > 4 && t.effort <= 8).length,
                'Very High (>8h)': tasks.filter(t => t.effort > 8).length
            };

            this.charts.effort = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(effortRanges),
                    datasets: [{
                        data: Object.values(effortRanges),
                        backgroundColor: ['#68D391', '#F6AD55', '#FC8181', '#F687B3']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12
                            }
                        }
                    }
                }
            });
        } catch (error) {
            ErrorHandler.handle(error, 'Failed to create effort chart');
        }
    }
};

// Time Slot Manager
const TimeSlotManager = {
    validate(startTime, endTime, breakTime) {
        const start = this.parseTime(startTime);
        const end = this.parseTime(endTime);
        
        if (!start || !end) throw new Error('Invalid time format');
        if (start >= end) throw new Error('Start time must be before end time');
        if (breakTime < 0) throw new Error('Break time cannot be negative');
        
        return { start, end, breakTime };
    },

    parseTime(timeString) {
        const [hours, minutes] = timeString.split(':').map(Number);
        return hours + minutes / 60;
    },

    formatTime(decimalTime) {
        const hours = Math.floor(decimalTime);
        const minutes = Math.round((decimalTime - hours) * 60);
        const period = hours >= 12 ? 'PM' : 'AM';
        const formattedHour = hours % 12 || 12;
        return `${formattedHour}:${minutes.toString().padStart(2, '0')} ${period}`;
    },

    calculateAvailableTime(start, end, breakTime) {
        return end - start - (breakTime / 60);
    },

    allocateTimeSlots(tasks, settings) {
        try {
            const { start, end, breakTime } = this.validate(
                settings.startTime,
                settings.endTime,
                settings.breakTime
            );

            let currentTime = start;
            const availableTime = this.calculateAvailableTime(start, end, breakTime);
            const scheduledTasks = [];
            const unscheduledTasks = [];

            // Sort tasks by priority
            const sortedTasks = [...tasks].sort((a, b) => {
                if (a.completedTime) return 1;
                if (b.completedTime) return -1;
                return calculatePriorityScore(b) - calculatePriorityScore(a);
            });

            sortedTasks.forEach(task => {
                if (task.completedTime) {
                    scheduledTasks.push(task);
                    return;
                }

                if (!task.effort || task.effort <= 0) {
                    unscheduledTasks.push({
                        ...task,
                        scheduledTime: "Invalid effort"
                    });
                    return;
                }

                const taskEndTime = currentTime + task.effort;
                if (taskEndTime <= end) {
                    scheduledTasks.push({
                        ...task,
                        scheduledTime: `${this.formatTime(currentTime)} - ${this.formatTime(taskEndTime)}`,
                        startTime: currentTime,
                        endTime: taskEndTime
                    });
                    currentTime = taskEndTime + (breakTime / 60);
                } else {
                    unscheduledTasks.push({
                        ...task,
                        scheduledTime: "Exceeds available time"
                    });
                }
            });

            return {
                scheduled: scheduledTasks,
                unscheduled: unscheduledTasks,
                availableTime,
                remainingTime: end - currentTime
            };
        } catch (error) {
            ErrorHandler.handle(error, 'Time slot allocation failed');
            return null;
        }
    }
};

// Auto Scheduler
const AutoScheduler = {
    strategies: {
        priority: (tasks) => [...tasks].sort((a, b) => calculatePriorityScore(b) - calculatePriorityScore(a)),
        deadline: (tasks) => [...tasks].sort((a, b) => {
            if (!a.dueDate) return 1;
            if (!b.dueDate) return -1;
            return new Date(a.dueDate) - new Date(b.dueDate);
        }),
        effort: (tasks) => [...tasks].sort((a, b) => a.effort - b.effort)
    },

    calculateOptimalTimeSlots(tasks, workingHours) {
        const totalEffort = tasks.reduce((sum, task) => sum + (task.effort || 0), 0);
        const dailyHours = workingHours.end - workingHours.start;
        const daysNeeded = Math.ceil(totalEffort / dailyHours);
        
        return {
            daysNeeded,
            dailyHours,
            totalEffort,
            suggestedBreaks: this.calculateBreaks(tasks.length, dailyHours)
        };
    },

    calculateBreaks(taskCount, dailyHours) {
        // Suggest breaks based on workload
        const baseBreak = 15; // 15 minutes base break
        const breakCount = Math.floor(dailyHours / 2); // Break every 2 hours
        return Math.min(taskCount - 1, breakCount) * baseBreak;
    },

    autoSchedule(tasks, strategy = 'priority') {
        try {
            // Get current working hours
            const start = TimeUtil.toDecimal(document.getElementById('start-time').value);
            const end = TimeUtil.toDecimal(document.getElementById('end-time').value);

            // Calculate optimal scheduling
            const optimal = this.calculateOptimalTimeSlots(tasks, { start, end });
            
            // Update break time with suggested value
            document.getElementById('break-time').value = optimal.suggestedBreaks;

            // Sort tasks according to selected strategy
            const sortedTasks = this.strategies[strategy](tasks.filter(t => !t.completedTime));
            
            // Show scheduling summary
            this.showSchedulingSummary(optimal);

            // Apply the schedule
            return TimeSlotManager.allocateTimeSlots(sortedTasks, {
                startTime: document.getElementById('start-time').value,
                endTime: document.getElementById('end-time').value,
                breakTime: optimal.suggestedBreaks
            });
        } catch (error) {
            ErrorHandler.handle(error, 'Auto-scheduling failed');
            return null;
        }
    },

    showSchedulingSummary(optimal) {
        const summary = `
            <div class="text-sm">
                <p>Total effort: ${optimal.totalEffort.toFixed(1)} hours</p>
                <p>Days needed: ${optimal.daysNeeded}</p>
                <p>Suggested breaks: ${optimal.suggestedBreaks} minutes</p>
            </div>
        `;

        const summaryDiv = document.createElement('div');
        summaryDiv.className = 'fixed bottom-4 right-4 bg-indigo-500 text-white p-4 rounded-lg shadow-lg z-50';
        summaryDiv.innerHTML = summary;
        document.body.appendChild(summaryDiv);
        setTimeout(() => summaryDiv.remove(), 5000);
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', async function() {
    try {
        tasks = DataManager.load('tasks', []);
        setupAutoTimeSlotUpdate();  // Add this line
        initializeCharts();
        updateTaskList();
        if (tasks.length > 0) {
            allocateTimeSlots(tasks);  // Initial allocation
        }
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to initialize application');
    }
});

// Form submissions
document.getElementById('task-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    try {
        const taskName = document.getElementById('task-name')?.value.trim();
        const urgency = Number(document.getElementById('urgency')?.value);
        const importance = Number(document.getElementById('importance')?.value);
        const effort = Number(document.getElementById('effort')?.value);
        
        if (!taskName) throw new Error('Task name is required');
        if (!urgency) throw new Error('Urgency is required');
        if (!importance) throw new Error('Importance is required');
        if (!effort || effort <= 0) throw new Error('Valid effort value is required');

        const taskData = {
            name: taskName,
            urgency,
            importance,
            effort,
            id: Date.now().toString(),
            completedTime: null,
            scheduledTime: null
        };

        // Add task
        tasks.push(taskData);
        DataManager.save('tasks', tasks);
        
        // Update UI
        this.reset();
        
        // Immediately show task list and update it
        const taskList = document.getElementById('task-list');
        if (taskList) {
            taskList.classList.remove('hidden');
            taskList.scrollIntoView({ behavior: 'smooth' });
        }
        
        updateTaskList();
        updatePriorityAnalysis();
        updateDashboard();
        
        // Show success message
        const successMessage = document.createElement('div');
        successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        successMessage.textContent = 'Task added successfully';
        document.body.appendChild(successMessage);
        setTimeout(() => successMessage.remove(), 3000);
        
        // Reallocate time slots if needed
        if (tasks.length > 0) {
            allocateTimeSlots(tasks);
        }
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to add task');
    }
});

// Add these utility functions
function getElementValue(id, defaultValue = '') {
    const element = document.getElementById(id);
    return element ? element.value : defaultValue;
}

function showErrorMessage(message) {
    ErrorHandler.showError(message);
}

function showAllocationSummary(result) {
    const existingSummary = document.querySelector('.allocation-summary');
    if (existingSummary) {
        existingSummary.remove();
    }

    const summary = document.createElement('div');
    summary.className = 'allocation-summary fixed bottom-4 right-4 bg-white p-4 rounded-lg shadow-lg z-50';
    summary.innerHTML = `
        <h3 class="font-bold mb-2">Task Allocation Summary</h3>
        <p>Scheduled Tasks: ${result.scheduled.length}</p>
        <p>Unscheduled Tasks: ${result.unscheduled.length}</p>
        <p>Available Time: ${result.availableTime.toFixed(1)}h</p>
        <p>Remaining Time: ${result.remainingTime.toFixed(1)}h</p>
    `;
    document.body.appendChild(summary);
    setTimeout(() => {
        if (document.body.contains(summary)) {
            document.body.removeChild(summary);
        }
    }, 5000);
}

// Add task completion and deletion handlers
function completeTask(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (task) {
        task.completedTime = new Date().toISOString();
        DataManager.save('tasks', tasks);
        allocateTimeSlots(tasks);  // Reallocate after completion
        updateTaskList();
        updateDashboard();
    }
}

function deleteTask(taskId) {
    tasks = tasks.filter(t => t.id !== taskId);
    DataManager.save('tasks', tasks);
    allocateTimeSlots(tasks);  // Reallocate after deletion
    updateTaskList();
    updateDashboard();
}

function clearAllTasks() {
    if (confirm('Are you sure you want to clear all tasks?')) {
        tasks = [];
        DataManager.save('tasks', tasks);
        updateTaskList();
        updateDashboard();
    }
}

// Fix chart initialization
function initializeCharts() {
    try {
        if (!document.getElementById('task-status-chart')) {
            // If we're not on the dashboard page, just return
            return;
        }

        // First check if elements exist
        const statusChart = document.getElementById('task-status-chart');
        const priorityChart = document.getElementById('task-priority-chart');
        const effortChart = document.getElementById('task-effort-chart');

        if (!statusChart || !priorityChart || !effortChart) {
            console.warn('Some chart elements are missing');
            return;
        }

        // Get contexts safely
        const contexts = {
            status: statusChart.getContext('2d'),
            priority: priorityChart.getContext('2d'),
            effort: effortChart.getContext('2d')
        };

        // Destroy existing charts if they exist
        if (dashboard.charts.tasksStatus) dashboard.charts.tasksStatus.destroy();
        if (dashboard.charts.priority) dashboard.charts.priority.destroy();
        if (dashboard.charts.effort) dashboard.charts.effort.destroy();

        // Create new charts only if context exists
        if (contexts.status) dashboard.createTaskStatusChart();
        if (contexts.priority) dashboard.createPriorityChart();
        if (contexts.effort) dashboard.createEffortChart();

    } catch (error) {
        ErrorHandler.handle(error, 'Chart initialization failed');
    }
}

function updateDashboard() {
    try {
        // Only initialize dashboard if we're on the dashboard page
        if (document.getElementById('dashboard-content')) {
            dashboard.init();
        }
    } catch (error) {
        ErrorHandler.handle(error, 'Dashboard update failed');
    }
}

// Add this new function to analyze and display priority distribution
function updatePriorityAnalysis() {
    try {
        const priorityGroups = {
            critical: { tasks: [], element: 'critical-tasks', countElement: 'critical-count', threshold: 8 },
            high: { tasks: [], element: 'high-tasks', countElement: 'high-count', threshold: 6 },
            medium: { tasks: [], element: 'medium-tasks', countElement: 'medium-count', threshold: 4 },
            low: { tasks: [], element: 'low-tasks', countElement: 'low-count', threshold: 0 }
        };

        // Group tasks by priority
        tasks.forEach(task => {
            if (task.completedTime) return; // Skip completed tasks
            const score = parseFloat(calculatePriorityScore(task));
            
            if (score >= priorityGroups.critical.threshold) {
                priorityGroups.critical.tasks.push(task);
            } else if (score >= priorityGroups.high.threshold) {
                priorityGroups.high.tasks.push(task);
            } else if (score >= priorityGroups.medium.threshold) {
                priorityGroups.medium.tasks.push(task);
            } else {
                priorityGroups.low.tasks.push(task);
            }
        });

        // Update the UI for each priority group
        Object.entries(priorityGroups).forEach(([level, group]) => {
            const element = document.getElementById(group.element);
            const countElement = document.getElementById(group.countElement);
            
            if (!element || !countElement) return;

            // Update count badge
            countElement.textContent = group.tasks.length;

            if (group.tasks.length === 0) {
                element.innerHTML = '<div class="py-3 text-gray-500 text-sm">No tasks</div>';
                return;
            }

            element.innerHTML = group.tasks
                .sort((a, b) => calculatePriorityScore(b) - calculatePriorityScore(a))
                .slice(0, 2)
                .map(task => `
                    <div class="flex items-center justify-between py-1">
                        <div class="flex-1 truncate">
                            <div class="font-medium truncate">${task.name}</div>
                            <div class="text-xs text-gray-500">
                                Score: ${calculatePriorityScore(task)} | ${task.scheduledTime || 'Not scheduled'}
                            </div>
                        </div>
                    </div>
                `).join('');

            if (group.tasks.length > 2) {
                element.innerHTML += `
                    <div class="text-xs text-gray-500 text-right">
                        +${group.tasks.length - 2} more
                    </div>
                `;
            }
        });
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to update priority analysis');
    }
}

// Add this new function for auto-updating time slots
function setupAutoTimeSlotUpdate() {
    const inputs = ['start-time', 'end-time', 'break-time'];
    inputs.forEach(id => {
        document.getElementById(id)?.addEventListener('change', () => {
            if (tasks.length > 0) {
                allocateTimeSlots(tasks);
                updateTaskList();
            }
        });
    });
}

// Modify the allocateTimeSlots function
function allocateTimeSlots(tasks) {
    if (!tasks?.length) return tasks;

    try {
        const settings = {
            startTime: getElementValue('start-time', '09:00'),
            endTime: getElementValue('end-time', '17:00'),
            breakTime: parseInt(getElementValue('break-time', '0'))
        };

        const result = TimeSlotManager.allocateTimeSlots(tasks, settings);
        if (!result) return tasks;

        // Update tasks with new scheduling
        tasks = [...result.scheduled, ...result.unscheduled];
        DataManager.save('tasks', tasks);

        // Show allocation summary
        showAllocationSummary(result);
        
        // Update dashboard if it exists
        updateDashboard();
        
        return tasks;
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to allocate time slots');
        return tasks;
    }
}

// Add this to your existing JavaScript
const ProjectManager = {
    projects: [],
    currentProject: null,

    init() {
        this.projects = DataManager.load('projects', []);
        this.setupEventListeners();
        this.renderProjects();
        this.hideTaskSections();
    },

    hideTaskSections() {
        const sections = ['task-form', 'time-slot-definition', 'task-list'];
        sections.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.classList.add('hidden');
        });
    },

    setupEventListeners() {
        // Project modal events
        document.getElementById('new-project-btn')?.addEventListener('click', () => {
            const modal = document.getElementById('project-modal');
            if (modal) {
                modal.style.opacity = '0';
                modal.classList.remove('hidden');
                setTimeout(() => modal.style.opacity = '1', 10);
            }
        });

        document.getElementById('project-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.createProject();
        });

        // Close modal on outside click
        document.getElementById('project-modal')?.addEventListener('click', (e) => {
            if (e.target.id === 'project-modal') {
                this.closeProjectModal();
            }
        });
    },

    closeProjectModal() {
        const modal = document.getElementById('project-modal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('project-form')?.reset();
                document.getElementById('project-tasks').innerHTML = ''; // Clear initial tasks
            }, 300);
        }
    },

    createProject() {
        try {
            const startDate = document.getElementById('project-start-date')?.value;
            const dueDate = document.getElementById('project-due-date')?.value;
            const name = document.getElementById('project-name')?.value.trim();
            
            if (!name) throw new Error('Project name is required');
            if (!startDate || !dueDate) throw new Error('Both start and due dates are required');
            if (new Date(dueDate) < new Date(startDate)) throw new Error('Due date must be after start date');

            // Collect tasks data
            const taskElements = document.querySelectorAll('#project-tasks > div');
            const tasks = Array.from(taskElements).map(taskEl => {
                const id = taskEl.id.split('-')[1];
                return {
                    id: Date.now().toString() + Math.random().toString(36).substr(2, 5),
                    name: document.querySelector(`[name="task-name-${id}"]`).value,
                    urgency: Number(document.querySelector(`[name="task-urgency-${id}"]`).value),
                    importance: Number(document.querySelector(`[name="task-importance-${id}"]`).value),
                    effort: Number(document.querySelector(`[name="task-effort-${id}"]`).value),
                    completedTime: null,
                    scheduledTime: null,
                    createdAt: new Date().toISOString()
                };
            });

            const project = {
                id: Date.now().toString(),
                name,
                description: document.getElementById('project-description')?.value.trim() || '',
                startDate,
                dueDate,
                priority: document.getElementById('project-priority')?.value || 'medium',
                tasks: tasks,
                createdAt: new Date().toISOString()
            };

            this.projects.push(project);
            DataManager.save('projects', this.projects);
            this.renderProjects();
            this.closeProjectModal();
            this.showNotification(`Project created with ${tasks.length} tasks`, 'success');
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    },

    showNotification(message, type = 'error') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    },

    selectProject(projectId) {
        try {
            this.currentProject = this.projects.find(p => p.id === projectId);
            if (!this.currentProject) {
                throw new Error('Project not found');
            }

            // Show project header and task sections
            ['current-project-header', 'task-form', 'time-slot-definition', 'task-list'].forEach(id => {
                document.getElementById(id)?.classList.remove('hidden');
            });
            
            // Hide projects list
            document.getElementById('project-list')?.classList.add('hidden');
            
            // Update tasks list
            tasks = this.currentProject.tasks || [];
            updateTaskList();
            updatePriorityAnalysis();
            
            this.showNotification(`Managing project: ${this.currentProject.name}`, 'success');
        } catch (error) {
            this.showNotification(error.message, 'error');
        }
    },

    showProjectsList() {
        // Hide project management sections
        ['current-project-header', 'task-form', 'time-slot-definition', 'task-list'].forEach(id => {
            document.getElementById(id)?.classList.add('hidden');
        });
        
        // Show projects list
        document.getElementById('project-list')?.classList.remove('hidden');
        
        // Remove back button
        document.getElementById('back-to-projects')?.remove();
        
        this.currentProject = null;
        tasks = [];
    },

    addTaskToProject(task) {
        try {
            if (!this.currentProject) {
                throw new Error('No project selected');
            }

            // Add project reference to task
            const taskWithProject = {
                ...task,
                projectId: this.currentProject.id,
                createdAt: new Date().toISOString()
            };

            // Add to project tasks
            this.currentProject.tasks.push(taskWithProject);
            
            // Update in projects array
            const projectIndex = this.projects.findIndex(p => p.id === this.currentProject.id);
            if (projectIndex !== -1) {
                this.projects[projectIndex] = this.currentProject;
            }

            // Save to storage
            DataManager.save('projects', this.projects);
            
            // Update UI
            this.renderProjects();
            updateTaskList();
            updatePriorityAnalysis();

            return true;
        } catch (error) {
            this.showNotification(error.message, 'error');
            return false;
        }
    },

    deleteTaskFromProject(taskId) {
        try {
            if (!this.currentProject) {
                throw new Error('No project selected');
            }

            // Remove task from project
            this.currentProject.tasks = this.currentProject.tasks.filter(t => t.id !== taskId);
            
            // Update in projects array
            const projectIndex = this.projects.findIndex(p => p.id === this.currentProject.id);
            if (projectIndex !== -1) {
                this.projects[projectIndex] = this.currentProject;
            }

            // Save to storage
            DataManager.save('projects', this.projects);
            
            // Update UI
            this.renderProjects();
            tasks = this.currentProject.tasks;
            updateTaskList();
            updatePriorityAnalysis();

            return true;
        } catch (error) {
            this.showNotification(error.message, 'error');
            return false;
        }
    },

    addProjectTaskField() {
        const taskContainer = document.getElementById('project-tasks');
        const taskId = Date.now();
        
        const taskHtml = `
            <div id="task-${taskId}" class="grid grid-cols-6 gap-3 items-center bg-gray-50 p-3 rounded-lg">
                <div class="col-span-2">
                    <input type="text" placeholder="Task name" 
                        class="w-full p-2 border border-gray-300 rounded-lg" 
                        name="task-name-${taskId}" required>
                </div>
                <div class="col-span-1">
                    <select name="task-urgency-${taskId}" required
                        class="w-full p-2 border border-gray-300 rounded-lg">
                        <option value="" disabled selected>Urgency</option>
                        <option value="1">1 - Low</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5 - High</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <select name="task-importance-${taskId}" required
                        class="w-full p-2 border border-gray-300 rounded-lg">
                        <option value="" disabled selected>Importance</option>
                        <option value="1">1 - Low</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5 - High</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <input type="number" placeholder="Effort (hrs)" 
                        class="w-full p-2 border border-gray-300 rounded-lg"
                        name="task-effort-${taskId}" min="0.5" max="24" step="0.5" required>
                </div>
                <div class="col-span-1">
                    <button type="button" onclick="removeProjectTaskField('task-${taskId}')"
                        class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 w-full">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = taskHtml;
        taskContainer.appendChild(tempContainer.firstElementChild);
    },

    removeProjectTaskField(taskId) {
        const taskElement = document.getElementById(taskId);
        if (taskElement) {
            taskElement.remove();
        }
    },

    renderProjects() {
        try {
            const container = document.getElementById('project-cards');
            const projectList = document.getElementById('project-list');
            
            if (!container || !this.projects.length) {
                if (projectList) projectList.classList.add('hidden');
                return;
            }

            projectList.classList.remove('hidden');
            
            container.innerHTML = this.projects
                .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
                .map(project => {
                    const completedTasks = (project.tasks || []).filter(t => t.completedTime).length;
                    const totalTasks = (project.tasks || []).length;
                    const progress = totalTasks ? Math.round((completedTasks / totalTasks) * 100) : 0;
                    const dueDate = new Date(project.dueDate);
                    const isOverdue = dueDate < new Date() && completedTasks < totalTasks;

                    return `
                        <div class="bg-white border rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-semibold text-lg">${project.name}</h3>
                                    <p class="text-gray-600 text-sm mt-1 line-clamp-2">
                                        ${project.description || 'No description'}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full ${this.getPriorityClass(project.priority)}">
                                    ${project.priority}
                                </span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span>Progress</span>
                                    <span>${progress}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-500 rounded-full h-2 transition-all duration-500" 
                                         style="width: ${progress}%"></div>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Tasks: ${completedTasks}/${totalTasks}</span>
                                    <span class="${isOverdue ? 'text-red-500 font-medium' : ''}">
                                        ${isOverdue ? '⚠ ' : ''}Due: ${dueDate.toLocaleDateString()}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button onclick="ProjectManager.selectProject('${project.id}')" 
                                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                                    <i class="fa fa-tasks mr-2"></i>Manage Tasks
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
        } catch (error) {
            this.showNotification('Failed to render projects', 'error');
            console.error('Render projects error:', error);
        }
    },

    getPriorityClass(priority) {
        const classes = {
            low: 'bg-green-100 text-green-800',
            medium: 'bg-yellow-100 text-yellow-800',
            high: 'bg-orange-100 text-orange-800',
            critical: 'bg-red-100 text-red-800'
        };
        return classes[priority] || classes.low;
    },

    viewProject(projectId) {
        try {
            const project = this.projects.find(p => p.id === projectId);
            if (!project) {
                throw new Error('Project not found');
            }
            this.selectProject(projectId);
        } catch (error) {
            this.showNotification('Failed to open project', 'error');
            console.error(error);
        }
    }
};

// Update the viewProject function to use ProjectManager
function viewProject(projectId) {
    ProjectManager.selectProject(projectId);
}

// Update the task form submission handler
document.getElementById('task-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    try {
        if (!ProjectManager.currentProject) {
            throw new Error('Please select a project first');
        }

        const taskData = {
            name: getElementValue('task-name').trim(),
            urgency: Number(getElementValue('urgency')),
            importance: Number(getElementValue('importance')),
            effort: Number(getElementValue('effort')),
            id: Date.now().toString(),
            completedTime: null,
            scheduledTime: null
        };

        // Validate task data
        if (!taskData.name) throw new Error('Task name is required');
        if (!taskData.urgency) throw new Error('Urgency is required');
        if (!taskData.importance) throw new Error('Importance is required');
        if (!taskData.effort || taskData.effort <= 0) throw new Error('Valid effort value is required');

        // Add task to current project
        if (ProjectManager.addTaskToProject(taskData)) {
            // Reset form
            this.reset();
            
            // Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            successMessage.textContent = 'Task added successfully';
            document.body.appendChild(successMessage);
            setTimeout(() => successMessage.remove(), 3000);

            // Update time slots
            if (tasks.length > 0) {
                allocateTimeSlots(tasks);
            }
        }
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to add task');
    }
});

// Add project view function
function viewProject(projectId) {
    try {
        const project = ProjectManager.projects.find(p => p.id === projectId);
        if (!project) {
            throw new Error('Project not found');
        }
        window.location.href = `tasks.php?id=${projectId}`;
    } catch (error) {
        ErrorHandler.show('Failed to open project');
        console.error(error);
    }
}

// Update the existing task form submission to include project association
let currentProject = null;

// Modify the existing addTask function
const originalAddTask = addTask;
addTask = function(taskData) {
    if (!currentProject) {
        ErrorHandler.show('Please select a project first');
        return false;
    }
    
    const result = originalAddTask(taskData);
    if (result) {
        currentProject.tasks.push(taskData);
        DataManager.save('projects', ProjectManager.projects);
        ProjectManager.renderProjects();
    }
    return result;
};

const TimeUtil = {
    toDecimal(timeString) {
        const [hours, minutes] = timeString.split(':').map(Number);
        return hours + minutes / 60;
    }
};

// Initialize project manager
document.addEventListener('DOMContentLoaded', () => {
    try {
        ProjectManager.init();
        // Hide task management sections initially
        document.getElementById('task-form')?.classList.add('hidden');
        document.getElementById('time-slot-definition')?.classList.add('hidden');
        document.getElementById('task-list')?.classList.add('hidden');
    } catch (error) {
        console.error('Initialization failed:', error);
    }
});

// Update deleteTask function to use ProjectManager
function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        ProjectManager.deleteTaskFromProject(taskId);
    }
}

// Add these helper functions
function addProjectTaskField() {
    ProjectManager.addProjectTaskField();
}

function removeProjectTaskField(taskId) {
    ProjectManager.removeProjectTaskField(taskId);
}

// Update the project form event listener
document.getElementById('project-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    ProjectManager.createProject();
});

// Remove standalone viewProject function and use ProjectManager.viewProject instead
// Update button onclick handlers to use ProjectManager.viewProject
function updateProjectButtons() {
    document.querySelectorAll('[onclick*="viewProject"]').forEach(button => {
        const projectId = button.getAttribute('onclick').match(/'([^']+)'/)[1];
        button.setAttribute('onclick', `ProjectManager.viewProject('${projectId}')`);
    });
}

// Remove duplicate task management code and consolidate into TaskManager
const TaskManager = {
    addTask(taskData, projectId) {
        if (!projectId) {
            ErrorHandler.show('Please select a project first');
            return false;
        }

        const project = ProjectManager.projects.find(p => p.id === projectId);
        if (!project) {
            ErrorHandler.show('Project not found');
            return false;
        }

        const newTask = {
            ...taskData,
            projectId,
            id: Date.now().toString(),
            completedTime: null,
            scheduledTime: null,
            createdAt: new Date().toISOString()
        };

        project.tasks.push(newTask);
        DataManager.save('projects', ProjectManager.projects);
        ProjectManager.renderProjects();
        
        // Update global tasks array
        if (ProjectManager.currentProject?.id === projectId) {
            tasks = project.tasks;
            updateTaskList();
            updatePriorityAnalysis();
        }

        return true;
    },

    deleteTask(taskId) {
        if (!ProjectManager.currentProject) return false;

        ProjectManager.currentProject.tasks = ProjectManager.currentProject.tasks.filter(t => t.id !== taskId);
        tasks = ProjectManager.currentProject.tasks;
        
        DataManager.save('projects', ProjectManager.projects);
        ProjectManager.renderProjects();
        updateTaskList();
        updatePriorityAnalysis();
        
        return true;
    },

    completeTask(taskId) {
        if (!ProjectManager.currentProject) return false;

        const task = ProjectManager.currentProject.tasks.find(t => t.id === taskId);
        if (task) {
            task.completedTime = new Date().toISOString();
            DataManager.save('projects', ProjectManager.projects);
            ProjectManager.renderProjects();
            updateTaskList();
            updatePriorityAnalysis();
            return true;
        }
        return false;
    }
};

// Update event handlers
document.addEventListener('DOMContentLoaded', () => {
    try {
        ProjectManager.init();
        setupEventHandlers();
    } catch (error) {
        console.error('Initialization failed:', error);
    }
});

function setupEventHandlers() {
    // Task form submission
    document.getElementById('task-form')?.addEventListener('submit', handleTaskSubmission);
    
    // Project form submission
    document.getElementById('project-form')?.addEventListener('submit', (e) => {
        e.preventDefault();
        ProjectManager.createProject();
    });
}

function handleTaskSubmission(e) {
    e.preventDefault();
    try {
        if (!ProjectManager.currentProject) {
            throw new Error('Please select a project first');
        }

        const taskData = {
            name: getElementValue('task-name').trim(),
            urgency: Number(getElementValue('urgency')),
            importance: Number(getElementValue('importance')),
            effort: Number(getElementValue('effort'))
        };

        // Validate and add task
        if (TaskManager.addTask(taskData, ProjectManager.currentProject.id)) {
            this.reset();
            // Show success notification
            showSuccessMessage('Task added successfully');
        }
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to add task');
    }
}

// Helper functions
function showSuccessMessage(message) {
    const successMessage = document.createElement('div');
    successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successMessage.textContent = message;
    document.body.appendChild(successMessage);
    setTimeout(() => successMessage.remove(), 3000);
}

// Update task action handlers
function completeTask(taskId) {
    TaskManager.completeTask(taskId);
}

function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        TaskManager.deleteTask(taskId);
    }
}
    </script>
</body>
</html>

