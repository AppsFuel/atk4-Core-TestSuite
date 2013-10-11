<?php

class TestCase_ControllerDataArray extends TestCase {
    function init() {
        parent::init();
        $this->model = $this->add('Model', array('id_field' => 'id'));
        $this->model->addField('id')->type('int');
        $this->model->addField('parent_id')->type('flaot');
        $this->model->addField('field')->type('str');

        $this->rows = array(
            4 => array('id' => 4, 'parent_id' => 87.4, 'field' => 'pluto'),
            6 => array('id' => 6, 'parent_id' => 826, 'field' => 'pippo'),
            9 => array('id' => 9, 'parent_id' => 0.1, 'field' => 'foo'),
            10 => array('id' => 10, 'parent_id' => 46.4, 'field' => 'bar'),
        );

        $this->controller = $this->add('Controller_Data_Array');        
        $this->controller->setSource($this->model, $this->rows);

        $this->model->setControllerData($this->controller);
    }

    function testConditions() {
        $row = array('id' => 5, 'parent_id' => 4, 'field' => 'pluto');

        $cond = array('parent_id', '>', 3);
        $this->assertTrue($this->controller->isValid($row, $cond), 'Fail conditions >');
        $cond = array('parent_id', '>', 5);
        $this->assertFalse($this->controller->isValid($row, $cond), 'Fail conditions >');

        $cond = array('parent_id', '>=', 4);
        $this->assertTrue($this->controller->isValid($row, $cond), 'Fail conditions =>');
        $cond = array('parent_id', '>=', 2);
        $this->assertTrue($this->controller->isValid($row, $cond), 'Fail conditions =>');
        $cond = array('parent_id', '>=', 6);
        $this->assertFalse($this->controller->isValid($row, $cond), 'Fail conditions =>');
        
        $cond = array('parent_id', '<=', 4);
        $this->assertTrue($this->controller->isValid($row, $cond), 'Fail conditions <=');
        $cond = array('parent_id', '<=', 6);
        $this->assertTrue($this->controller->isValid($row, $cond), 'Fail conditions <=');
        $cond = array('parent_id', '<=', 3);
        $this->assertFalse($this->controller->isValid($row, $cond), 'Fail conditions <=');
        
        $cond = array('parent_id', '<', 6);
        $this->assertTrue($this->controller->isValid($row, $cond), 'Fail conditions <');
        $cond = array('parent_id', '<', 4);
        $this->assertFalse($this->controller->isValid($row, $cond), 'Fail conditions <');
        $cond = array('parent_id', '<', 3);
        $this->assertFalse($this->controller->isValid($row, $cond), 'Fail conditions <');

        $cond = array('parent_id', '=', 4);
        $this->assertTrue($this->controller->isValid($row, $cond), 'Fail conditions =');
        $cond = array('parent_id', '=', 6);
        $this->assertFalse($this->controller->isValid($row, $cond), 'Fail conditions =');

        $cond = array('parent_id', '!=', 6);
        $this->assertTrue($this->controller->isValid($row, $cond), 'Fail conditions !=');
        $cond = array('parent_id', '!=', 4);
        $this->assertFalse($this->controller->isValid($row, $cond), 'Fail conditions !=');
    }

    function testInvalidCondition() {
        $row = array('id' => 5, 'parent_id' => 4, 'field' => 'pluto');

        $cond = array('parent_id', 'invalidCondition', 3);
        $this->assertThrowException('Exception_DB', $this->controller, 'isValid', array($row, $cond));
    }

    function testGetIdsFromConditions() {
        $conditions = array(
            array('parent_id', '>', 1)
        );
        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions);
        $this->assertEquals(array(4, 6, 10), $ids);


