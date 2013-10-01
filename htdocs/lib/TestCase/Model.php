<?php

/*
 * 
E questi qui ??? che fine fanno?? chiedere a romans
newField($name);
hasField($name);
getField($f);
 */

class TestCase_Model extends TestCase {
    function testAddField() {
        $model = $this->add('Model_TestModel');
        $fieldName = 'field_name';

        $field = $model->addField($fieldName);

        $this->assertTrue($field instanceof $model->field_class, 'Field is not a valid instance');
        $this->assertTrue(isset($model->elements[$fieldName]), 'Field not append to model\'s element');
    }

    function testAddTwiceField() {
        $model = $this->add('Model_TestModel');
        $fieldName = 'field_name';

        $field1 = $model->addField($fieldName);
        $field2 = $model->addField($fieldName);

        $this->assertEquals($fieldName, $field1->short_name);
        $this->assertNotEquals($fieldName, $field2->short_name);
    }

    function testSet() {
        $model = $this->add('Model_TestModel');
        $fieldName = 'field_name';
        $fieldValue = 'field_value';
        $field = $model->addField($fieldName);

        $model->set($fieldName, $fieldValue);

        $this->assertEquals($fieldValue, $model->data[$fieldName], 'Model set different value');
        $this->assertTrue($model->isDirty($fieldName), 'Field isn\'t set to dirty');
    }

    function testSetWithArray() {
        $model = $this->add('Model_TestModel');

        $model->set(array('field1' => 'value1', 'field2' => 'value2'));

        $this->assertEquals('value1', $model->data['field1'], 'Model set different value');
        $this->assertEquals('value2', $model->data['field2'], 'Model set different value');
        $this->assertTrue($model->isDirty('field1'), 'Field isn\'t set to dirty');
        $this->assertTrue($model->isDirty('field2'), 'Field isn\'t set to dirty');
    }

    function testSetInexistentField() {
        $model = $this->add('Model_TestModel', array('strict_fields' => true));

        $e = $this->assertThrowException('Exception_Logic', $model, 'set', array('inexistentField', 'value'));
        $this->assertEquals('inexistentField', $e->more_info['field']);
    }

    function testSetToSameValue() {
        $model = $this->add('Model_TestModel');
        $model->data['field1'] = 'value1';

        $model->set('field1', 'value1');

        $this->assertFalse($model->isDirty('field1'), 'Model field is dirty');
    }

    function testSetNullFieldStrict() {
        $model = $this->add('Model_TestModel', array('strict_fields' => true));
        $model->set(array('field1' => null, 'field2' => 2, 'field3' => 3));

        $this->assertTrue(array_key_exists('field1', $model->data), 'Model not set');
        $this->assertEquals(null, $model->data['field1']);
    }

    function testSetOnlyKey() {
        $model = $this->add('Model_TestModel');

        $this->assertThrowException('BaseException', $model, 'set', array('field1'));
    }

    function testGet() {
        $model = $this->add('Model_TestModel');
        $model->data['field1'] = 'value1';

        $this->assertEquals('value1', $model->get('field1'));
    }

    function testGetInesistentFieldNoStrict() {
        $model = $this->add('Model_TestModel');

        $value = $model->get('inexistentField');
        $this->assertEquals(null, $value);
    }

    function testGetInesistentFieldStrict() {
        $model = $this->add('Model_TestModel', array('strict_fields' => true));

        $e = $this->assertThrowException('Exception_Logic', $model, 'get', array('inexistentField'));
        $this->assertEquals('inexistentField', $e->more_info['field']);
    }

    function testGetNullFieldStrict() {
        $model = $this->add('Model_TestModel', array('strict_fields' => true));
        $model->set(array('field1' => null, 'field2' => 2, 'field3' => 3));

        $this->assertEquals(null, $model->get('field1'));
    }

    function testGetCalculated() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $expr = $model->addExpression('concatenate', 'Field_Concatenate');
        $expr->separator = ' ';

        $model->loadAny();

