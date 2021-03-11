<?php

namespace Bliksem_Relationships;

class bliksemException extends Exception
{
    public function errorMessage()
    {
        //error message
        $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
    .': <b>'.$this->getMessage().'</b> is not a valid E-Mail address';
        return $errorMsg;
    }
}

$email = "someone@example...com";

try {
    //check if
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        //throw exception if email is not valid
        throw new bliksemException($email);
    }
} catch (bliksemException $e) {
    //display custom message
    echo $e->errorMessage();
}