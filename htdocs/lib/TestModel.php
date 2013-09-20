<?php

class TestModel extends Model {
    public $id_field = 'field1';

    function init() {
        parent::init();

        $this->addField('field1')->group('group1');
        $this->addField('field2')->group('group1');
        $this->addField('field3')->group('group2');
    }
}