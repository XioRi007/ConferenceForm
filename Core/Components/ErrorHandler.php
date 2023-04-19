<?php

namespace Core\Components;

use PDOException;

class ErrorHandler
{
    public static function handle(PDOException $exception)
    {
        $errorCode = $exception->getCode();
        switch ($errorCode) {
            case '23000':
                $errorObject = self::parseDuplicateError($exception->getMessage());
                break;
            case '22001':
                $errorObject = self::parseLengthError($exception->getMessage());
                break;
            default:
                $errorObject = $exception->getMessage();
                break;
        }
        return $errorObject;
    }

    private static function parseDuplicateError($message)
    {
        preg_match("/(?<=key ')[^']+(?=')/", $message, $matches);
        if (count($matches) < 1) {
            return "An unexpected error occurred.";
        }
        $field = $matches[0];
        if (strpos($field, '.') !== false) {
            $field = substr($field, strpos($field, '.') + 1);
        }
        return ['field' => $field, 'message' => "User with this " . $field . " already exists. Enter another one."];
    }
    private static function parseLengthError($message)
    {
        preg_match("/(?<=column ')\w+(?=' at row )/", $message, $matches);
        if (count($matches) < 1) {
            return "An unexpected error occurred.";
        }
        return ['field' => $matches[0], 'message' => "Data too long for the " . $matches[0] . " field. Enter another one."];
    }
}
