<?php

namespace App\Core\Database;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASSWORD'];

        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    private function query(string $sql, array $params = []): ?\PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            return $stmt;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);

        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getRow(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);

        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public function getOne(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);
        if ($stmt) {
            $row = $stmt->fetch(PDO::FETCH_NUM);

            return $row !== false ? $row[0] : false;
        }

        return false;
    }

    public function insert(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        if ($stmt) {
            return (int)$this->connection->lastInsertId();
        }

        return 0;
    }
}
