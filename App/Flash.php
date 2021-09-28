<?php

namespace App;

class Flash
{
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';

    //Add a message
    public static function addMessage($message, $type = 'success')
    {
        //Create array in the session if it doesn't already exist
        if (! isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        //Append the message to the array
        $_SESSION['flash_notifications'][] = ['body' => $message, 'type' => $type];
    }

    //Get all the messages
    public static function getMessages()
    {
        if (isset($_SESSION['flash_notifications'])) {
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);

            return $messages;
        }
    }
}
