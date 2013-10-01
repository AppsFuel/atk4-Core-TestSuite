<?php

class Model_ComplexModel extends Model {
    public $title_field = 'title';
    public $id_field = 'id';

    function init() {
        parent::init();

        $this->addField('id');
        $this->addField('title');
        $this->addField('description');
        $this->hasOne('Model_ComplexModel', 'parent_id');
        $this->hasMany('Model_ComplexModel', 'parent_id');

        $this->setControllerData('Foo');
        $this->setControllerSource(self::$exampleSource);
    }

    static public $exampleSource = array(
        array('id' =>  1, 'title' => 'Title1', 'description' => 'Description of Title1', 'parent_id' => 6),
        array('id' =>  2, 'title' => 'Title2', 'description' => 'Description of Title2', 'parent_id' => 2),
        array('id' =>  3, 'title' => 'Title3', 'description' => 'Description of Title3', 'parent_id' => 8),
        array('id' =>  4, 'title' => 'Title4', 'description' => 'Description of Title4', 'parent_id' => 3),
        array('id' =>  5, 'title' => 'Title5', 'description' => 'Description of Title5', 'parent_id' => 4),
        array('id' =>  6, 'title' => 'Title6', 'description' => 'Description of Title6', 'parent_id' => 5),
        array('id' =>  7, 'title' => 'Title7', 'description' => 'Description of Title7', 'parent_id' => 1),
        array('id' =>  8, 'title' => 'Title8', 'description' => 'Description of Title8', 'parent_id' => 7),
    );
}