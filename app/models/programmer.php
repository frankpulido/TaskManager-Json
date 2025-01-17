<?php
declare(strict_types = 1);

class Programmer {
    use JsonPersistence;
    private static string $filePath = ROOT_PATH . 'app/models/data/programmers.php';
    private static string $filePathBackup = ROOT_PATH . 'app/models/data/backup_programmers.php';
    private const ALLOWED_SKILLS = ['FRONTEND', 'BACKEND', 'DATABASE'];

    protected int $id_programmer;
    protected string $programmer_name;
    protected array $skills; // I won't use enum class this time (SET column in MySQL)

    public function __construct(string $programmer_name, array $skills) {
        $this->id_programmer = $this->generateUniqueId(self::$filePath);
        $this->programmer_name = $programmer_name;
        $this->skills = $this->setSkills($skills);
    }

    private function generateUniqueId() : int {
        $data = $this->loadData(self::$filePath);
        if (!empty($data)) {
            $ids = array_map(fn($item) => (int)$item['id_programmer'], $data);
            return max($ids) + 1;
        }
        return 1;
    }

    // Getters

    public function getIdProgrammer() : int {
        return $this->id_programmer;
    }

    public function getProgrammerName() : string {
        return $this->programmer_name;
    }

    public function getSkills() : array {
        return $this->skills;
    }

    // Setters

    public function setIdProgrammer(int $id_programmer) : void {
        $this->id_programmer = $id_programmer;
    }

    public function setProgrammerName(string $programmer_name) : void {
        $this->programmer_name = $programmer_name;
    }

    public function setSkills(array $skills) : void {
        foreach ($skills as $skill) {
            if (!in_array($skill, self::ALLOWED_SKILLS)) {
                throw new InvalidArgumentException("Invalid skill $skills");
            }
        }
        $this->skills = $skills;
    }

    
    // Serialize object to push it into the json persistence file

    public function toArray(): array {
        return [
            'id_programmer' => $this->id_programmer,
            'programmer_name' => $this->programmer_name,
            'skills' => $this->skills
            //'skills' => array_map(fn($skill) => $skill->value, $this->skills),
        ];
    }

    public function getAll() : array {
        $allProgrammers = $this->loadData(self::$filePath);
        return $allProgrammers;
    }

    public function getById(int $id_programmer) : array {
        $programmers = $this->getAll();
        foreach ($programmers as $programmer) {
            if ($programmer['id_programmer'] === $id_programmer) {
                return $programmer;
            }
        }
        return null;  // Task not found
    }

    // ************************ CRUD ************************
}
?>