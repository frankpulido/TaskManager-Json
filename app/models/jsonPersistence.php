<?php
declare(strict_types = 1);

trait JsonPersistence {
    public function loadData(string $filePath) : array {
        if (!file_exists($filePath)) return [];
        $data = file_get_contents($filePath);
        return json_decode($data, true) ?? [];
    }

    private function saveData(array $data, string $filePath) : bool {
        return file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }

    private function overwriteBackup(string $filePath,string $filePathBackup) : string {
        if(!file_exists($filePath) || !file_exists($filePathBackup)) {
            return "There is an error in the path route of the files. Couldn't overwrite the Backup.";
        }
        $data = file_get_contents($filePath);
        file_put_contents($filePathBackup, $data);
        return "The Backup file has been correctly overwritten with your current production data.";
    }

    private function restoreBackup(string $filePath,string $filePathBackup) : string {
        // Here we overwrite data modified in production : $filePath is overwritten by $filePathBackup
        if(!file_exists($filePath) || !file_exists($filePathBackup)) {
            return "There is an error in the path route of the files. Couldn't restore the Backup.";
        }
        $data = file_get_contents($filePathBackup);
        file_put_contents($filePath, $data);
        return "The Backup file has been correctly restored as production data.";
    }
}
?>