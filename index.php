<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-900 font-sans">
    <div class="container mx-auto p-2 sm:p-6">
        <!-- Navigation -->
       

        <!-- Main Content -->
        <div class="space-y-6 max-w-4xl mx-auto">
            <!-- Task Form -->
            <form id="task-form" class="bg-white p-6 rounded-lg shadow-lg w-full">
                <div class="grid grid-cols-1 sm:grid-cols-6 gap-4 items-center">
                    <!-- Task Name -->
                    <div class="col-span-2">
                        <label for="task-name" class="block text-sm font-medium text-gray-700 mb-1">Task Name</label>
                        <input type="text" id="task-name" placeholder="Enter task name" required
                            class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <!-- Urgency -->
                    <div class="col-span-1">
                        <label for="urgency" class="block text-sm font-medium text-gray-700 mb-1">Urgency</label>
                        <select id="urgency" required class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Select</option>
                            <option value="1">1 - Low</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5 - High</option>
                        </select>
                    </div>
                    <!-- Importance -->
                    <div class="col-span-1">
                        <label for="importance" class="block text-sm font-medium text-gray-700 mb-1">Importance</label>
                        <select id="importance" required class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Select</option>
                            <option value="1">1 - Low</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5 - High</option>
                        </select>
                    </div>
                    <!-- Effort -->
                    <div class="col-span-1">
                        <label for="effort" class="block text-sm font-medium text-gray-700 mb-1">Effort (hrs)</label>
                        <input type="number" id="effort" placeholder="0.5 - 24 hrs" min="0.0" max="24" step="0.5" required
                            class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <!-- Submit Button -->
                    <div class="col-span-1">
                        <button type="submit" id="submit-button" class="mt-5 sm:mt-0 w-full bg-blue-500 text-white p-3 rounded-lg shadow-md hover:bg-blue-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Add Task
                        </button>
                    </div>
                </div>
            </form>

            <!-- Time Slot Form -->
            <div id="time-slot-definition" class="bg-white p-6 rounded-lg shadow-lg">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label for="start-time" class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input type="time" id="start-time" required
                            class="w-full p-3 border border-gray-300 rounded-lg" onchange="saveTimeSlotSettings()">
                    </div>
                    <div>
                        <label for="end-time" class="block text-sm font-medium text-gray-700">End Time</label>
                        <input type="time" id="end-time" required
                            class="w-full p-3 border border-gray-300 rounded-lg" onchange="saveTimeSlotSettings()">
                    </div>
                    <div>
                        <label for="break-time" class="block text-sm font-medium text-gray-700">Break (min)</label>
                        <input type="number" id="break-time" min="0"
                            class="w-full p-3 border border-gray-300 rounded-lg" onchange="saveTimeSlotSettings()">
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
                <!-- Remove priority-analysis div and directly show task list -->
                <div id="task-list-content" class="mt-4">
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
        // Core utilities - Keep only essential error handling
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

// Data management - Simplified
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

// Task Management - Simplified
let tasks = [];

// Core functions - Essential only
function addTask(taskData) {
    try {
        const newTask = {
            ...taskData,
            id: Date.now().toString(),
            completedTime: null,
            scheduledTime: null
        };

        tasks.push(newTask);
        const result = TimeSlotManager.allocateTimeSlots(tasks, {
            startTime: getElementValue('start-time', '09:00'),
            endTime: getElementValue('end-time', '17:00'),
            breakTime: parseInt(getElementValue('break-time', '0'))
        });

        if (result) {
            tasks = [...result.scheduled, ...result.unscheduled];
            DataManager.save('tasks', tasks);
        }
        
        updateTaskList();
        return true;
    } catch (error) {
        ErrorHandler.handle(error, 'Task addition failed');
        return false;
    }
}

// Time management - Essential functionality
const TimeUtil = {
    toDecimal(timeString) {
        try {
            if (!timeString || typeof timeString !== 'string') return null;
            const [hours, minutes] = timeString.split(':').map(Number);
            if (isNaN(hours) || isNaN(minutes)) return null;
            return hours + (minutes / 60);
        } catch (error) {
            return null;
        }
    },

    fromDecimal(decimal) {
        try {
            if (typeof decimal !== 'number' || isNaN(decimal)) return null;
            const hours = Math.floor(decimal);
            const minutes = Math.round((decimal - hours) * 60);
            return {
                hours: Math.max(0, Math.min(23, hours)),
                minutes: Math.max(0, Math.min(59, minutes)),
                formatted: `${String(Math.max(0, Math.min(23, hours))).padStart(2, '0')}:${String(Math.max(0, Math.min(59, minutes))).padStart(2, '0')}`
            };
        } catch (error) {
            return null;
        }
    },

    formatTimeRange(start, end) {
        const startTime = this.fromDecimal(start);
        const endTime = this.fromDecimal(end);
        if (!startTime || !endTime) return 'Invalid time';
        return `${this.to12Hour(startTime.formatted)} - ${this.to12Hour(endTime.formatted)}`;
    },

    to12Hour(time24) {
        try {
            const [hours, minutes] = time24.split(':');
            const hour = parseInt(hours);
            const period = hour >= 12 ? 'PM' : 'AM';
            const hour12 = hour % 12 || 12;
            return `${hour12}:${minutes} ${period}`;
        } catch (error) {
            return time24;
        }
    }
};

