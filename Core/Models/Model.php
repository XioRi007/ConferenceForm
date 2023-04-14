<?php

namespace Core\Models;

use Exception;
use PDO;

class Model
{
    /**
     * The PDO object used to interact with the database.
     * @var \PDO
    */
    protected $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            "mysql:host={$_ENV['host']};dbname={$_ENV['dbname']}",
            $_ENV['username'],
            $_ENV['password']
        );
    }
    public function __destruct()
    {
        $this->pdo = null;
    }
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Finds a record by its ID in the database table and sets its keys to attributes.
     * @param int $id The ID of the record to find.
     * @param array $fieldsArray An optional array of field names to select in the query.
     * @throws Exception If the record with the given ID is not found in the table.
     */
    public function findById($id, $fieldsArray=[])
    {
        $fields = '*';
        if(count($fieldsArray)) {
            $fields = implode(", ", $fieldsArray);
        }
        $statement = $this->pdo->prepare("SELECT $fields FROM " . static::$tableName . " WHERE id=:id");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        if (!$result) {
            throw new Exception('Record not found');
        }

        foreach ($result as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * Get all records from the database table.
     * @param array $fieldsArray An optional array of field names to select in the query.
     * @return array An array of objects representing the records.
     */
    public static function getAll($fieldsArray=[])
    {
        $myClass = new self();
        $pdo = $myClass->pdo;
        $fields = '*';
        if(count($fieldsArray)) {
            $fields = implode(", ", $fieldsArray);
        }
        $statement = $pdo->prepare("SELECT $fields FROM " . static::$tableName);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        $members = [];

        foreach ($result as $member) {
            $model = new static();

            foreach ($member as $key => $value) {
                $model->$key = $value;
            }

            $members[] = $model;
        }

        return $members;
    }

    /**
     * Deletes a record from the database by ID.
     *
     * @param int $id The ID of the record to be deleted.
     * @return bool Returns true if the deletion was successful, false otherwise.
     * @throws Exception If the record does not exist or an error occurs while deleting the record.
     */
    public function delete($id)
    {
        $statement = $this->pdo->prepare("DELETE FROM " . static::$tableName . " WHERE id=:id");
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
     * Update a record in the database and sets its keys to attributes.
     *
     * @param array $data An associative array containing the updated values.
     * @param int $id The ID of the record to be updated.
     * @throws Exception Throws an exception if the record is not found.
     */
    public function update($data, $id)
    {
        $sql = "UPDATE " . static::$tableName . " SET";
        foreach ($data as $key => $value) {
            $sql = $sql . " " . $key . "=:" . $key . ",";
        }
        $sql = rtrim($sql, ",");
        $sql = $sql . " WHERE id=:id;";
        $statement = $this->pdo->prepare($sql);
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
    }

    /**
     * Create a new record in the database table and sets its keys to attributes.
     * @param array $data An associative array containing the column values to be inserted.
     * @throws Exception Throws an exception if the insert operation failed.
     */
    public function create($data)
    {
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
        $statement = $this->pdo->prepare($sql);
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
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
        $this->id=$this->pdo->lastInsertId();
    }

    /**
     * Returns the number of records in the table.
     *
     * @return int The number of records.
     * @throws Exception If an error occurs while executing the query.
     */
    public static function count()
    {
        $myClass = new self();
        $pdo = $myClass->pdo;
        $statement = $pdo->prepare("SELECT COUNT(*) FROM " . static::$tableName);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_COLUMN);
        return $result;
    }

    /**
     * Get the attributes of the model by given attribute names.
     *
     * @param  array  $attributeNames
     * @return array
     */
    public function getAttributesByNames(array $attributeNames): array
    {
        $attributes = [];
        foreach ($attributeNames as $attributeName) {
            if (property_exists($this, $attributeName)) {
                $attributes[$attributeName] = $this->$attributeName;
            }
        }
        return $attributes;
    }
}
