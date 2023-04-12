<?php

namespace Core\Models;

use Exception;
use PDO;

class Model
{
    /**
     * Get a PDO database connection instance.
     * @return PDO Returns a PDO database connection instance.
     */
    protected static function getConnection()
    {
        return new PDO(
            "mysql:host={$_ENV['host']};dbname={$_ENV['dbname']}",
            $_ENV['username'],
            $_ENV['password']
        );
    }

    /**
     * Finds a record by its ID in the database table.
     * @param int $id The ID of the record to find.
     * @param array $fieldsArray An optional array of field names to select in the query.
     * @return object The retrieved record as an object.
     * @throws Exception If the record with the given ID is not found in the table.
     */
    public static function findById($id, $fieldsArray=[])
    {
        $pdo = static::getConnection();
        $fields = '*';
        if(count($fieldsArray)) {
            $fields = implode(", ", $fieldsArray);
        }
        $statement = $pdo->prepare("SELECT $fields FROM " . static::$tableName . " WHERE id=:id");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        if (!$result) {
            throw new Exception('Record not found');
        }
        return $result;
    }

    /**
     * Get all records from the database table.
     * @param array $fieldsArray An optional array of field names to select in the query.
     * @return array An array of objects representing the records.
     */
    public static function getAll($fieldsArray=[])
    {
        $pdo = static::getConnection();
        $fields = '*';
        if(count($fieldsArray)) {
            $fields = implode(", ", $fieldsArray);
        }
        $statement = $pdo->prepare("SELECT $fields FROM " . static::$tableName);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    /**
     * Deletes a record from the database by ID.
     *
     * @param int $id The ID of the record to be deleted.
     * @return bool Returns true if the deletion was successful, false otherwise.
     * @throws Exception If the record does not exist or an error occurs while deleting the record.
     */
    public static function delete($id)
    {
        $pdo = static::getConnection();
        $statement = $pdo->prepare("DELETE FROM " . static::$tableName . " WHERE id=:id");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $statement->execute();
        if (!$result) {
            throw new Exception('Error deleting record');
        }
        if ($statement->rowCount() === 0) {
            throw new Exception('Record not found');
        }
        return true;
    }

    /**
     * Update a record in the database.
     *
     * @param array $data An associative array containing the updated values.
     * @param int $id The ID of the record to be updated.
     * @return bool Returns true on success, false otherwise.
     * @throws Exception Throws an exception if the record is not found.
     */
    public static function update($data, $id)
    {
        $pdo = static::getConnection();
        $sql = "UPDATE " . static::$tableName . " SET";
        foreach ($data as $key => $value) {
            $sql = $sql . " " . $key . "=:" . $key . ",";
        }
        $sql = rtrim($sql, ",");
        $sql = $sql . " WHERE id=:id;";
        $statement = $pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $paramType = PDO::PARAM_STR;
            if (gettype($value) != 'string') {
                switch (gettype($value)) {
                    case 'integer':
                        $paramType = PDO::PARAM_INT;
                        break;
                }
            }
            $statement->bindValue(':' . $key, $value, $paramType);
        }
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $statement->execute();
        if(!$result) {
            throw new Exception('Error updating record');
        }
        return $result;
    }

    /**
     * Create a new record in the database table.
     * @param array $data An associative array containing the column values to be inserted.
     * @return string Returns ID of the inserted entity
     * @throws Exception Throws an exception if the insert operation failed.
     */
    public static function create($data)
    {
        $pdo = static::getConnection();
        $sql = "INSERT INTO " . static::$tableName . " (";
        foreach ($data as $key => $value) {
            $sql .= $key . ", ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ") VALUES (";
        foreach ($data as $key => $value) {
            $sql .= ":" . $key . ", ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ")";
        $statement = $pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $paramType = PDO::PARAM_STR;
            if (gettype($value) != 'string') {
                switch (gettype($value)) {
                    case 'integer':
                        $paramType = PDO::PARAM_INT;
                        break;
                }
            }
            $statement->bindValue(':' . $key, $value, $paramType);
        }
        $result = $statement->execute();
        if (!$result) {
            throw new Exception('Insert operation failed');
        }
        return $pdo->lastInsertId();
    }

    /**
     * Returns the number of records in the table.
     *
     * @return int The number of records.
     * @throws Exception If an error occurs while executing the query.
     */
    public static function count()
    {
        $pdo = static::getConnection();
        $statement = $pdo->prepare("SELECT COUNT(*) FROM " . static::$tableName);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_COLUMN);
        return $result;
    }
}