// Add missing calculatePriorityScore function
function calculatePriorityScore(task) {
    try {
        if (!task?.urgency || !task?.importance || !task?.effort) return 0;
        
        const urgencyWeight = 0.45;
        const importanceWeight = 0.45;
        const effortWeight = 0.10;
        
        const normalizedEffort = task.effort <= 2 ? 1 :
                               task.effort <= 4 ? 2 :
                               task.effort <= 6 ? 3 :
                               task.effort <= 8 ? 4 : 5;
        
        const score = (
            (urgencyWeight * task.urgency) +
            (importanceWeight * task.importance) - 
            (effortWeight * normalizedEffort)
        ) * 2;
        
        return Math.min(10, Math.max(0, score)).toFixed(2);
    } catch (error) {
        return 0;
    }
}

// Update TimeSlotManager with validation
const TimeSlotManager = {
    validate(settings) {
        if (!settings?.startTime || !settings?.endTime) return false;
        const start = TimeUtil.toDecimal(settings.startTime);
        const end = TimeUtil.toDecimal(settings.endTime);
        return start !== null && end !== null && start < end;
    },

    allocateTimeSlots(tasks, settings) {
        try {
            if (!Array.isArray(tasks) || !this.validate(settings)) {
                return null;
            }

            const start = TimeUtil.toDecimal(settings.startTime);
            const end = TimeUtil.toDecimal(settings.endTime);
            const breakInHours = Math.max(0, (settings.breakTime || 0)) / 60;
            let currentTime = start;
            
            const scheduledTasks = [];
            const unscheduledTasks = [];

            // Sort by priority
            const sortedTasks = [...tasks].sort((a, b) => {
                if (a.completedTime && !b.completedTime) return 1;
                if (!a.completedTime && b.completedTime) return -1;
                return calculatePriorityScore(b) - calculatePriorityScore(a);
            });

            for (const task of sortedTasks) {
                if (task.completedTime) {
                    scheduledTasks.push(task);
                    continue;
                }

                if (!task.effort || task.effort <= 0) {
                    unscheduledTasks.push({ ...task, scheduledTime: "Invalid effort" });
                    continue;
                }

                const taskEndTime = currentTime + task.effort;
                if (taskEndTime <= end) {
                    scheduledTasks.push({
                        ...task,
                        scheduledTime: TimeUtil.formatTimeRange(currentTime, taskEndTime),
                        startTime: currentTime,
                        endTime: taskEndTime
                    });
                    currentTime = taskEndTime + breakInHours;
                } else {
                    unscheduledTasks.push({ ...task, scheduledTime: "Exceeds available time" });
                }
            }

            return { scheduled: scheduledTasks, unscheduled: unscheduledTasks };
        } catch (error) {
            ErrorHandler.handle(error, 'Scheduling failed');
            return null;
        }
    }
};

// Update form submission handler with validation
document.getElementById('task-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    try {
        const taskName = document.getElementById('task-name')?.value?.trim();
        const urgency = Number(document.getElementById('urgency')?.value);
        const importance = Number(document.getElementById('importance')?.value);
        const effort = Number(document.getElementById('effort')?.value);
        
        if (!taskName) throw new Error('Task name is required');
        if (urgency < 1 || urgency > 5) throw new Error('Invalid urgency value');
        if (importance < 1 || importance > 5) throw new Error('Invalid importance value');
        if (effort <= 0 || effort > 24) throw new Error('Effort must be between 0 and 24 hours');

        const taskData = { name: taskName, urgency, importance, effort };
        
        if (addTask(taskData)) {
            this.reset();
            updateTaskList();
        }
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to add task');
    }
});

