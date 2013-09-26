<?php

/*
 INCOERERNZE
  - se la set e la get sono in strict_mode le more info hanno 2 chiavi diverse per accedere al nome del field
  - il Controller_Data dpovrebbe avere un trySave oppure una save? perchè a fine save c'è il check loaded()
  - save con id?
  - delete con id?
*/

class TestCase_Model extends TestCase {
    function testAddField() {
        $model = $this->add('TestModel');
        $fieldName = 'field_name';

        $field = $model->addField($fieldName);

        $this->assertTrue($field instanceof $model->field_class, 'Field is not a valid instance');
        $this->assertTrue(isset($model->elements[$fieldName]), 'Field not append to model\'s element');
    }

    function testAddTwiceField() {
        $model = $this->add('TestModel');
        $fieldName = 'field_name';

        $field1 = $model->addField($fieldName);
        $field2 = $model->addField($fieldName);

        $this->assertEquals($fieldName, $field1->short_name);
        $this->assertNotEquals($fieldName, $field2->short_name);
    }

    function testSet() {
        $model = $this->add('TestModel');
        $fieldName = 'field_name';
        $fieldValue = 'field_value';
        $field = $model->addField($fieldName);

        $model->set($fieldName, $fieldValue);

        $this->assertEquals($fieldValue, $model->data[$fieldName], 'Model set different value');
        $this->assertTrue($model->isDirty($fieldName), 'Field isn\'t set to dirty');
    }

    function testSetWithArray() {
        $model = $this->add('TestModel');

        $model->set(array('field1' => 'value1', 'field2' => 'value2'));

        $this->assertEquals('value1', $model->data['field1'], 'Model set different value');
        $this->assertEquals('value2', $model->data['field2'], 'Model set different value');
        $this->assertTrue($model->isDirty('field1'), 'Field isn\'t set to dirty');
        $this->assertTrue($model->isDirty('field2'), 'Field isn\'t set to dirty');
    }

    function testSetInexistentField() {
        $model = $this->add('TestModel', array('strict_fields' => true));

        $e = $this->assertThrowException('Exception_Logic', $model, 'set', array('inexistentField', 'value'));
        $this->assertEquals('inexistentField', $e->more_info['field']);
    }

    function testSetToSameValue() {
        $model = $this->add('TestModel');
        $model->data['field1'] = 'value1';

        $model->set('field1', 'value1');

        $this->assertFalse($model->isDirty('field1'), 'Model field is dirty');
    }

    function testSetNullFieldStrict() {
        $model = $this->add('TestModel', array('strict_fields' => true));
        $model->set(array('field1' => null, 'field2' => 2, 'field3' => 3));

        $this->assertTrue(array_key_exists('field1', $model->data), 'Model not set');
        $this->assertEquals(null, $model->data['field1']);
    }

    function testGet() {
        $model = $this->add('TestModel');
        $model->data['field1'] = 'value1';

        $this->assertEquals('value1', $model->get('field1'));
    }

    function testGetInesistentFieldNoStrict() {
        $model = $this->add('TestModel');

        $value = $model->get('inexistentField');
        $this->assertEquals(null, $value);
    }

    function testGetInesistentFieldStrict() {
        $model = $this->add('TestModel', array('strict_fields' => true));

        $e = $this->assertThrowException('Exception_Logic', $model, 'get', array('inexistentField'));
        $this->assertEquals('inexistentField', $e->more_info['field']);
    }

    function testGetNullFieldStrict() {
        $model = $this->add('TestModel', array('strict_fields' => true));
        $model->set(array('field1' => null, 'field2' => 2, 'field3' => 3));

        $this->assertEquals(null, $model->get('field1'));
    }

    function testGetActualFields() {
        $model = $this->add('TestModel');

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2', 'field3');
        $this->assertEquals($expected, $actualFields);
    }

    function testGetSetActualFields() {
        $model = $this->add('TestModel');
        $expected = array('field1', 'field3');
        $model->setActualFields($expected);

        $actualFields = $model->getActualFields();

        $this->assertEquals($expected, $actualFields);   
    }

    function testGroupActualFields() {
        $model = $this->add('TestModel');
        $model->setActualFields('group1');

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2');
        $this->assertEquals($expected, $actualFields);
    }

    function testCommaSeparatedGroupActualFields() {
        $model = $this->add('TestModel');
        $model->addField('field4')->group('group3');
        $model->setActualFields('group1,group3');

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2', 'field4');
        $this->assertEquals($expected, $actualFields);
    }

    function testExpludeGroupActualFields() {
        $model = $this->add('TestModel');
        $model->addField('field4')->group('group3');
        $model->setActualFields('all,-group3');

        $actualFields = $model->getActualFields();

        $expected = array('field1', 'field2', 'field3');
        $this->assertEquals($expected, $actualFields);
    }

