<?php

namespace Core\Models;

class Member extends Model
{
    protected static $tableName = 'members';
    public static function getPersonal($id)
    {
        $result = parent::findById($id, array(  'first_name as firstName', 
                                                'last_name as lastName', 
                                                'birthdate', 
                                                'report_subject as reportSubject',
                                                'country', 
                                                'phone', 
                                                'email'));
        return $result;
    }

    public static function getDetails($id)
    {
        $result = parent::findById($id, array(  'company',  
                                                'position', 
                                                'about', 
                                                'photo'));
        return $result;
    }
    public static function getMembers()
    {
        $result = parent::getAll(array('email', 'report_subject as reportSubject', 'CONCAT(first_name, \' \', last_name) AS fullName', 'photo'));        
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
}