// Add these functions before the DOMContentLoaded event listener
function saveTimeSlotSettings() {
    const settings = {
        startTime: document.getElementById('start-time').value,
        endTime: document.getElementById('end-time').value,
        breakTime: document.getElementById('break-time').value
    };
    DataManager.save('timeSlotSettings', settings);
    
    // Reallocate tasks with new settings
    const result = TimeSlotManager.allocateTimeSlots(tasks, {
        startTime: settings.startTime,
        endTime: settings.endTime,
        breakTime: parseInt(settings.breakTime)
    });
    
    if (result) {
        tasks = [...result.scheduled, ...result.unscheduled];
        DataManager.save('tasks', tasks);
        updateTaskList();
    }
}

function loadTimeSlotSettings() {
    const defaultSettings = {
        startTime: '09:00',
        endTime: '17:00',
        breakTime: '0'
    };
    
    const settings = DataManager.load('timeSlotSettings', defaultSettings);
    
    document.getElementById('start-time').value = settings.startTime;
    document.getElementById('end-time').value = settings.endTime;
    document.getElementById('break-time').value = settings.breakTime;
}

// Modify the DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', function() {
    loadTimeSlotSettings();
    tasks = DataManager.load('tasks', []);
    updateTaskList();
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    tasks = DataManager.load('tasks', []);
    updateTaskList();
});

// Helper functions - Keep only essentials
function getElementValue(id, defaultValue = '') {
    return document.getElementById(id)?.value || defaultValue;
}

// Add the missing updateTaskList implementation
function updateTaskList() {
    try {
        const taskListContent = document.getElementById('task-list-content');
        if (!taskListContent) return;

        taskListContent.innerHTML = `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metrics</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${tasks.length ? '' : `
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <i class="fa fa-tasks fa-2x mb-2"></i>
                                    <p>No tasks found</p>
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>
            </div>
        `;

        const tbody = taskListContent.querySelector('tbody');
        tasks.forEach(task => {
            const priorityScore = calculatePriorityScore(task);
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 ${task.completedTime ? 'line-through' : ''}">
                        ${task.name}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm ${getPriorityClass(priorityScore)}">
                        ${getPriorityLabel(priorityScore)}
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
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex space-x-2">
                        ${!task.completedTime ? `
                            <button onclick="completeTask('${task.id}')"
                                class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                <i class="fa fa-check"></i>
                            </button>
                        ` : `
                            <span class="text-green-600">
                                <i class="fa fa-check-circle"></i>
                            </span>
                        `}
                        <button onclick="deleteTask('${task.id}')"
                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to update task list');
    }
}

// Add missing utility functions
function getPriorityClass(score) {
    const numScore = parseFloat(score);
    if (numScore >= 8) return 'text-red-600 font-bold';
    if (numScore >= 6) return 'text-orange-500 font-bold';
    if (numScore >= 4) return 'text-yellow-600';
    return 'text-green-600';
}

function getPriorityLabel(score) {
    const numScore = parseFloat(score);
    if (numScore >= 8) return 'Critical';
    if (numScore >= 6) return 'High';
    if (numScore >= 4) return 'Medium';
    return 'Low';
}

function completeTask(taskId) {
    try {
        const task = tasks.find(t => t.id === taskId);
        if (task) {
            task.completedTime = new Date().toISOString();
            DataManager.save('tasks', tasks);
            const result = TimeSlotManager.allocateTimeSlots(tasks, {
                startTime: getElementValue('start-time', '09:00'),
                endTime: getElementValue('end-time', '17:00'),
                breakTime: parseInt(getElementValue('break-time', '0'))
            });
            if (result) {
                tasks = [...result.scheduled, ...result.unscheduled];
                DataManager.save('tasks', tasks);
            }
            updateTaskList();
        }
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to complete task');
    }
}

function deleteTask(taskId) {
    try {
        tasks = tasks.filter(t => t.id !== taskId);
        DataManager.save('tasks', tasks);
        const result = TimeSlotManager.allocateTimeSlots(tasks, {
            startTime: getElementValue('start-time', '09:00'),
            endTime: getElementValue('end-time', '17:00'),
            breakTime: parseInt(getElementValue('break-time', '0'))
        });
        if (result) {
            tasks = [...result.scheduled, ...result.unscheduled];
            DataManager.save('tasks', tasks);
        }
        updateTaskList();
    } catch (error) {
        ErrorHandler.handle(error, 'Failed to delete task');
    }
}

function clearAllTasks() {
    if (confirm('Are you sure you want to clear all tasks?')) {
        tasks = [];
        DataManager.save('tasks', tasks);
        updateTaskList();
    }
}

// ...rest of essential utility functions...
    </script>
</body>
</html>

