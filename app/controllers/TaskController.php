<?php

class TaskController extends Controller {

    use JsonPersistence;
    protected string $taskFilePath = ROOT_PATH . '/app/models/data/tasks.json';
    protected string $programmerFilePath = ROOT_PATH . '/app/models/data/programmers.json';
    protected string $projectFilePath = ROOT_PATH . '/app/models/data/projects.json';

    public function createAction() {
        $error = null;

        // Ensure that the form data has been submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //var_dump($_POST);
            $taskKind = $_POST['task_kind'];
            $taskDescription = $_POST['task_description'] ?? null;
            $programmerSkills = $this->getProgrammerSkillsById($_POST['programmer_id']);
            if ($taskDescription === null || trim($taskDescription) === '') {
                $error = "Task description is required.";
            }
            if (empty($programmerSkills) || !in_array($taskKind, $programmerSkills)) {
                $error = "The selected developer doesn't have the required skills to undertake the Task";
            }

            if(!isset($error)) {
                // Collect the data from the form
                $task = new Task($_POST['project_id'], $_POST['programmer_id'], $_POST['task_kind'], $_POST['task_description'] ?? '');
                $created_task = $this->storeCreatedTask($task);
                $this->view->created_task = $created_task;
            }
        }

        if ($error) {$this->view->error = $error;}
        $projects = $this->loadData($this->projectFilePath);
        $programmers = $this->loadData($this->programmerFilePath);
        //$kinds = Task::const ALLOWED_KINDS;
        $this->view->title = "CRUD Task Create";
        $this->view->projects = $projects;
        //$this->view->kinds = $kinds;
        $this->view->programmers = $programmers;
    }


    // HELPERS

    public function storeCreatedTask(Task $task) : array { // This funtion is to CREATE in json file
        $taskData = $task->toArray();
        $allTasks = $this->loadData($this->taskFilePath);
        $allTasks[] = $taskData;
        $this->saveData($allTasks, $this->taskFilePath);
        return $taskData;
    }


    public function getTaskById(int $id_task) {
        $tasks = $this->loadData($this->taskFilePath);
        foreach ($tasks as $task) {
            if ($task['id_task'] === $id_task) {
                return $task;
            }
        }
        return null;  // Task not found
    }

    public function getProjectById(int $id_project) : array {
        $projects = $this->loadData($this->projectFilePath);
        foreach($projects as $project) {
            if($project['id_project'] === $id_project) {
                return $project;
            }
        }
        return null; // Project not found
    }

    public function getProgrammerById(int $programmer_id) {
        $programmers = $this->loadData($this->programmerFilePath);
        foreach ($programmers as $programmer) {
            if ($programmer['id_programmer'] === $programmer_id) {
                return $programmer;
            }
        }
        return null; // Programmer not found
    }

    public function getProgrammerSkillsById(int $programmer_id) : array {
        $programmer = $this->getProgrammerById($programmer_id);
        return $programmer['skills'] ?? [];
    }

    public function showAction() {

        $tasks = $this->loadData($this->taskFilePath);
        //$selected_task = [];
        $selected_task = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_task'])) {
            
            $id_task = (int) $_POST['id_task'];
            $tasks = $this->loadData($this->taskFilePath);

            foreach ($tasks as $task) {
                if ($task['id_task'] === $id_task) {
                    $selected_task = $task;
                }
            }
        }
        // Render the show task view
        $this->view->title = 'CRUD Task Show';
        $this->view->selected_task = $selected_task;
        $this->view->tasks = $tasks;
        //$this->view->render('crudtask/show.php');
    }
    
    /*
    public function updateAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_task'], $_POST['assigned_programmer'])) {
            $taskId = (int)$_POST['id_task'];
            $updatedData = [
                'programmer_id' => $_POST['assigned_programmer']
            ];
    
            $taskManager = TaskManager::getInstance();
            $success = $taskManager->updateTask($taskId, $updatedData);
    
            if ($success) {
                $task = $taskManager->getTaskById($taskId);
                $_SESSION['selected_task'] = $task;
                echo "Task updated successfully!";
            } else {
                echo "Failed to update the task.";
            }
        }
        $this->view->render('crudtask/update.php');
    }

    public function upgradeProgressAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_task'])) {
            $taskId = (int)$_POST['id_task'];
    
            $taskManager = TaskManager::getInstance();
            $taskArray = $taskManager->getTaskById($taskId); // Retrieves task as an array
    
            // Convert array to Task object locally
            $task = $this->convertArrayToTask($taskArray);
    
            $now = new DateTime();
    
            // Use Task methods to progress status
            switch ($task->getTaskStatus()) {
                case TaskStatus::RELEASED:
                    $message = '<p class="rajdhani-light" style="color: green; margin-left: 10px;">Task progress cannot be advanced, it has already been RELEASED!</p>';
                    break; // Final stage
                case TaskStatus::DELIVERED:
                    $task->setDateApproved($now);
                    $message = '<p class="rajdhani-light" style="color: green; margin-left: 10px;">Task progress advanced successfully to RELEASED!</p>';
                    break;
                case TaskStatus::INIT:
                    $task->setDateDelivered($now);
                    $message = '<p class="rajdhani-light" style="color: green; margin-left: 10px;">Task progress advanced successfully to DELIVERED!</p>';
                    break;
                case TaskStatus::PIPELINED:
                    $task->setDateInit($now);
                    $message = '<p class="rajdhani-light" style="color: green; margin-left: 10px;">Task progress advanced successfully to INITIATED!</p>';
                    break;
            }
    
            // Save the updated task
            $taskManager->updateTask($taskId, $task->toArray());
    
            // Update session with the modified task
            $_SESSION['selected_task'] = $task->toArray();
    
            // Feedback to the user
            echo $message;
        }
    
        $this->view->render('crudtask/update.php');
    }


    // Helper for upgradeProgressAction
    private function convertArrayToTask(array $taskArray): Task {
        $task = new Task(
            (int)$taskArray['project_id'],
            (int)$taskArray['programmer_id'],
            TaskKind::from($taskArray['task_kind']),
            $taskArray['task_description']
        );
        $task->setIdTask($taskArray['id_task']);
    
        if (!empty($taskArray['dateInit'])) {
            $task->setDateInit(new DateTime($taskArray['dateInit']));
        }
        if (!empty($taskArray['dateDelivered'])) {
            $task->setDateDelivered(new DateTime($taskArray['dateDelivered']));
        }
        if (!empty($taskArray['dateApproved'])) {
            $task->setDateApproved(new DateTime($taskArray['dateApproved']));
        }
    
        return $task;
    }
    */

    public function deleteAction() {
        $task = null;
        $message = "";
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = (int)($_POST['id_task'] ?? 0);
            // Check if confirmation exists
            if (!empty($_POST['confirmation'])) {
                $confirmation = strtolower(trim($_POST['confirmation']));
    
                // Process deletion only if confirmation matches
                if ($confirmation === 'delete') {
                    $tasks = $this->loadData($this->taskFilePath);
                    // Delete selected task and reindex the array
                    $tasksAfterDeletion = array_values(array_filter($tasks, fn($task) => $task['id_task'] !== $taskId));
                    $success = $this->saveData($tasksAfterDeletion, $this->taskFilePath);
    
                    if ($success) {
                        $message = '<p class="rajdhani-light" style="color: green; margin-left: 10px;">Task deleted successfully!</p>';
                    } else {
                        $message = '<p class="rajdhani-light" style="color: red; margin-left: 10px;">Task deletion failed!</p>';
                    }
                    $this->view->selected_task = null;
                } else {
                    $message = '<p class="rajdhani-light" style="color: red; margin-left: 10px;">Task deletion failed! Please confirm typing "DELETE".</p>';
                }
            }
    
            // Retrieve the task for confirmation in the delete view
            $task = $this->getTaskById($taskId);
        }
    
        // Always render the delete form
        $this->view->title = 'CRUD Task Delete';
        $this->view->message = $message;
        $this->view->selected_task = $task ?? [];
    }
}
?>