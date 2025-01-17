<?php
declare(strict_types = 1);

class Programmer {
    //
    protected int $id_programmer;
    protected string $programmer_name;
    protected array $skills; // I won't use enum class this time (SET column in MySQL)

    public function __construct(string $programmer_name, array $skills) {
        $this->programmer_name = $programmer_name;
        $this->skills = $skills;
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
}
?>