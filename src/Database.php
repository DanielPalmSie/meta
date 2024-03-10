<?php

namespace Meta\Project;

use PDO;

class Database {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = new PDO('pgsql:host=postgres;dbname=postgres', 'your_username', 'your_password');
    }

    public function query($query, $params = []) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        return $statement;
    }
}