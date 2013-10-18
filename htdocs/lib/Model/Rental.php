<?php

class Model_Rental extends Model_Relation {
    public $table = 'rental';
    public $table_alias = 'Rental';

    function init() {
        parent::init();
        $this->setControllerData('SQL');

        $this->addField('id')
            ->type('int');
        $this->addField('is_returned')
            ->type('boolean');
        $this->addField('dvd_id');

        $this->hasOne('Customer', 'customer_id');
    }
}
