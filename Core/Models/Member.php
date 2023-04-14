<?php

namespace Core\Models;

class Member extends Model
{
    protected static $tableName = 'members';
    public $id = null;
    public $firstName;
    public $lastName;
    public $birthdate;
    public $reportSubject;
    public $country;
    public $phone;
    public $email;
    public $fullName;

    /**
     * Returns an array of all members with their email, report subject, full name, and photo (if available).
     * @return array An array of members with their email, report subject, full name, and photo (if available).
     */
    public static function getAll($fields = "*")
    {
        $result = parent::getAll($fields);
        foreach ($result as $value) {
            if($value->photo != null) {
                $value->photo = $_ENV['img_base_url'].$value->photo;
            } else {
                $value->photo = $_ENV['img_base_url'].'default.png';
            }
            $value->fullName = $value->firstName. " ".$value->lastName;
        }
        return $result;
    }
}
