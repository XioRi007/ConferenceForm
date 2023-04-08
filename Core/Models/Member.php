<?php

namespace Core\Models;

use Exception;
use PDO;
/**
 * Модель "Member" содержащая бизнес логику
 * относящуюся к сущности "Member"
 * 
 * 
 */
class Member
{
    public static function register($data)
    {
        $pdo = new PDO("mysql:host={$_ENV['host']};dbname={$_ENV['dbname']}", $_ENV['username'], $_ENV['password']);
        $sql = "INSERT INTO members (";
        foreach ($data as $key=>$value) {
            $sql = $sql.", ".$key;
        }
        $sql = $sql." ) VALUES(";
        foreach ($data as $key=>$value) {
            $sql = $sql.", :".$key;
        }
        $sql = str_replace('(,', '(', $sql);
        $sql = str_replace('),', ')', $sql);
        $sql =$sql.");";
        $statement = $pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $paramType = PDO::PARAM_STR;
            if (gettype($value) != 'string'){
                switch(gettype($value)){
                    case 'integer':
                        $paramType = PDO::PARAM_INT;
                        break; 
                }                 
            }
            $statement->bindValue(':'.$key, $value, $paramType);
        }
        $result = $statement->execute();
        if($result){
            $id = $pdo->lastInsertId();
            return $id;         
        }else{
            return NULL;
        }
    }
    public static function update($data, $id, $file=NULL)
    {
        $pdo = new PDO("mysql:host={$_ENV['host']};dbname={$_ENV['dbname']}", $_ENV['username'], $_ENV['password']);
        $sql = "UPDATE members SET";
        foreach ($data as $key => $value) {
            $sql = $sql.", ".$key."=:".$key;
        }
        if($file != NULL){
            $sql = $sql.", photo=:photo";
        }
        $sql =$sql." WHERE members.id=:id;";
        $sql = str_replace('SET,', 'SET', $sql);
        $statement = $pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $paramType = PDO::PARAM_STR;
            if (gettype($value) != 'string'){
                switch(gettype($value)){
                    case 'integer':
                        $paramType = PDO::PARAM_INT;
                        break; 
                }                 
            }
            $statement->bindValue(':'.$key, $value, $paramType);
        }
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        if($file != NULL){
            $statement->bindValue(':photo', $file, PDO::PARAM_STR);
        }
        $result = $statement->execute();
        return $result;
    }
    public static function getPersonal($id)
    {
        $pdo = new PDO("mysql:host={$_ENV['host']};dbname={$_ENV['dbname']}", $_ENV['username'], $_ENV['password']);
        $statement = $pdo->prepare("select first_name as firstName, last_name as lastName, birthdate, report_subject as reportSubject, country, phone, email from members where id='$id';");
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        if(!$result){
            throw new Exception('User not found');
        }
        return $result;
    }

    public static function getDetails($id)
    {        
        $pdo = new PDO("mysql:host={$_ENV['host']};dbname={$_ENV['dbname']}", $_ENV['username'], $_ENV['password']);
        $statement = $pdo->prepare("select company, position, about, photo from members where id='$id';");
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        if(!$result){
            throw new Exception('User not found');
        }
        return $result;
    }
    public static function getMembers()
    {
        $pdo = new PDO("mysql:host={$_ENV['host']};dbname={$_ENV['dbname']}", $_ENV['username'], $_ENV['password']);
        $statement = $pdo->prepare("select email, report_subject as reportSubject, CONCAT(first_name, ' ', last_name) AS fullName, photo from members;");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_OBJ);        
        foreach ($result as $value) {
            if($value->photo != NULL){
                $value->photo = $_ENV['img_base_url'].$value->photo;
            }
            else{
                $value->photo = $_ENV['img_base_url'].'default.png';
            }
        }
        return $result;
    }
    public static function getMembersCount()
    {
        $pdo = new PDO("mysql:host={$_ENV['host']};dbname={$_ENV['dbname']}", $_ENV['username'], $_ENV['password']);
        $statement = $pdo->prepare('SELECT count(*) as membersCount FROM `members`;');
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);
        return $result;
    }
}

