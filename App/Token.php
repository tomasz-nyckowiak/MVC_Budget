<?php

namespace App;

class Token
{
    //The token value
    protected $token;
    
    /* Class constructor. Create a new random token or assign an existing one if passed in.
    @param string $value (optional) A token value*/
    public function __construct($token_value = null)
    {
        if ($token_value) {
            $this->token = $token_value;
        } else {
            $this->token = bin2hex(random_bytes(16));  //16 bytes = 128 bits = 32 hex characters
        }
    }

    //Get the token value
    public function getValue()
    {
        return $this->token;
    }

    //Get the hashed token value
    public function getHash()
    {
        return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY);  //sha256 = 64 chars
    }
}
