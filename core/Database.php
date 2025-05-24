<?php
/**
 * Database - Database Connection Handler
 * Egypt Printing Services Marketplace
 */

class Database
{
    private $connection;

    /**
     * Constructor - Establish database connection
     */
    public function __construct()
    {
        try {
            $this->connection = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            // Log error and display friendly message
            $this->logError($e);
            die('Database connection failed. Please try again later.');
        }
    }

    /**
     * Execute a query and return the statement
     */
    public function query($sql, $params = [])
    {
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            $this->logError($e);
            throw new Exception('Database query failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute a query and return a single record
     */
    public function fetchOne($sql, $params = [])
    {
        $statement = $this->query($sql, $params);
        return $statement->fetch();
    }

    /**
     * Execute a query and return all records
     */
    public function fetchAll($sql, $params = [])
    {
        $statement = $this->query($sql, $params);
        return $statement->fetchAll();
    }

    /**
     * Execute a query and return a single column
     */
    public function fetchColumn($sql, $params = [], $column = 0)
    {
        $statement = $this->query($sql, $params);
        return $statement->fetchColumn($column);
    }

    /**
     * Execute an insert query and return the last insert ID
     */
    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }

    /**
     * Execute an update query and return the number of affected rows
     */
    public function update($table, $data, $where, $whereParams = [])
    {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "$column = ?";
        }
        $setClause = implode(', ', $set);

        $sql = "UPDATE $table SET $setClause WHERE $where";

        $params = array_merge(array_values($data), $whereParams);
        $statement = $this->query($sql, $params);

        return $statement->rowCount();
    }

    /**
     * Execute a delete query and return the number of affected rows
     */
    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM $table WHERE $where";

        $statement = $this->query($sql, $params);
        return $statement->rowCount();
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollback()
    {
        return $this->connection->rollBack();
    }

    /**
     * Get the database connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Log database error
     */
    private function logError($exception)
    {
        // Log error to file
        $errorMessage = date('Y-m-d H:i:s') . ' - Database Error: ' . $exception->getMessage() .
                        ' in ' . $exception->getFile() . ' on line ' . $exception->getLine() . "\n";

        error_log($errorMessage, 3, BASE_PATH . '/logs/database_errors.log');
    }
}
