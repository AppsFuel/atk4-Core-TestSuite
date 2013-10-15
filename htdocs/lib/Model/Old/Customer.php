<?php

class Model_Old_Customer extends SQL_Model {
    public $table='customer';
    
    function init(){
        parent::init();

        $this->addField('name');
        $this->addField('email');
    }
}