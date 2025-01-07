

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div id="dashboard-section" class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900 mb-4">Dashboard Overview</h2>
            <div id="dashboard-loading" class="hidden">
                <p class="text-center text-gray-600">Loading dashboard...</p>
            </div>
            <div id="dashboard-content" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-blue-100 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800">Task Status</h3>
                    <div class="h-64">
                        <canvas id="task-status-chart"></canvas>
                    </div>
                </div>
                <div class="bg-green-100 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800">Priority Distribution</h3>
                    <div class="h-64">
                        <canvas id="task-priority-chart"></canvas>
                    </div>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800">Effort Distribution</h3>
                    <div class="h-64">
                        <canvas id="task-effort-chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Additional Dashboard Statistics -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                    <h4 class="text-lg font-semibold text-gray-700">Total Tasks</h4>
                    <p id="total-tasks" class="text-3xl font-bold text-blue-600">0</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-green-500">
                    <h4 class="text-lg font-semibold text-gray-700">Completed Tasks</h4>
                    <p id="completed-tasks" class="text-3xl font-bold text-green-600">0</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-yellow-500">
                    <h4 class="text-lg font-semibold text-gray-700">Average Effort</h4>
                    <p id="average-effort" class="text-3xl font-bold text-yellow-600">0h</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-red-500">
                    <h4 class="text-lg font-semibold text-gray-700">High Priority</h4>
                    <p id="high-priority-tasks" class="text-3xl font-bold text-red-600">0</p>
                </div>
            </div>
        </div>
        
        <!-- Back to Tasks button -->
        <div class="mt-6 text-center">
            <a href="index.php" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                Back to Tasks
            </a>
        </div>
    </div>

    <script>
        // Dashboard initialization
        document.addEventListener('DOMContentLoaded', function() {
            initializeDashboard();
        });

        function initializeDashboard() {
            try {
                const loadingEl = document.getElementById('dashboard-loading');
                const contentEl = document.getElementById('dashboard-content');
                
                if (loadingEl && contentEl) {
                    // Show loading
                    loadingEl.classList.remove('hidden');
                    contentEl.classList.add('hidden');
                    
                    // Load tasks from localStorage
                    const tasks = JSON.parse(localStorage.getItem('tasks') || '[]');
                    updateDashboardStats(tasks);
                    initializeCharts(tasks);
                    
                    // Hide loading
                    loadingEl.classList.add('hidden');
                    contentEl.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Dashboard initialization failed:', error);
                // Show error state if loading element exists
                const loadingEl = document.getElementById('dashboard-loading');
                if (loadingEl) {
                    loadingEl.innerHTML = '<p class="text-red-500">Failed to load dashboard</p>';
                    loadingEl.classList.remove('hidden');
                }
            }
        }

        function updateDashboardStats(tasks) {
            document.getElementById('total-tasks').textContent = tasks.length;
            document.getElementById('completed-tasks').textContent = tasks.filter(t => t.completedTime).length;
            
            const avgEffort = tasks.length > 0 
                ? (tasks.reduce((sum, t) => sum + (t.effort || 0), 0) / tasks.length).toFixed(1) 
                : 0;
            document.getElementById('average-effort').textContent = `${avgEffort}h`;
            
            const highPriorityCount = tasks.filter(t => {
                const score = calculatePriorityScore(t);
                return score >= 8 && !t.completedTime;
            }).length;
            document.getElementById('high-priority-tasks').textContent = highPriorityCount;
        }

        function initializeCharts(tasks) {
            createTaskStatusChart(tasks);
            createPriorityChart(tasks);
            createEffortChart(tasks);
        }

        function createTaskStatusChart(tasks) {
            const ctx = document.getElementById('task-status-chart').getContext('2d');
            const pending = tasks.filter(t => !t.completedTime).length;
            const completed = tasks.filter(t => t.completedTime).length;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Completed'],
                    datasets: [{
                        data: [pending, completed],
                        backgroundColor: ['#4299E1', '#48BB78']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function createPriorityChart(tasks) {
            const ctx = document.getElementById('task-priority-chart').getContext('2d');
            const priorities = {
                Critical: 0,
                High: 0,
                Medium: 0,
                Low: 0
            };

            tasks.forEach(task => {
                if (task.completedTime) return;
                const score = calculatePriorityScore(task);
                if (score >= 8) priorities.Critical++;
                else if (score >= 6) priorities.High++;
                else if (score >= 4) priorities.Medium++;
                else priorities.Low++;
            });

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(priorities),
                    datasets: [{
                        label: 'Tasks by Priority',
                        data: Object.values(priorities),
                        backgroundColor: ['#FC8181', '#F6AD55', '#F6E05E', '#68D391']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        function createEffortChart(tasks) {
            const ctx = document.getElementById('task-effort-chart').getContext('2d');
            const effortRanges = {
                'Low (0-2h)': tasks.filter(t => t.effort <= 2).length,
                'Medium (2-4h)': tasks.filter(t => t.effort > 2 && t.effort <= 4).length,
                'High (4-8h)': tasks.filter(t => t.effort > 4 && t.effort <= 8).length,
                'Very High (>8h)': tasks.filter(t => t.effort > 8).length
            };

            new Chart(ctx, {
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
                    maintainAspectRatio: false
                }
            });
        }

        // Priority score calculation function
        function calculatePriorityScore(task) {
            if (!task.urgency || !task.importance || !task.effort) return 0;
            
            const urgencyWeight = 0.45;
            const importanceWeight = 0.45;
            const effortWeight = 0.10;
            
            const normalizedEffort = task.effort <= 2 ? 1 :
                                    task.effort <= 4 ? 2 :
                                    task.effort <= 6 ? 3 :
                                    task.effort <= 8 ? 4 : 5;
            
            const baseScore = (
                (urgencyWeight * task.urgency) +
                (importanceWeight * task.importance) -
                (effortWeight * normalizedEffort)
            ) * 2;

            return Math.min(10, Math.max(0, baseScore)).toFixed(2);
        }
    </script>
</body>
</html>

<?php require_once 'footer.php'; ?>
