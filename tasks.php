<?php
// Project context handling
session_start();

// Handle project loading
function loadProject($projectId) {
    $projectsJson = file_get_contents('projects.json');
    if ($projectsJson === false) {
        return null;
    }
    
    $projects = json_decode($projectsJson, true);
    return isset($projects[$projectId]) ? $projects[$projectId] : null;
}

// Validate project ID
$projectId = $_GET['id'] ?? null;
if (!$projectId) {
    header('Location: index.php');
    exit;
}

$project = loadProject($projectId);
if (!$project) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['name']); ?> - Tasks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        #task-form {
            transition: opacity 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Project Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($project['name']); ?></h1>
                    <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($project['description']); ?></p>
                </div>
                <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    <i class="fa fa-arrow-left mr-2"></i>Back to Projects
                </a>
            </div>
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-3 rounded">
                    <div class="text-sm text-gray-500">Start Date</div>
                    <div class="font-medium"><?php echo date('M j, Y', strtotime($project['startDate'])); ?></div>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <div class="text-sm text-gray-500">Due Date</div>
                    <div class="font-medium"><?php echo date('M j, Y', strtotime($project['dueDate'])); ?></div>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <div class="text-sm text-gray-500">Priority</div>
                    <div class="font-medium capitalize"><?php echo $project['priority']; ?></div>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <div class="text-sm text-gray-500">Tasks</div>
                    <div class="font-medium"><?php echo count($project['tasks'] ?? []); ?> total</div>
                </div>
            </div>
        </div>

        <!-- Task Management -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Task List -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Tasks</h2>
                <div id="task-list" class="space-y-3">
                    <?php if (!empty($project['tasks'])): ?>
                        <?php foreach ($project['tasks'] as $task): ?>
                            <div class="task-item border rounded p-3 <?php echo $task['completedTime'] ? 'bg-gray-50' : ''; ?>">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium <?php echo $task['completedTime'] ? 'line-through text-gray-500' : ''; ?>">
                                            <?php echo htmlspecialchars($task['name']); ?>
                                        </h3>
                                        <div class="text-sm text-gray-500 mt-1">
                                            Urgency: <?php echo $task['urgency']; ?>/5 | 
                                            Importance: <?php echo $task['importance']; ?>/5 | 
                                            Effort: <?php echo $task['effort']; ?>h
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <?php if (!$task['completedTime']): ?>
                                            <button onclick="completeTask('<?php echo $task['id']; ?>')" 
                                                class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button onclick="deleteTask('<?php echo $task['id']; ?>')"
                                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-gray-500 text-center py-4">
                            No tasks found
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Task Form -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Add New Task</h2>
                <form id="task-form" class="space-y-4">
                    <input type="hidden" name="projectId" value="<?php echo $projectId; ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Name</label>
                        <input type="text" name="name" required class="w-full p-2 border rounded">
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urgency</label>
                            <select name="urgency" required class="w-full p-2 border rounded">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> - <?php echo $i === 1 ? 'Low' : ($i === 5 ? 'High' : ''); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Importance</label>
                            <select name="importance" required class="w-full p-2 border rounded">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> - <?php echo $i === 1 ? 'Low' : ($i === 5 ? 'High' : ''); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Effort (hrs)</label>
                            <input type="number" name="effort" min="0.5" max="24" step="0.5" required 
                                class="w-full p-2 border rounded">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                        Add Task
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Task management JavaScript
    document.getElementById('task-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch('api/tasks.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) throw new Error('Failed to add task');
            
            const result = await response.json();
            if (result.success) {
                form.reset();
                location.reload();
            }
        } catch (error) {
            console.error('Failed to add task:', error);
            alert('Failed to add task. Please try again.');
        }
    });

    async function completeTask(taskId) {
        try {
            const response = await fetch('api/tasks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'complete',
                    taskId,
                    projectId: '<?php echo $projectId; ?>'
                })
            });
            
            if (!response.ok) throw new Error('Failed to complete task');
            
            location.reload();
        } catch (error) {
            console.error('Failed to complete task:', error);
            alert('Failed to complete task. Please try again.');
        }
    }

    async function deleteTask(taskId) {
        if (!confirm('Are you sure you want to delete this task?')) return;
        
        try {
            const response = await fetch('api/tasks.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    taskId,
                    projectId: '<?php echo $projectId; ?>'
                })
            });
            
            if (!response.ok) throw new Error('Failed to delete task');
            
            location.reload();
        } catch (error) {
            console.error('Failed to delete task:', error);
            alert('Failed to delete task. Please try again.');
        }
    }
    </script>
</body>
</html>
