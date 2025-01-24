<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller 
{
    use JsonPersistence;
    protected string $taskFilePath = ROOT_PATH . '/app/models/data/tasks.json';
    protected string $programmerFilePath = ROOT_PATH . '/app/models/data/programmers.json';
    protected string $projectFilePath = ROOT_PATH . '/app/models/data/projects.json';

    public function indexAction()
    {
        $tasks = $this->loadData($this->taskFilePath);
        $this->view->title = "View ALL in GRID";
        $this->view->tasks = $tasks;
    }

    public function byProjectAction()
    {
        $tasks = $this->loadData($this->taskFilePath);
        $projects = $this->loadData($this->projectFilePath);
        $categorizedTasks = [];
        $associativeProjects = [];

        foreach ($tasks as $task) {
            $project_id = (int) $task['project_id'];
            $categorizedTasks[$project_id][] = $task;
        }

        foreach ($projects as $project) {
            $project_id = (int) $project['id_project'];
            $associativeProjects[$project_id] = $project;
        }

        ksort($categorizedTasks);
        ksort($associativeProjects);

        $this->view->title = "View grouped by PROJECT";
        $this->view->tasks = $categorizedTasks;
        $this->view->projects = $associativeProjects;
    }

    public function columnKindAction()
    {
        $tasks = $this->loadData($this->taskFilePath);
        $categorizedTasks = [
            'FRONTOFFICE' => [],
            'BACKOFFICE' => [],
            'DATABASE' => []
        ];

        foreach ($tasks as $task) {
            $kind = $task['task_kind'];
            if (isset($categorizedTasks[$kind])) {
                $categorizedTasks[$kind][] = $task;
            }
        }

        $this->view->title = "View grouped by KIND";
        $this->view->tasks = $categorizedTasks;
    }

    public function columnProgressAction()
    {
        $tasks = $this->loadData($this->taskFilePath);
        $categorizedTasks = [
            'PIPELINED' => [],
            'INIT' => [],
            'DELIVERED' => [],
            'RELEASED' => []
        ];

        foreach ($tasks as $task) {
            $status = $task['task_status'];
            if (isset($categorizedTasks[$status])) {
                $categorizedTasks[$status][] = $task;
            }
        }

        $this->view->title = "View grouped by PROGRESS";
        $this->view->tasks = $categorizedTasks;
    }
}