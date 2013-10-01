<?php

class Model_TestModel extends Model {
    public $id_field = 'field1';
    public $title_field = 'field3';

    function init() {
        parent::init();

        $this->addField('field1')->group('group1');
        $this->addField('field2')->group('group1');
        $this->addField('field3')->group('group2');

        $this->setControllerData('Foo');
    }

    public $stopAt = null;
    public $step = 0;
    function eachFunction() {
        $this->step++;
        if ($this->stopAt && $this->stopAt == $this->step) {
            return false;
        }
        $this->eachArguments[] = func_get_args();
    }
}