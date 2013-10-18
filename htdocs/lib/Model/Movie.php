<?php

class Model_Movie extends Model_Relation {
    public $table = 'movie';
    public $table_alias = 'Movie';

    function init() {
        parent::init();
        $this->setControllerData('SQL');

        $this->addField('id')
            ->type('int');
        $this->addField('name');
        $this->addField('year');
        $this->addField('imdb');
    }
}