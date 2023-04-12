<?php

namespace Core\Components;

use PDOException;

class ErrorHandler {

    public static function handle(PDOException $exception) {
        $errorCode = $exception->getCode();
        switch ($errorCode) {
            case '23000':
                $errorMessage = self::parseDuplicateError($exception->getMessage());
                break;
            case '22001':
                $errorMessage = self::parseLengthError($exception->getMessage());
                break;
            default:
                $errorMessage = $exception->getMessage();
                break;
        }
        return $errorMessage;
    }
    
    private static function parseDuplicateError($message) {
        preg_match("/(?<=for key ')\w+\.(?<word>\w+)(?=')/", $message, $matches);
        if (count($matches) < 2) {
            return "An unexpected error occurred.";
        }
        return "User with this " . $matches[1] . " already exists. Enter another one.";
    }
    private static function parseLengthError($message) {
        preg_match("/(?<=column ')\w+(?=' at row )/", $message, $matches);
        if (count($matches) < 1) {
            return "An unexpected error occurred.";
        }
        return "Data too long for the " . $matches[0] . " field. Enter another one.";
    }
}