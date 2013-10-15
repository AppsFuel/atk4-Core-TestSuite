<?php

class TestCase_SQLModel extends TestCase {
    function init() {
        parent::init();
        $this->api->dbConnect('mysql://root:aa@localhost/atk4core_testsuite');
        $this->api->db->beginTransaction(); // force use transaction
    }

    function testLoad() {
        $model = $this->add('Model_Old_Customer');

        $model->load(1);

        $expected = array(
            'id' => '1',
            'name' => 'John Smith',
            'email' => 'demo',
        );
        $this->assertEquals($expected, $model->get());
    }

    function testLoadWithCondition() {
        $model = $this->add('Model_Old_Customer');
        $model->addCondition('id', '!=', 1);

        $model->tryLoad(1);

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testInsert() {
        $model = $this->add('Model_Old_Customer');
        $model->set('email', 'example')
            ->set('name', 'Example Example');

        $model->save();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
        $this->assertEquals('example', $model->get('email'));
        $this->assertEquals('Example Example', $model->get('name'));
    }

    function testInsertWithConditions() {
        $model = $this->add('Model_Old_Customer');
        $model->addCondition('email', '!=', 'example');
        $model->set('email', 'example')
            ->set('name', 'Example Example');

        $e = $this->assertThrowException('BaseException', $model, 'save');

        $this->assertEquals('Saved model did not match conditions. Save aborted.', $e->getMessage());
    }

    function testUpdate() {
        $model = $this->add('Model_Old_Customer');
        $model->load(1);
        $model->set('email', 'example')
            ->set('name', 'Example Example');

        $model->save();

        $expected = array(
            'id' => '1',
            'name' => 'Example Example',
            'email' => 'example',
        );
        $this->assertEquals($expected, $model->get());
        $this->assertEquals('1', $model->id);
    }

    function testUpdateWithConditions() {
        $model = $this->add('Model_Old_Customer');
        $model->load(1);
        $model->addCondition('email', '!=', 'example');
        $model->set('email', 'example')
            ->set('name', 'Example Example');

        $e = $this->assertThrowException('BaseException', $model, 'save');

        $this->assertEquals('Record could not be loaded', $e->getMessage());
        $this->assertEquals('1', $e->more_info['id']);
    }

    function testDelete() {
        $model = $this->add('Model_Old_Customer');
        $model->load(1);

        $model->delete();

        $model = $this->add('Model_Old_Customer');
        $model->tryLoad(1);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteWithConditionsAlreadyLoaded() {
        $model = $this->add('Model_Old_Customer');
        $model->load(1);
        $model->addCondition('id', '!=', 1);

        $model->delete();

        $model = $this->add('Model_Old_Customer');
        $model->tryLoad(1);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteWithConditionsNotLoaded() {
        $model = $this->add('Model_Old_Customer');
        $model->addCondition('id', '!=', 1);

        $e = $this->assertThrowException('BaseException', $model, 'delete', array(1));

        $this->assertEquals('Record could not be loaded', $e->getMessage());
    }
}