        $expected = array(
            'field1' => 'value1.2',
            'field2' => 'value2.2',
            'field3' => 'value3.2',
            'concatenate' => 'value1.2 value2.2',
        );
        $this->assertEquals($expected, $model->get());
    }

    function testGetOnlyCalculated() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $expr = $model->addExpression('concatenate', 'Field_Concatenate');
        $expr->separator = ' ';

        $model->loadAny();

        $this->assertEquals('value1.2 value2.2', $model->get('concatenate'));
    }

    function testGetDefaultValue() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->getElement('field2')->defaultValue('defaultValue');

        $this->assertEquals('defaultValue', $model->get('field2'));
    }

    function testGetNoDefaultValueStrictMode() {
        $model = $this->add('Model_TestModel', array('strict_fields' => true));
        $model->setControllerSource(self::$exampleData);

        $this->assertThrowException('BaseException', $model, 'get', array('field2'));
    }

    function testArrayAccess() {
        $model = $this->add('Model_TestModel');
        $model->set('field1', 'value1111');

        $this->assertEquals('value1111', $model['field1']);

        $model['field2'] = 'value2222';
        $this->assertEquals('value2222', $model->data['field2']);

        $this->assertTrue(isset($model['field2']), 'Expected true on isset');
        $this->assertFalse(isset($model['field3']), 'Expected false on isset');

        $model->getElement('field3')->defaultValue('defaultValue');
        $this->assertTrue(isset($model['field3']), 'Expected true on isset');

        $model->set('field3', 'value3333');
        $this->assertEquals('value3333', $model['field3']);

        unset($model['field3']);
        $this->assertEquals('defaultValue', $model->get('field3'));
    }

    function testGetTitleField() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $this->assertEquals('field3', $model->getTitleField());
    }

    function testGetTitleFieldNull() {
        $model = $this->add('Model_TestModel', array('title_field' => null));
        $model->setControllerSource(self::$exampleData);

        $this->assertEquals('field1', $model->getTitleField());
    }

    function testGetActualFields() {
        $model = $this->add('Model_TestModel');

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2', 'field3');
        $this->assertEquals($expected, $actualFields);
    }

    function testGetSetActualFields() {
        $model = $this->add('Model_TestModel');
        $expected = array('field1', 'field3');
        $model->setActualFields($expected);

        $actualFields = $model->getActualFields();

        $this->assertEquals($expected, $actualFields);   
    }

    function testSetAllActualFields() {
        $model = $this->add('Model_TestModel');
        $model->setActualFields();

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2', 'field3');
        $this->assertEquals($expected, $actualFields);
    }

    function testGroupActualFields() {
        $model = $this->add('Model_TestModel');
        $model->setActualFields('group1');

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2');
        $this->assertEquals($expected, $actualFields);
    }

    function testCommaSeparatedGroupActualFields() {
        $model = $this->add('Model_TestModel');
        $model->addField('field4')->group('group3');
        $model->setActualFields('group1,group3');

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2', 'field4');
        $this->assertEquals($expected, $actualFields);
    }

    function testExpludeGroupActualFields() {
        $model = $this->add('Model_TestModel');
        $model->addField('field4')->group('group3');
        $model->setActualFields('all,-group3');

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2', 'field3');
        $this->assertEquals($expected, $actualFields);
    }

    function testSetSource() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $this->assertTrue($model->controller instanceof Controller_Data, 'Controller wrong type');
    }

    function testSetSourceInvalidController() {
        $model = $this->add('Model_TestModel');

        $this->assertThrowException('BaseException', $model, 'setSource', array($this, self::$exampleData));
    }

    function testSetControllerSource() {
        $model = $this->add('Model_TestModel');
        $model->controller = null;

        $this->assertThrowException('BaseException', $model, 'setControllerSource', array(array()));
    }

    function testLoad() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->load('value1.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value1.1', $model->id);
    }

    function testLoadFail() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'load', array('inexistentValue'));
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testLoadHooks() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->load('value1.1');

        $this->assertEquals(2, $tmp);
    }

    function testLoadHooksFail() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $this->assertThrowException('BaseException', $model, 'load', array('inexistentValue'));
        $this->assertEquals(1, $tmp);
    }

    function testLoadLoadedModel() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->id = 'value1.2';

        $model->load('value1.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value1.1', $model->id);
    }

    function testLoadDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->load('value1.2');

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testLoadFailDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $model->tryLoad('value1.2');

        $this->assertThrowException('BaseException', $model, 'load', array('inexistentValue'));
        $this->assertTrue($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testTryLoad() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->tryLoad('value1.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value1.1', $model->id);
    }

    function testTryLoadFail() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->controller->foundOnLoad = false;

        $model->tryLoad('inexistentId');

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
        $this->assertEquals(null, $model->id);
    }

    function testTryLoadLoadedModel() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->id = 'value1.2';

        $model->tryLoad('value1.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value1.1', $model->id);
    }

    function testTryLoadLoadedModelFail() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->id = 'value1.2';
        $model->controller->foundOnLoad = false;

        $model->tryLoad('inexistentId');

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
        $this->assertEquals(null, $model->id);
    }

    function testTryLoadHook() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoad('value1.1');

        $this->assertEquals(2, $tmp);
    }

    function testTryLoadHookFail() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoad('inexistentId');

        $this->assertEquals(1, $tmp);
    }

    function testTryLoadDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->tryLoad('value1.2');

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testTryLoadFailDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $model->tryLoad('value1.2');

        $this->assertEquals('value11.11', $model->get('field1'));
        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }

    function testLoadAny() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->loadAny();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testLoadAnyFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'loadAny');
        $this->assertEquals(null, $model->id);
    }

    function testLoadAnyLoadedModel() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->id = 'value1.2';

        $model->loadAny();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testLoadAnyLoadedModelFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $model->id = 'value1.2';

        $this->assertThrowException('BaseException', $model, 'loadAny');
        $this->assertEquals(null, $model->id);
    }

    function testLoadAnyHooks() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = array();
        $self = $this;
        $model->addHook('beforeLoad',
            function() use (&$tmp, $self) {
                $args = func_get_args();
                $self->assertEquals('loadAny', $args[1]);
                $self->assertEquals(array(), $args[2]);
                $tmp[] = 'beforeLoad';
        });
        $model->addHook('afterLoad',
            function() use (&$tmp, $self) {
                $args = func_get_args();
                $self->assertEquals(1, count($args));
                $tmp[] = 'afterLoad';
        });

        $model->loadAny();

        $this->assertEquals(array('beforeLoad', 'afterLoad'), $tmp);
    }

    function testLoadAnyHooksFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $tmp = array();
        $self = $this;
        $model->addHook('beforeLoad',
            function() use (&$tmp, $self) {
                $args = func_get_args();
                $self->assertEquals('loadAny', $args[1]);
                $self->assertEquals(array(), $args[2]);
                $tmp[] = 'beforeLoad';
        });
        $model->addHook('afterLoad',
            function() use (&$tmp, $self) {
                $args = func_get_args();
                $self->assertEquals(1, count($args));
                $tmp[] = 'afterLoad';
        });

        $this->assertThrowException('BaseException', $model, 'loadAny');
        $this->assertEquals(array('beforeLoad'), $tmp);
    }

    function testLoadAnyDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->loadAny();

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testLoadAnyFailDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'loadAny');
        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }

    function testTryLoadAny() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->tryLoadAny();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testTryLoadAnyFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;

        $model->tryLoadAny();

        $this->assertEquals(null, $model->id);
    }

    function testTryLoadAnyLoadedModel() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->id = 'value1.2';

        $model->tryLoadAny();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testTryLoadAnyLoadedModelFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $model->id = 'value1.2';

        $model->tryLoadAny();

        $this->assertEquals(null, $model->id);
    }

    function testTryLoadAnyHooks() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoadAny();

        $this->assertEquals(2, $tmp);
    }

    function testTryLoadAnyHooksFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoadAny();

        $this->assertEquals(1, $tmp);
    }


    function testTryLoadAnyDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->tryLoadAny();

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testTryLoadAnyFailDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $model->tryLoadAny();

        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }

    function testLoadBy() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->loadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testLoadByFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'loadBy', array('field1', '=', 'inexisten'));
        $this->assertEquals(null, $model->id);
    }

    function testLoadByLoadedModel() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->id = 'value1.2';

        $model->loadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testLoadByLoadedModelFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $model->id = 'value1.2';

        $this->assertThrowException('BaseException', $model, 'loadBy', array('field1', '=', 'inexisten'));
        $this->assertEquals(null, $model->id);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testLoadByHooks() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->loadBy('field2', '=', 'value2.1');

        $this->assertEquals(2, $tmp);
    }

    function testLoadByHooksFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $this->assertThrowException('BaseException', $model, 'loadBy', array('field1', '=', 'inexisten'));
        $this->assertEquals(1, $tmp);
    }

    function testLoadByDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->loadBy('field2', '=', 'value2.1');

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testLoadByFailDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'loadBy', array('field1', '=', 'inexisten'));
        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }

    function testTryLoadBy() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testTryLoadByFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;

        $model->tryLoadBy('field1', '=', 'inexisten');

        $this->assertEquals(null, $model->id);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testTryLoadByLoadedModel() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->id = 'value1.2';

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testTryLoadByLoadedModelFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $model->id = 'value1.2';

        $model->tryLoadBy('field1', '=', 'inexisten');

        $this->assertEquals(null, $model->id);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testTryLoadByArgument() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->tryLoadBy('field2', 'value2.2');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value2.2', $model->get('field2'));
    }

    function testTryLoadByHooks() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertEquals(2, $tmp);
    }

    function testTryLoadByHooksFail() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertEquals(1, $tmp);
    }

    function testTryLoadByDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testTryLoadByFailDirty() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }


    function testUnload() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', self::$exampleData, 'value1.1');

        $model->unload();

        $this->assertFalse($model->loaded(), 'Model is already loaded');
        $this->assertEquals(null, $model->id, 'Id is already set');
    }

    function testUnloadHooks() {
        $model = $this->add('Model_TestModel');
        $model->setSource('Foo', self::$exampleData, 'value1.1');
        $tmp = 0;
        $model->addHook('beforeUnload,afterUnload', function() use (&$tmp) { $tmp += 1; });

        $model->unload();
        
        $this->assertEquals(2, $tmp);
    }

    function testUnloadHooksNotLoaded() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeUnload,afterUnload', function() use (&$tmp) { $tmp += 1; });

        $model->unload();
        
        $this->assertEquals(1, $tmp);
    }

    function testInsert() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $fields = array(
            'field1' => 'newValue1',
            'field2' => 'newValue2',
        );
        $model->set($fields)->save('newValue1');

        $this->assertEquals($fields, $model->get());
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEmpty($model->dirty);
    }

    function testUpdate() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->load('value1.1');

        $fields = array(
            'field2' => 'newValue2',
        );
        $model->set($fields)->save('newValue1');

        $this->assertEquals($fields['field2'], $model->get('field2'));
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEmpty($model->dirty);
    }

    function testSaveAndUnload() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->load('value1.1');

        $fields = array(
            'field2' => 'newValue2',
        );
        $model->set($fields)->saveAndUnload('newValue1');

        $this->assertEmpty($model->data);
        $this->assertFalse($model->loaded(), 'Model must be unloaded');
        $this->assertEmpty($model->dirty);
    }

    function testInsertHook() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeSave,beforeUpdate,beforeInsert,afterUpdate,afterInsert,afterSave', function() use (&$tmp) { $tmp += 1; });
        $tmp1 = 0;
        $model->addHook('beforeSave,beforeInsert,afterInsert,afterSave', function() use (&$tmp1) { $tmp1 += 1; });


        $fields = array(
            'field2' => 'newValue2',
        );
        $model->set($fields)->save();

        $this->assertEquals(4, $tmp);
        $this->assertEquals(4, $tmp1);
    }

    function testUpdateHook() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeSave,beforeUpdate,beforeInsert,afterUpdate,afterInsert,afterSave', function() use (&$tmp) { $tmp += 1; });
        $tmp1 = 0;
        $model->addHook('beforeSave,beforeUpdate,afterUpdate,afterSave', function() use (&$tmp1) { $tmp1 += 1; });
        
        $fields = array(
            'field2' => 'newValue2',
        );
        $model->set($fields)->save();

        $this->assertEquals(4, $tmp);
    }

    function testDelete() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->delete('value1.1');

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteModelLoaded() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->load('value1.1');

        $model->delete();

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteModelLoadedWithId() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->load('value1.1');

        $this->assertThrowException('BaseException', $model, 'delete', array('value1.2'));
        $this->assertTrue($model->loaded(), 'Model must not loaded');
    }

    function testDeleteModelLoadedWithSameId() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->load('value1.1');

        $model->delete('value1.1');

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteNothing() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $this->assertThrowException('BaseException', $model, 'delete');
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteAll() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = array();
        $model->addHook('beforeDeleteAll',
            function() use (&$tmp) {
                $tmp[] = 'beforeDeleteAll';
        });
        $model->addHook('afterDeleteAll',
            function() use (&$tmp) {
                $tmp[] = 'afterDeleteAll';
        });

        $model->deleteAll();

        $this->assertEquals(array('beforeDeleteAll', 'afterDeleteAll'), $tmp);
    }

    function testDeleteAllLoaded() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->load('value1.1');
        $tmp = array();
        $model->addHook('beforeDeleteAll',
            function() use (&$tmp) {
                $tmp[] = 'beforeDeleteAll';
        });
        $model->addHook('afterDeleteAll',
            function() use (&$tmp) {
                $tmp[] = 'afterDeleteAll';
        });

        $model->deleteAll();

        $this->assertEquals(array('beforeDeleteAll', 'afterDeleteAll'), $tmp);
    }

    function testReload() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->load('value1.1');
        $oldValue = $model->get('field1');
        $model->set('field1', 'value1111');

        $model->reload();

        $this->assertEquals($oldValue, $model->get('field1'));
        $this->assertTrue($model->loaded(), 'Model must be loaded');
    }

    function testReloadUnloadedModel() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $this->assertThrowException('BaseException', $model, 'reload');
    }

    function testIterable() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        foreach($model as $n => $row) {
            $this->assertTrue($model->loaded(), 'Model must be loaded');
            $this->assertEquals($row, $model->data);
            $this->assertEquals(self::$exampleData[$n], $row);
        }
        $this->assertEquals(4, $model->controller->next);
        $this->assertEquals(1, $model->controller->rewind);
    }

    function testIterableLoaded() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->load('value1.1');

        foreach($model as $n => $row) {
            $this->assertTrue($model->loaded(), 'Model must be loaded');
            $this->assertEquals($row, $model->data);
            $this->assertEquals(self::$exampleData[$n], $row);
        }
        $this->assertEquals(4, $model->controller->next);
        $this->assertEquals(1, $model->controller->rewind);
    }

    function testIterableHook() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $tmp = array();
        $self = $this;
        $model->addHook('beforeLoad',
            function() use (&$tmp, $self) {
                $args = func_get_args();
                $self->assertEquals('iterating', $args[1]);
                $self->assertEquals(null, $args[2]);
                $tmp[] = 'beforeLoad';
        });
        $model->addHook('afterLoad',
            function() use (&$tmp, $self) {
                $args = func_get_args();
                $self->assertEquals(1, count($args));
                $tmp[] = 'afterLoad';
        });

        foreach($model as $n => $row) { }

        $this->assertEquals(7, count($tmp));
        $this->assertEquals('beforeLoad', $tmp[0]);
        $this->assertEquals('afterLoad', $tmp[1]);
        $this->assertEquals('beforeLoad', $tmp[2]);
        $this->assertEquals('afterLoad', $tmp[3]);
        $this->assertEquals('beforeLoad', $tmp[4]);
        $this->assertEquals('afterLoad', $tmp[5]);
        $this->assertEquals('beforeLoad', $tmp[6]);

    }

    function testAddCondition() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->addCondition('field1', 'value1.1');

        $this->assertTrue(isset($model->conditions['field1']), 'Model doesn\'t store condition');
        $this->assertEquals(1, count($model->conditions['field1']));
        $this->assertEquals(array('field1', '=', 'value1.1'), $model->conditions['field1'][0]);
    }

    function testAddConditionArray() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->addCondition(array(
            array('field1', 'value1.1'),
            array('field1', '>', 'value2.1')));

        $this->assertTrue(isset($model->conditions['field1']), 'Model doesn\'t store condition');
        $this->assertEquals(2, count($model->conditions['field1']));
        $this->assertEquals(array('field1', '=', 'value1.1'), $model->conditions['field1'][0]);
        $this->assertEquals(array('field1', '>', 'value2.1'), $model->conditions['field1'][1]);
    }

    function testAddConditionOnlyKey() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $this->assertThrowException('BaseException', $model, 'addCondition', array('field1'));

        $this->assertEquals(0, count($model->conditions));
    }

    function testAddConditionUnsupported() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->controller->supportConditions = false;

        $this->assertThrowException('BaseException', $model, 'addCondition', array('field1', 'value1.1'));
    }

    function testSetLimit() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->setLimit(3, 45);

        $this->assertEquals(array(3, 45), $model->limit);
    }

    function testDefaultLimit() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $this->assertEquals(array(null, null), $model->limit);
    }

    function testSetLimitUnsupported() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->controller->supportLimit = false;

        $this->assertThrowException('BaseException', $model, 'setLimit', array(4));
    }

    function testSetOrder() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->setOrder('field1', 'desc');

        $this->assertEquals(array('field1', 'desc'), $model->order);
    }

    function testDefaultOrder() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $this->assertEquals(array(null, null), $model->order);
    }

    function testSetOrderUnsupported() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->controller->supportOrder = false;

        $this->assertThrowException('BaseException', $model, 'setOrder', array('field1'));
    }

    function testCount() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $count = $model->count();
        $this->assertEquals(3, $count);
    }

    function testCountBuiltin() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $count = count($model);
        $this->assertEquals(3, $count);
    }

    function testCountUnsupported() {
        $model = $this->add('Model_TestModel');
        $model->setControllerData('Empty');

        $this->assertThrowException('BaseException', $model, 'count');
    }

    function testEachWithString() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->each('eachFunction');

        $this->assertEquals(3, count($model->eachArguments));
        foreach ($model->eachArguments as $arg) {
            $this->assertEmpty($arg);
        }
    }

    function testEachWithCallable() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->each(array($model, 'eachFunction'));

        $this->assertEquals(3, count($model->eachArguments));
        foreach ($model->eachArguments as $arg) {
            $this->assertEquals(1, count($arg));
            $this->assertEquals($model, $arg[0]);
        }
    }

    function testEachWithCallableBreak() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->stopAt = 3;

        $model->each(array($model, 'eachFunction'));

        $this->assertEquals(2, count($model->eachArguments));
        foreach ($model->eachArguments as $arg) {
            $this->assertEquals(1, count($arg));
            $this->assertEquals($model, $arg[0]);
        }
    }

    function testHasMany() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->hasMany('Model_TestModel');

        $expected = array(
            'Model_TestModel' => array(
                'Model_TestModel',
                UNDEFINED,
                UNDEFINED,
        ));
        $this->assertEquals($expected, $model->_references);
    }

    function testHasManyAllParamters() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->hasMany('Model_TestModel', 'other_field', 'my_field', 'reference_name');

        $expected = array(
            'reference_name' => array(
                'Model_TestModel',
                'other_field',
                'my_field',
        ));
        $this->assertEquals($expected, $model->_references);
    }

    function testHasManyRefNotLoaded() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->hasMany('Model_TestModel', 'field1', 'field2', 'parent');

        $referencedModel = $model->ref('parent');

        $this->assertTrue($referencedModel instanceof Model_TestModel, 'Return a wrong model type');
        $expected = array(
            'field1' => array(
                array('field1', '=', null)
            )
        );
        $this->assertEquals($expected, $referencedModel->conditions);
    }

    function testHasManyRefLoaded() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $model->hasMany('Model_TestModel', 'field1', 'field2', 'parent');
        $model->load('value2.1');

        $referencedModel = $model->ref('parent');

        $this->assertTrue($referencedModel instanceof Model_TestModel, 'Return a wrong model type');
        $expected = array(
            'field1' => array(
                array('field1', '=', $model->get('field2'))
        ));
        $this->assertEquals($expected, $referencedModel->conditions);
    }

    function testHasOne() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->hasOne('Model_TestModel');

        $expected = array(
            'model_testmodel_id' => get_class($model)
        );
        $this->assertEquals($expected, $model->_references);
    }

    function testHasOneAllParameter() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $model->hasOne('Model_TestModel', 'field2');

        $expected = array(
            'field2' => get_class($model)
        );
        $this->assertEquals($expected, $model->_references);
    }

    function testHasOneNewField() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $field = $model->hasOne('Model_TestModel', 'field4');

        $this->assertTrue($model->getElement('field4') instanceof Field, 'Model doesn\'t create new field');
        $this->assertTrue(is_string($field->getModel()), 'Model field hasn\'t model');
    }

    function testHasOneRefNoLoaded() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $field = $model->hasOne('Model_TestModel', 'parent_id');

        $referencedModel = $model->ref('parent_id');

        $this->assertTrue($referencedModel instanceof Model_TestModel, 'Returned a wrong model type');
        $this->assertFalse($referencedModel->loaded(), 'Expected a unloaded model');
    }

    function testHasOneRefNoLoaded2() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);
        $field = $model->hasOne('Model_TestModel', 'parent_id');
        $model->set('parent_id', 'value1.3');
        $data = self::$exampleData;
        $model->addHook('beforeRefLoad', function($model, $refModel, $id) use ($data) {
            $refModel->setControllerSource($data);
        });

        $referencedModel = $model->ref('parent_id');

        $this->assertTrue($referencedModel instanceof Model_TestModel, 'Returned a wrong model type');
        $this->assertTrue($referencedModel->loaded(), 'Expected a unloaded model');
        $this->assertEquals('value1.3', $referencedModel->id);
    }

    function testRefFail() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $e = $this->assertThrowException('BaseException', $model, 'ref', array('UnexistentField'));
        $this->assertEquals($model, $e->more_info['model']);
        $this->assertEquals('UnexistentField', $e->more_info['ref']);
        $this->assertFalse($model->hasElement('UnexistentField'), 'Model have UnexistentField');
    }

    function testHasOneForeignField() {
        $model = $this->add('Model_TestModel');
        $data = self::$exampleData;
        $data[0]['parent_id'] = 'value1.2';
        $data[1]['parent_id'] = 'value1.3';
        $data[2]['parent_id'] = 'value1.1';
        $model->setControllerSource($data);
        $hasOneField = $model->hasOne('Model_TestModel', 'parent_id');
        $hasOneField->addHook('beforeForeignLoad', function($field, $model, $id) use ($data) {
            $model->setControllerSource($data);
        });

        $model->load('value1.1');

        $this->assertEquals($data[0]['parent_id'], $model->get('parent_id'));
        $this->assertEquals($data[1]['field3'], $model->get('parent'));
    }

    function testGetRows() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $rows = $model->getRows();

        foreach($model as $n => $row) {
            $this->assertEquals($row, $rows[$n]);
        }
    }

    function testGetRowsWithCalculated() {
        $model = $this->add('Model_ComplexModel');

        $rows = $model->getRows();

        $expected = Model_ComplexModel::$exampleSource;
        foreach($model as $n => $row) {
            $this->assertTrue(isset($rows[$n]['parent']), 'Calculated field not valued');
            unset($rows[$n]['parent']);
            $this->assertEquals($expected[$n], $rows[$n]);
        }
    }

    function testGetRowsWithArgument() {
        $model = $this->add('Model_TestModel');
        $model->setControllerSource(self::$exampleData);

        $rows = $model->getRows(array('field1', 'field2'));

        foreach($rows as $row) {
            $this->assertTrue(isset($row['field1']), 'Expected a valued field1');
            $this->assertTrue(isset($row['field2']), 'Expected a valued field2');
            $this->assertFalse(array_key_exists('field3', $row), 'field3 must be not returned');
        }
    }

    function testClone() {
        $model = $this->add('Model_ComplexModel');
        $model->addCondition('id', '>', 4);
        $model->setOrder('title');
        $model->setLimit(2, 4);
        $clonedModel = clone $model;
        $model->addField('field4');
        $model->addCondition('id', '>', 6);
        $model->setOrder('description');
        $model->setLimit(1, 3);

        $this->assertEquals($model->owner, $clonedModel->owner);
        $this->assertEquals(1, count($clonedModel->conditions));
        $this->assertEquals(array('title', null), $clonedModel->order);
        $this->assertEquals(array(2, 4), $clonedModel->limit);
        $this->assertFalse($clonedModel->hasElement('field4'), 'Copied a field addded after the cloning...');
        $this->assertEquals(array_keys($model->_expressions), array_keys($clonedModel->_expressions));
    }

    function testReset() {
        $model = $this->add('Model_ComplexModel');
        $model->addCondition('id', '>', 4);
        $model->setOrder('title');
        $model->setLimit(2, 4);
        $model->loadAny();

        $model->reset();

        $this->assertEmpty($model->conditions);
        $this->assertEmpty($model->order);
        $this->assertEmpty($model->limit);
        $this->assertEmpty($model->data);
        $this->assertEmpty($model->_table);
        $this->assertEmpty($model->id);
    }

    function testComplexExample() {
        $model = $this->add('Model_ComplexModel');

        $model->load(1);
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals(6, $model->get('parent_id'));
        $this->assertEquals('Title6', $model->get('parent'));

        $parentModel = $model->ref('parent_id');
        $this->assertTrue($parentModel->loaded(), 'Model must be loaded');
        $this->assertEquals(6, $parentModel->id);
        $this->assertEquals($model->get('parent_id'), $parentModel->id);
        $this->assertEquals('Title6', $parentModel->get('title'));
        $this->assertEquals(5, $parentModel->get('parent_id'));
        $this->assertEquals('Title5', $parentModel->get('parent'));

        $gFatherModel = $model->ref('parent_id/parent_id');
        $this->assertTrue($gFatherModel->loaded(), 'Model must be loaded');
        $this->assertEquals(5, $gFatherModel->id);
        $this->assertEquals($parentModel->get('parent_id'), $gFatherModel->id);
        $this->assertEquals('Title5', $gFatherModel->get('title'));
        $this->assertEquals(4, $gFatherModel->get('parent_id'));
        $this->assertEquals('Title4', $gFatherModel->get('parent'));

        $childModel = $gFatherModel->ref('Model_ComplexModel');
        $this->assertFalse($childModel->loaded(), 'Model must be not loaded');
        // The Controller_DataFoo controller don't really load the real row
        $childModel->loadAny();
        $this->assertEquals(2, $childModel->id);
        $expected = array(
            'parent_id' => array(array('parent_id', '=', $gFatherModel->id))
        );
        $this->assertEquals($expected, $childModel->conditions);

        $this->assertThrowException('BaseException', $gFatherModel, 'ref', array('Model_ComplexModel/Model_ComplexModel'));
    }

    static private $exampleData = array(
        array('field1' => 'value1.1', 'field2' => 'value2.1', 'field3' => 'value3.1'),
        array('field1' => 'value1.2', 'field2' => 'value2.2', 'field3' => 'value3.2'),
        array('field1' => 'value1.3', 'field2' => 'value2.3', 'field3' => 'value3.3'),
    );
}
