<?php
declare(strict_types=1);
use DateTimeImmutable;

final class Task {
    use JsonPersistence;
    private static string $filePath = ROOT_PATH . '/app/models/data/tasks.php';
    private static string $filePathBackup = ROOT_PATH . '/app/models/data/backup_tasks.php';
    private const ALLOWED_KINDS = ['FRONTEND', 'BACKEND', 'DATABASE'];

    protected int $id_task;
    protected int $project_id;
    protected int $programmer_id;
    protected $task_kind; // Important : having discarded the use of an enum we should somehow restrict possible values for data entry.
    protected $task_status; // Important : enum discarded. Triggered by other attributes' set methods.
    protected string $task_description;
    protected DateTimeImmutable $dateCreated;
    protected ?DateTime $dateInit = null;
    protected ?DateTime $dateDelivered = null; // If not approved this attribute may be overwritten later when delivered for second time.
    protected ?DateTime $dateApproved = null;

    public function __construct(int $project_id, int $programmer_id, string $task_kind, string $task_description) {
        $this->project_id = $project_id;
        $this->programmer_id = $programmer_id;
        $this->task_kind = (string) $this->setTaskKind($task_kind); // Validation
        $this->task_status = "PIPELINED"; // No longer an enum
        $this->task_description = $task_description;
        $this->dateCreated = new DateTimeImmutable();
    }

    private function generateUniqueId() : int {
        $tasks = $this->getAllTasks();
        if (!empty($tasks)) {
            $ids = array_map(fn($item) => (int)$item['id_task'], $tasks);
            return max($ids) + 1;
        }
        return 1;
    }
    
    // Getters

    public function getIdTask() : int {
        return $this->id_task;
    }

    public function getProjectId() : int {
        return $this->project_id;
    }

    public function getProgrammerId() : int {
        return $this->programmer_id;
    }

    public function getTaskKind() : string {
        return $this->task_kind;
    }

    public function getTaskStatus() : string {
        return $this->task_status;
    }

    public function getTaskDescription() : string {
        return $this->task_description;
    }

    public function getDateCreated() : DateTimeImmutable {
        return $this->dateCreated;
    }
    
    public function getDateInit() : DateTime {
        return $this->dateInit;
    }
    
    public function getDateDelivered() : DateTime {
        return $this->dateDelivered;
    }
    
    public function getDateApproved() : DateTime {
        return $this->dateApproved;
    }
    
    // Setters

    public function setIdTask($id_task) : void {
        $this->id_task = $id_task;
    }

    public function setProjectId(int $project_id) : void {
        $this->project_id = $project_id;
    }

    public function setProgrammerId(int $programmer_id) : void {
        $this->programmer_id = $programmer_id;
    }

    public function setTaskKind(string $task_kind) : void {
        if (!in_array($task_kind, self::ALLOWED_KINDS)) {
            throw new InvalidArgumentException("Invalid task kind: $task_kind");
        }
        $this->task_kind = $task_kind;
    }

    // NO SET FOR task_status : Done by triggers in Date Setters and function updateTaskStatus()

    public function setTaskDescription(string $task_description) : void {
        $this->task_description = $task_description;
    }

    // NO SET FOR dateCreated since it is immutable
    
    public function setDateInit(DateTime $dateInit) : void {
        $this->dateInit = $dateInit;
        $this->task_status = "INIT"; // Triggers an status change
    }
    
    public function setDateDelivered(DateTime $dateDelivered) : void {
        $this->dateDelivered = $dateDelivered;
        $this->task_status = "DELIVERED"; // Triggers an status change
    }
    
    public function setDateApproved(DateTime $dateApproved) : void {
        $this->dateApproved = $dateApproved;
        $this->task_status = "RELEASED"; // Triggers an status change
    }

    public function toArray(): array {
        return [
            'id_task' => $this->id_task,
            'project_id' => $this->project_id,
            'programmer_id' => $this->programmer_id,
            'task_kind' => $this->task_kind,
            'task_status' => $this->task_status,
            'task_description' => $this->task_description,
            'dateCreated' => $this->dateCreated->format(DateTime::ATOM),
            'dateInit' => $this->dateInit ? $this->dateInit->format(DateTime::ATOM) : null,
            'dateDelivered' => $this->dateDelivered ? $this->dateDelivered->format(DateTime::ATOM) : null,
            'dateApproved' => $this->dateApproved ? $this->dateApproved->format(DateTime::ATOM) : null,
        ];
    }


    // ************************ CRUD ************************

    // CREATE

    public function createTask(array $taskData) : array {
        $task = new Task(
            (int) $taskData['project_id'],
            (int) $taskData['programmer_id'],
            $taskData['task_kind'],
            $taskData['task_description'] ?? '',
        );
        $task = $task->toArray();
        $allTasks = $this->getAllTasks();
        $allTasks[] = $task;
        $this->saveData($allTasks, self::$filePath);
        return $task;
    }

    // READ

    public function getAllTasks() : array {
        $allTasks = $this->loadData(self::$filePath);
        return $allTasks;
    }

    public function getTaskById(int $id_task) : array {
        $tasks = $this->getAllTasks();
        foreach ($tasks as $task) {
            if ($task['id_task'] === $id_task) {
                return $task;
            }
        }
        return [];  // Task not found : empty array
    }

    // UPDATE : Task 'project_id' and 'task_kind' cannot be updated (proceed to delete and create a new task).

    public function updateTaskStatus() : string {
        $date = new DateTime();
        if($this->task_status == 'RELEASED') { return "Task has already been released"; }
        if($this->task_status == 'DELIVERED') { $this->setDateApproved($date); return "Task status updated to RELEASED"; }
        if($this->task_status == 'INIT') { $this->setDateDelivered($date); return "Task status updated to DELIVERED"; }
        if($this->task_status == 'PIPELINED') { $this->setDateInit($date); return "Task status updated to INIT"; }
    }

    public function updateTask(int $id_task, array $updatedData) : string {
        $allTasks = $this->getAllTasks();
        foreach($allTasks as $task) {
            if($task['id_task'] == $id_task){
                if(isset($updatedData['programmer_id'])) {$task['programmer_id'] = $updatedData['programmer_id'];}
                if(isset($updatedData['task_description'])) {$task['task_description'] = $updatedData['task_description'];}
                $this->saveData($allTasks, self::$filePath);
                return "Task programmer and/or description successfully updated for Task ID : $id_task";
            }
        }
        return "Task with ID : $id_task wasn't found in database";
    }

    public function deleteTask(int $id_task) : string {
        $existingTask = $this->getTaskById($id_task);
        if (!$existingTask) {
            return "Task with ID : $id_task wasn't found in database";
        }
        $tasks = $this->getAllTasks();
        $tasksAfterDeletion = array_filter($tasks, fn($task) => $task['id_task'] !== $id_task);
    
        $this->saveData($tasksAfterDeletion, self::$filePath);
        return "Task with ID : $id_task has been deleted from database";
    }
}
?>