        $conditions = array(
            array('parent_id', '>', 1), array('parent_id', '<', 100)
        );
        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions);
        $this->assertEquals(array(4, 10), $ids);

        $conditions = array(
            array('parent_id', '>', 1),
            array('field', '!=', 'pluto')
        );
        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions);
        $this->assertEquals(array(6, 10), $ids);

        $conditions = array(
            array('parent_id', '>', 1), array('parent_id', '<', 100),
            array('field', '!=', 'pluto')
        );
        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions);
        $this->assertEquals(array(10), $ids);

        $conditions = array(
            array('parent_id', '>', 1), array('parent_id', '<', 100),
            array('field', '!=', 'pluto'),
            array('id', '!=', 10),
        );
        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions);
        $this->assertEquals(array(), $ids);
    }

    function testGetIdsFromConditionsWithLimit() {
        $conditions = array(
            array('parent_id', '>', 1)
        );
        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions, array(1, 2));
        $this->assertEquals(array(6, 10), $ids);

        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions, array(0, 2));
        $this->assertEquals(array(4, 6), $ids);

        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions, array(1, null));
        $this->assertEquals(array(6), $ids);

        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions, array(null, null));
        $this->assertEquals(array(4, 6, 10), $ids);

        $ids = $this->controller->getIdsFromConditions($this->rows, $conditions, null);
        $this->assertEquals(array(4, 6, 10), $ids);
    }

    function testSetSourceAssoc() {
        $this->assertEquals($this->rows, $this->model->_table[$this->controller->short_name]);
    }

    function testSetSourceList() {
        $rows = array(
            array('id' => 5, 'parent_id' => 46, 'field' => 'field5'),
            array('id' => 7, 'parent_id' => 99, 'field' => 'field7'),
            array('id' => 6, 'parent_id' => 33, 'field' => 'field6'),
        );
        $this->model->setControllerSource($rows);

        $expected = array(
            5 => array('id' => 5, 'parent_id' => 46, 'field' => 'field5'),
            7 => array('id' => 7, 'parent_id' => 99, 'field' => 'field7'),
            6 => array('id' => 6, 'parent_id' => 33, 'field' => 'field6'),
        );
        $this->assertEquals($expected, $this->model->_table[$this->model->controller->short_name]);
    }

    function testSetSourceDifferentKey() {
        $model = $this->add('Model_TestModel');
        $model->id_field = 'field';

        $this->controller->setSource($model, $this->rows);

        $expected = array(
            'pluto' => array('id' => 4, 'parent_id' => 87.4, 'field' => 'pluto'),
            'pippo' => array('id' => 6, 'parent_id' => 826, 'field' => 'pippo'),
            'foo' => array('id' => 9, 'parent_id' => 0.1, 'field' => 'foo'),
            'bar' => array('id' => 10, 'parent_id' => 46.4, 'field' => 'bar'),
        );
        $this->assertEquals($expected, $model->_table[$this->controller->short_name]);
    }

    function testSetSourceWrongArgument() {
        $model = $this->add('Model_TestModel');
        $controller = $this->add('Controller_Data_Array');

        $e = $this->assertThrowException('Exception_DB', $controller, 'setSource', array($model, 'invalidArgument'));
        $this->assertEquals('Wrong type: expected array', $e->getMessage());
    }

    function testDelete() {
        $this->controller->delete($this->model, 4);

        $this->assertFalse(isset($this->model->_table[$this->controller->short_name][4]), 'Deletion fail');
    }

    function testDeleteUnexistentId() {
        $this->controller->delete($this->model, 999);

        $this->assertFalse(isset($this->model->_table[$this->controller->short_name][999]), 'Deletion fail');
    }

    function testLoadById() {
        $this->controller->loadById($this->model, 6);

        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals($this->rows[6], $this->model->data);
        $this->assertEquals($this->rows[6], $this->model->get());
    }

    function testLoadByIdFail() {
        $this->controller->loadById($this->model, 999);

        $this->assertFalse($this->model->loaded(), 'Model must be unloaded');
        $this->assertEmpty($this->model->data);
        $this->assertEmpty($this->model->id);
    }

    function testLoadByConditions() {
        $this->model->addCondition('parent_id', '>', 100);
        
        $this->controller->loadByConditions($this->model);

        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals(6, $this->model->id);
        $this->assertEquals($this->rows[6], $this->model->get());
    }

    function testLoadByConditionsFail() {
        $this->model->addCondition('parent_id', '>', 1000);
        
        $this->controller->loadByConditions($this->model);

        $this->assertFalse($this->model->loaded(), 'Model must be unloaded');
        $this->assertEmpty($this->model->id);
        $this->assertEmpty($this->model->get());
    }

    function testDeleteAll() {
        $this->model->addCondition('parent_id', '>', 1);

        $this->controller->deleteAll($this->model);

        $this->assertEquals(array(9 => $this->rows[9]), $this->model->_table[$this->controller->short_name]);
    }

    function testDeleteAllFail() {
        $this->model->addCondition('parent_id', '<', 0);

        $this->controller->deleteAll($this->model);

        $this->assertEquals($this->rows, $this->model->_table[$this->controller->short_name]);
    }

    function testPrefetchAll() {
        $this->model->addCondition('parent_id', '>', 6);

        $this->controller->prefetchAll($this->model);

        $this->assertEquals(array(4, 6, 10), $this->model->_table[$this->controller->short_name]['__ids__']);
    }

    function testPrefetchAllFail() {
        $this->model->addCondition('parent_id', '>', 6000);

        $this->controller->prefetchAll($this->model);

        $this->assertEquals(array(), $this->model->_table[$this->controller->short_name]['__ids__']);
    }

    function testLoadCurrent() {
        $this->model->addCondition('parent_id', '>', 6);
        $this->controller->prefetchAll($this->model);

        $this->controller->loadCurrent($this->model);

        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals(4, $this->model->id);
        $this->assertEquals($this->rows[4], $this->model->data);
        $this->assertEquals($this->rows[4], $this->model->get());

        $this->controller->loadCurrent($this->model);

        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals(6, $this->model->id);
        $this->assertEquals($this->rows[6], $this->model->data);
        $this->assertEquals($this->rows[6], $this->model->get());

        $this->controller->loadCurrent($this->model);

        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals(10, $this->model->id);
        $this->assertEquals($this->rows[10], $this->model->data);
        $this->assertEquals($this->rows[10], $this->model->get());

        $this->controller->loadCurrent($this->model);

        $this->assertFalse($this->model->loaded(), 'Model must be unloaded');
        $this->assertEmpty($this->model->id);
        $this->assertEmpty($this->model->data);
        $this->assertEmpty($this->model->get());
    }

    function testGenerateNewIdInt() {
        $id = $this->controller->generateNewId($this->model);

        $this->assertEquals(11, $id);
    }

    function testGenerateNewIdString() {
        $this->model->id_field = 'field';
        $this->model->setControllerSource($this->rows);

        $id = $this->controller->generateNewId($this->model);

        $this->assertTrue(is_string($id), 'Id must be a string');
        $this->assertFalse(in_array($id, array_keys($this->model->_table[$this->controller->short_name])), 'Duplicate id');
    }

    function testGenerateNewIdInvalidType() {
        $this->model->id_field = 'parent_id';
        $this->model->setControllerSource($this->rows);

        $e = $this->assertThrowException('Exception_DB', $this->controller, 'generateNewId', array($this->model));
        $this->assertEquals('Unknown id type', $e->getMessage());
    }

    function testInsert() {
        $data = array(
            'parent_id' => 11.23,
            'field' => 'boh',
        );
        $this->model->set($data);

        $this->model->id = $this->controller->save($this->model, null, $this->model->data);

        $data['id'] = 11;
        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals($data['id'], $this->model->id);
        $this->assertEquals($data, $this->model->data);
        $this->assertEquals($data, $this->model->_table[$this->controller->short_name][11]);
    }

    function testInsertWithId() {
        $data = array(
            'parent_id' => 11.23,
            'field' => 'boh',
            'id' => 22,
        );
        $this->model->set($data);

        $this->model->id = $this->controller->save($this->model, null, $this->model->data);

        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals(22, $this->model->id);
        $this->assertEquals($data, $this->model->data);
        $this->assertEquals($data, $this->model->_table[$this->controller->short_name][22]);
    }

    function testInsertWithUsedId() {
        $data = array(
            'parent_id' => 11.23,
            'field' => 'boh',
            'id' => 4,
        );
        $this->model->set($data);

        $e = $this->assertThrowException('Exception_DB', $this->controller, 'save', array($this->model, null, $data));
        $this->assertEquals('This id is already used. Load the model before', $e->getMessage());
    }

    function testUpdate() {
        $data = array(
            'parent_id' => 11.23,
        );
        $this->model->load(4);
        $this->model->set($data);

        $this->model->id = $this->controller->save($this->model, 4, $this->model->data);

        $expected = array(
            'id' => 4,
            'parent_id' => 11.23,
            'field' => 'pluto'
        );
        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals(4, $this->model->id);
        $this->assertEquals($expected, $this->model->data);
        $this->assertEquals($expected, $this->model->_table[$this->controller->short_name][4]);
    }

    function testUpdateWithId() {
        $data = array(
            'parent_id' => 11.23,
            'id' => 5,
        );
        $this->model->load(4);
        $this->model->set($data);

        $this->model->id = $this->controller->save($this->model, 4, $this->model->data);

        $expected = array(
            'id' => 5,
            'parent_id' => 11.23,
            'field' => 'pluto'
        );
        $this->assertTrue($this->model->loaded(), 'Model must be loaded');
        $this->assertEquals(5, $this->model->id);
        $this->assertEquals($expected, $this->model->data);
        $this->assertEquals($expected, $this->model->_table[$this->controller->short_name][5]);

        $this->assertFalse(isset($this->model->_table[$this->controller->short_name][4]), 'This key must be deleted');
    }

    function testComplexCase() {
        $model = $this->add('Model_ComplexModel');
        $model->setControllerData('Array');
        $model->setControllerSource(Model_ComplexModel::$exampleSource);

        $model->load(1);

        $expected = array(
            'id' => 1,
            'title' => 'Title1',
            'description' => 'Description of Title1',
            'parent_id' => 6,
            'parent' => 'Title6',
        );
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($expected, $model->get());


        // insert
        $data = array(
            'id' => 9,
            'title' => 'Title9',
            'description' => 'Description od Title9',
            'parent_id' => 2
        );
        $model->set($data);
        $data['parent'] = 'Title2';

        $model->save();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals(9, $model->id);
        $this->assertEquals($data, $model->get());

        // update
        $model->set('parent_id', 7)
            ->set('id', 10);
        $model->save();

        $data['parent'] = 'Title7';
        $data['parent_id'] = 7;
        $data['id'] = 10;
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals(10, $model->id);
        $this->assertEquals($data, $model->get());

        $rows = $model->getRows();
        $n = 0;
        foreach ($model as $key => $row) {
            $this->assertTrue($model->loaded(), 'Model must be loaded');
            $this->assertEquals($key, $model->id);
            $this->assertEquals($rows[$n], $row);
            $this->assertEquals($row, $model->get());
            $this->assertTrue(isset($row['parent']), 'HasOne field not loaded');
            $n++;
        }
        $this->assertEquals(8, count($rows));
        $this->assertEquals(8, $n);

        $model->load(6);
        $model->delete();
        $this->assertFalse($model->loaded(), 'Model must be unloaded');

        $model->tryLoad(6);
        $this->assertFalse($model->loaded(), 'Model must be unloaded');

    }
}