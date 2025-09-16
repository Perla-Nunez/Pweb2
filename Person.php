<?php

class Person{
    public $name;
    public $email;
    public $password;

    public function __construct($name, $password, $email){
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function breath(){
        echo $this->name . ' is breathing';
    }
}