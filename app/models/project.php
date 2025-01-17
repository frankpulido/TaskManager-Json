<?php
declare(strict_types=1);

class Project {
    use JsonPersistence;
    private static string $filePath = ROOT_PATH . 'app/models/data/projects.php';
    private static string $filePathBackup = ROOT_PATH . 'app/models/data/backup_projects.php';

    protected int $id_project;
    protected string $project_name;
    protected string $project_brief;
    protected int $manager_id; // Project is not created in the system until its Project Manager is appointed.
    protected bool $delivered;

    public function __construct(string $project_name, string $project_brief, int $manager_id) {
        $this->project_name = $project_name;
        $this->project_name = $project_brief;
        $this->manager_id = $manager_id;
        $this->delivered = false;
    }

    private function generateUniqueId() : int {
        $data = $this->loadData(self::$filePath);
        if (!empty($data)) {
            $ids = array_map(fn($item) => (int)$item['id_project'], $data);
            return max($ids) + 1;
        }
        return 1;
    }

    // Getters
    
    public function getIdProject() : int {
        return $this->id_project;
    }

    public function getProjectName() : string {
        return $this->project_name;
    }

    public function getProjectBrief() : string {
        return $this->project_brief;
    }

    public function getProjectManager() : int {
        return $this->manager_id;
    }

    public function getDelivered() : bool {
        return $this->delivered;
    }

    // Setters

    public function setIdProject($id_project) : void { // This set function will be call by CREATE method in TaskManager class
        $this->id_project = $id_project;
    }

    public function setProjectName(string $project_name) : void {
        $this->project_name = $project_name;
    }

    public function setProjectBrief(string $project_brief) : void {
        $this->project_brief = $project_brief;
    }

    public function setProjectManager(int $manager_id) : void {
        $this->manager_id = $manager_id;
    }

    public function setDelivered(bool $delivered) : void {
        $this->delivered = $delivered;
    }

    // Serialize object to push it into the json persistence file

    public function toArray(): array {
        return [
            'id_project' => $this->id_project,
            'project_name' => $this->project_name,
            'project_brief' => $this->project_brief,
            'manager_id' => $this->manager_id,
            'delivered' => $this->delivered,
        ];
    }

    public function getAll() : array {
        $allProjects = $this->loadData(self::$filePath);
        return $allProjects;
    }

    public function getById(int $id_project) : array {
        $projects = $this->getAll();
        foreach ($projects as $project) {
            if ($project['id_project'] === $id_project) {
                return $project;
            }
        }
        return [];  // Task not found
    }

    // ************************ CRUD ************************
}
?>