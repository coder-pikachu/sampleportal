<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Course
 *
 * @author sangrammohite
 */
class Course {

    //put your code here
    public $id;
    public $name;
    public $date;
    public $city;
    public $state;
    
    public function __construct($id, $name, $date, $city, $state) {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
        $this->city = $city;
        $this->state = $state;
    }

}
