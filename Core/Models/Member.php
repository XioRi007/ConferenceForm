<?php

namespace Core\Models;

class Member extends Model
{
    protected static $tableName = 'members';

    /**
     * Get personal information for a specific user by ID.
     * @param int $id The ID of the user to retrieve personal information for.
     * @return object The personal information for the specified user.
     */
    public static function getPersonal($id)
    {
        $result = parent::findById($id, array('first_name as firstName', 
                                              'last_name as lastName', 
                                              'birthdate', 
                                              'report_subject as reportSubject',
                                              'country', 
                                              'phone', 
                                              'email'));
        return $result;
    }
    
    /**
     * Returns the details of a record with the given ID.
     * @param int $id The ID of the record.
     * @return object The details of the record.
     */
    public static function getDetails($id)
    {
        $result = parent::findById($id, array('company',  
                                              'position', 
                                              'about', 
                                              'photo'));
        return $result;
    }

    /**
     * Returns an array of all members with their email, report subject, full name, and photo (if available).
     * @return array An array of members with their email, report subject, full name, and photo (if available).
     */
    public static function getMembers()
    {
        $result = parent::getAll(array( 'email', 
                                        'report_subject as reportSubject', 
                                        'CONCAT(first_name, \' \', last_name) AS fullName', 
                                        'photo'));        
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