    function testSetSource() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $this->assertTrue($model->controller instanceof Controller_Data, 'Controller wrong type');
    }

    function testSetSourceInvalidController() {
        $model = $this->add('TestModel');

        $this->assertThrowException('BaseException', $model, 'setSource', array($this, self::$exampleData));
    }

    function testLoad() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->load('value1.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value1.1', $model->id);
    }

    function testLoadFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'load', array('inexistentValue'));
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testLoadHooks() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->load('value1.1');

        $this->assertEquals(2, $tmp);
    }

    function testLoadHooksFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $this->assertThrowException('BaseException', $model, 'load', array('inexistentValue'));
        $this->assertEquals(1, $tmp);
    }

    function testLoadLoadedModel() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->id = 'value1.2';

        $model->load('value1.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value1.1', $model->id);
    }

    function testLoadDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->load('value1.2');

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testLoadFailDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $model->tryLoad('value1.2');

        $this->assertThrowException('BaseException', $model, 'load', array('inexistentValue'));
        $this->assertTrue($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testTryLoad() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->tryLoad('value1.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value1.1', $model->id);
    }

    function testTryLoadFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->controller->foundOnLoad = false;

        $model->tryLoad('inexistentId');

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
        $this->assertEquals(null, $model->id);
    }

    function testTryLoadLoadedModel() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->id = 'value1.2';

        $model->tryLoad('value1.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value1.1', $model->id);
    }

    function testTryLoadLoadedModelFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->id = 'value1.2';
        $model->controller->foundOnLoad = false;

        $model->tryLoad('inexistentId');

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
        $this->assertEquals(null, $model->id);
    }

    function testTryLoadHook() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoad('value1.1');

        $this->assertEquals(2, $tmp);
    }

    function testTryLoadHookFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoad('inexistentId');

        $this->assertEquals(1, $tmp);
    }

    function testTryLoadDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->tryLoad('value1.2');

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testTryLoadFailDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $model->tryLoad('value1.2');

        $this->assertEquals('value11.11', $model->get('field1'));
        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }

    function testLoadAny() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->loadAny();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testLoadAnyFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'loadAny');
        $this->assertEquals(null, $model->id);
    }

    function testLoadAnyLoadedModel() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->id = 'value1.2';

        $model->loadAny();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testLoadAnyLoadedModelFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $model->id = 'value1.2';

        $this->assertThrowException('BaseException', $model, 'loadAny');
        $this->assertEquals(null, $model->id);
    }

    function testLoadAnyHooks() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
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
        $model = $this->add('TestModel');
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
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->loadAny();

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testLoadAnyFailDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'loadAny');
        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }

    function testTryLoadAny() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->tryLoadAny();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testTryLoadAnyFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;

        $model->tryLoadAny();

        $this->assertEquals(null, $model->id);
    }

    function testTryLoadAnyLoadedModel() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->id = 'value1.2';

        $model->tryLoadAny();

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testTryLoadAnyLoadedModelFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $model->id = 'value1.2';

        $model->tryLoadAny();

        $this->assertEquals(null, $model->id);
    }

    function testTryLoadAnyHooks() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoadAny();

        $this->assertEquals(2, $tmp);
    }

    function testTryLoadAnyHooksFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoadAny();

        $this->assertEquals(1, $tmp);
    }


    function testTryLoadAnyDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->tryLoadAny();

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testTryLoadAnyFailDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $model->tryLoadAny();

        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }

    function testLoadBy() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->loadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testLoadByFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'loadBy', array('field1', '=', 'inexisten'));
        $this->assertEquals(null, $model->id);
    }

    function testLoadByLoadedModel() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->id = 'value1.2';

        $model->loadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testLoadByLoadedModelFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $model->id = 'value1.2';

        $this->assertThrowException('BaseException', $model, 'loadBy', array('field1', '=', 'inexisten'));
        $this->assertEquals(null, $model->id);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testLoadByHooks() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->loadBy('field2', '=', 'value2.1');

        $this->assertEquals(2, $tmp);
    }

    function testLoadByHooksFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $this->assertThrowException('BaseException', $model, 'loadBy', array('field1', '=', 'inexisten'));
        $this->assertEquals(1, $tmp);
    }

    function testLoadByDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->loadBy('field2', '=', 'value2.1');

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testLoadByFailDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $this->assertThrowException('BaseException', $model, 'loadBy', array('field1', '=', 'inexisten'));
        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }

    function testTryLoadBy() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testTryLoadByFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;

        $model->tryLoadBy('field1', '=', 'inexisten');

        $this->assertEquals(null, $model->id);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testTryLoadByLoadedModel() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->id = 'value1.2';

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertNotEmpty($model->id);
    }

    function testTryLoadByLoadedModelFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $model->id = 'value1.2';

        $model->tryLoadBy('field1', '=', 'inexisten');

        $this->assertEquals(null, $model->id);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testTryLoadByArgument() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->tryLoadBy('field2', 'value2.2');

        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals('value2.2', $model->get('field2'));
    }

    function testTryLoadByHooks() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertEquals(2, $tmp);
    }

    function testTryLoadByHooksFail() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', array());
        $model->controller->foundOnLoad = false;
        $tmp = 0;
        $model->addHook('beforeLoad,afterLoad', function() use (&$tmp) { $tmp += 1; });

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertEquals(1, $tmp);
    }

    function testTryLoadByDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertEquals('value1.2', $model->get('field1'));
        $this->assertFalse($model->isDirty('field1'), 'Load don\'t erase dirty array');
    }

    function testTryLoadByFailDirty() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->set('field1', 'value11.11');
        $model->controller->foundOnLoad = false;

        $model->tryLoadBy('field2', '=', 'value2.1');

        $this->assertTrue($model->isDirty('field1'), 'Load erase dirty array');
    }


    function testUnload() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData, 'value1.1');

        $model->unload();

        $this->assertFalse($model->loaded(), 'Model is already loaded');
        $this->assertEquals(null, $model->id, 'Id is already set');
    }

    function testUnloadHooks() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData, 'value1.1');
        $tmp = 0;
        $model->addHook('beforeUnload,afterUnload', function() use (&$tmp) { $tmp += 1; });

        $model->unload();
        
        $this->assertEquals(2, $tmp);
    }

    function testUnloadHooksNotLoaded() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $tmp = 0;
        $model->addHook('beforeUnload,afterUnload', function() use (&$tmp) { $tmp += 1; });

        $model->unload();
        
        $this->assertEquals(1, $tmp);
    }

    function testInsert() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

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
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->load('value1.1');

        $fields = array(
            'field2' => 'newValue2',
        );
        $model->set($fields)->save('newValue1');

        $this->assertEquals($fields['field2'], $model->get('field2'));
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEmpty($model->dirty);
    }

    function testInsertHook() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
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
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
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
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->delete('value1.1');

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteModelLoaded() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->load('value1.1');

        $model->delete();

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteModelLoadedWithId() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->load('value1.1');

        $this->assertThrowException('BaseException', $model, 'delete', array('value1.2'));
        $this->assertTrue($model->loaded(), 'Model must not loaded');
    }

    function testDeleteModelLoadedWithSameId() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);
        $model->load('value1.1');

        $model->delete('value1.1');

        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteNothing() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $this->assertThrowException('BaseException', $model, 'delete');
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testCount() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $count = $model->count();
        $this->assertEquals(3, $count);
    }

    function testCountBuiltin() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $count = count($model);
        $this->assertEquals(3, $count);
    }

    function testIterable() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        foreach($model as $n => $row) {
            $this->assertTrue($model->loaded(), 'Model must be loaded');
            $this->assertEquals($row, $model->data);
            $this->assertEquals(self::$exampleData[$n], $row);
        }
        $this->assertEquals(3, $model->controller->next);
        $this->assertEquals(1, $model->controller->rewind);
    }

    function testAddCondition() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->addCondition('field1', 'value1.1');

        $this->assertTrue(isset($model->conditions['field1']), 'Model doesn\'t store condition');
        $this->assertEquals(1, count($model->conditions['field1']));
        $this->assertEquals(array('field1', '=', 'value1.1'), $model->conditions['field1'][0]);
    }

    function testAddConditionArray() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $model->addCondition(array(
            array('field1', 'value1.1'),
            array('field1', '>', 'value2.1')));

        $this->assertTrue(isset($model->conditions['field1']), 'Model doesn\'t store condition');
        $this->assertEquals(2, count($model->conditions['field1']));
        $this->assertEquals(array('field1', '=', 'value1.1'), $model->conditions['field1'][0]);
        $this->assertEquals(array('field1', '>', 'value2.1'), $model->conditions['field1'][1]);
    }

    function testAddConditionOnlyKey() {
        $model = $this->add('TestModel');
        $model->setSource('Foo', self::$exampleData);

        $this->assertThrowException('BaseException', $model, 'addCondition', array('field1'));

        $this->assertEquals(0, count($model->conditions));
    }

    static private $exampleData = array(
        array('field1' => 'value1.1', 'field2' => 'value2.1', 'field3' => 'value3.1'),
        array('field1' => 'value1.2', 'field2' => 'value2.2', 'field3' => 'value3.2'),
        array('field1' => 'value1.3', 'field2' => 'value2.3', 'field3' => 'value3.3'),
    );
}
