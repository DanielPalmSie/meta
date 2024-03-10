<?php

namespace Meta\Project;

require __DIR__ . '/../vendor/autoload.php';

use PDO;
use Dotenv\Dotenv;

class Database {
    private PDO $pdo;

    public function __construct() {

        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        $host = $_ENV['POSTGRES_HOST'] ?? 'localhost';
        $dbname = $_ENV['POSTGRES_DB'];
        $username = $_ENV['POSTGRES_USER'];
        $password = $_ENV['POSTGRES_PASSWORD'];

        $this->pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    }

    public function query($query, $params = []) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        return $statement;
    }
}