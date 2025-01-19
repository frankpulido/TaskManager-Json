<?php

class TaskController extends Controller {

    use JsonPersistence;
    protected string $taskFilePath = ROOT_PATH . '/app/models/data/tasks.json';
    protected string $programmerFilePath = ROOT_PATH . '/app/models/data/programmers.json';
    protected string $projectFilePath = ROOT_PATH . '/app/models/data/projects.json';

    public function createAction() {

        // Ensure that the form data has been submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect the data from the form
            $taskData = [
                'project_id' => $_POST['project_id'],
                'programmer_id' => $_POST['programmer_id'],
                'task_kind' => $_POST['task_kind'],
                'task_description' => $_POST['task_description'] ?? ''
            ];
            $task = new Task($_POST['project_id'], $_POST['programmer_id'], $_POST['task_kind'], $_POST['task_description']);

            // Store the created task in a session (or pass it via query string)
            $_SESSION['created_task'] = $task;
        }

        $projects = $this->loadData($this->projectFilePath);
        $programmers = $this->loadData($this->programmerFilePath);
        $this->view->title = "CRUD Task";
        $this->view->projects = $projects;
        $this->view->programmers = $programmers;
        //if($task) { $this->view->task = $task; }


        // Render the create task form view
        //$this->view->task = $task;
        //ob_end_flush(); // Flush output buffer at the end
    }

    /*

    public function getAllAction(){
        if (ob_get_level()) {
            ob_end_flush();
        }

        // Call the TaskManager's getAllTasks method
        $taskManager = TaskManager::getInstance();
        $tasks = $taskManager->getAllTasks();

        // Store the created task in a session (or pass it via query string)
        $_SESSION['all_tasks'] = $tasks;
    }

    public function showAction() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_task'])) {
            $taskId = (int) $_POST['id_task'];
    
            $taskManager = TaskManager::getInstance();
            $task = $taskManager->getTaskById($taskId);
    
            if ($task) {
                $_SESSION['selected_task'] = $task; // Store task for use in show.php
            } else {
                $_SESSION['selected_task'] = null; // Handle case if task not found
            }
        }
    
        // Render the show task view
        $this->view->render('crudtask/show.php');
    }

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

    public function deleteAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['id_task'])) {
                $taskId = (int)$_POST['id_task'];
                $confirmation = strtolower(trim($_POST['confirmation'] ?? ''));
    
                // Process only if confirmation is provided
                if ($confirmation === 'delete') {
                    $taskManager = TaskManager::getInstance();
                    $success = $taskManager->deleteTask($taskId);
    
                    if ($success) {
                        echo '<p class="rajdhani-light" style="color: green; margin-left: 10px;">Task deleted successfully!</p>';
                    } else {
                        echo '<p class="rajdhani-light" style="color: red; margin-left: 10px;">Task was deleted already.</p>';
                    }
                } elseif (!empty($confirmation)) {
                    echo '<p class="rajdhani-light" style="color: red; margin-left: 10px;">Confirmation failed. Task not deleted.</p>';
                }
            }
        }
    
        // Always render the delete form
        $this->view->render('crudtask/delete.php');
    }
    */
}
?>