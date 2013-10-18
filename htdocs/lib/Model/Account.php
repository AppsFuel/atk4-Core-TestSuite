<?php

class Model_Account extends Model {

    public $baseListUrl = '/profiler/service/2/user/';
    public $baseInstanceUrl = '/profiler/service/2/user/{uid}/';

    public $id_field= 'uid';

    function init() {
        parent::init();

        $this->addField($this->id_field)
            ->system(true);

        $this->addField('email')
                ->group('post')
                ->type('string')
                ->length(50)
                ->mandatory(true);

        $this->addField('password')
                ->group('post')
                ->type('password')
                ->length(64)
                ->system(true);

        $this->addField('campaign_id')
                ->group('post')
                ->type('int')
                ->defaultValue(null);

        $this->addField('flight_id')
                ->group('post')
                ->type('int')
                ->defaultValue(null);

        $this->addField('click_id')
                ->group('post')
                ->type('int')
                ->defaultValue(null);

		$this->addField('ip')
			->type('string')
			->group('post')
			->length(15)
			->defaultValue(null)
			->mandatory(false);

        $this->addField('updated')
                ->type('datetime')
                ->system(true);

        $this->addField('created')
                ->type('datetime')
                ->system(true);

        $this->setControllerData('Gaia');
    }
}
