<?php
declare(strict_types=1);
use DateTimeImmutable;

final class Task {
    protected int $id_task;
    protected int $project_id;
    protected int $programmer_id; // When a task is created it must have a programmer assigned, then this attribute can be changed.
    protected $task_kind; // Important : discarding the use os an enum we should somehow restrict possible values for data entry.
    protected $task_status; // Important : discarding the use os an enum we should somehow restrict possiblevalues for data entry.
    protected string $task_description;
    protected DateTimeImmutable $dateCreated;
    protected ?DateTime $dateInit = null;
    protected ?DateTime $dateDelivered = null; // If not approved this attribute may be overwritten later when delivered for second time.
    protected ?DateTime $dateApproved = null;

    public function __construct(int $project_id, int $programmer_id, string $task_kind, string $task_description) {
        $this->project_id = $project_id;
        $this->programmer_id = $programmer_id;
        $this->task_kind = $task_kind; // No longer an enum
        $this->task_status = "PIPELINED"; // No longer an enum
        $this->task_description = $task_description;
        $this->dateCreated = new DateTimeImmutable();
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
        $this->task_kind = $task_kind;
    }

    public function setTaskStatus(string $task_status) : void {
        $this->task_status = $task_status;
    }

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

    // Serialize object to push it into the json persistence file

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
    // CREATING A TASK
    /*
    $task = new Task($projectId, $programmerId, 'FRONTOFFICE', 'INIT', 'Task description here.');
    $taskData = $task->toArray();
    $jsonData = json_encode($taskData, JSON_PRETTY_PRINT);
    file_put_contents('tasks.json', $jsonData);
    */
}

?>