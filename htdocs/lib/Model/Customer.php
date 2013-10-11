<?php

class Model_Customer extends Model_Relation {
    public $table = 'customer';
    public $table_alias = 'Customer';

    function init() {
        parent::init();
        $this->setControllerData('SQL');

        $this->addField('id')
            ->type('int');
        $this->addField('name');
        $this->addField('email');
        $this->addField('password');

        $this->hasMany('Rental');
    }
}