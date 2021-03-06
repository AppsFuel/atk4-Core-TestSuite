<?php

class TestCase_ControllerDataSQL extends TestCase {
    function init() {
        parent::init();
        $this->api->dbConnect('mysql://root:aa@localhost/atk4core_testsuite');
        $this->api->db->beginTransaction(); // force use transaction
    }

    function testLoadById() {
        $model = $this->add('Model_Customer');
        $controller = $model->controller;

        $controller->loadById($model, 1);

        $expected = array(
            'id' => '1',
            'name' => 'John Smith',
            'email' => 'demo',
            'password' => 'demo',
        );
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($expected, $model->get());
    }

    function testLoadByConditions() {
        $model = $this->add('Model_Customer');
        $model->addCondition('id', '2');
        $controller = $model->controller;

        $controller->loadByConditions($model);

        $expected = array(
            'id' => '2',
            'name' => 'Peter Taylor',
            'email' => 'test',
            'password' => 'test',
        );
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($expected, $model->get());
    }

    function testLoadWithActualFields() {
        $model = $this->add('Model_Customer');
        $model->getElement('email')->system(true);
        $model->setActualFields(array('id', 'password'));
        $controller = $model->controller;

        $controller->loadByConditions($model);

        $expected = array(
            'id' => '1',
            'email' => 'demo',
            'password' => 'demo',
        );
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($expected, $model->get());
    }

    function testLoadWithExpressionCondition() {
        $model = $this->add('Model_Customer');
        $model->addCondition($model->dsql()->expr('id % 2 = 0'));
        $controller = $model->controller;

        $controller->loadByConditions($model);

        $expected = array(
            'id' => '2',
            'name' => 'Peter Taylor',
            'email' => 'test',
            'password' => 'test',
        );
        $this->assertEquals($expected, $model->get());
    }

    function testLoadWithExpression() {
        $model = $this->add('Model_Customer');
        $model->addExpression('sanitized_name', $model->dsql()->expr('REPLACE(LOWER(name), " ", "_")'));
        $controller = $model->controller;

        $controller->loadByConditions($model);

        $expected = array(
            'id' => '1',
            'name' => 'John Smith',
            'email' => 'demo',
            'password' => 'demo',
            'sanitized_name' => 'john_smith',
        );
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($expected, $model->get());
    }

    function testLoadWithExpressionAndCondition() {
        $model = $this->add('Model_Customer');
        $model->addExpression('length', $model->dsql()->expr('LENGTH(name)'));
        $model->addCondition('length', '>', 10);
        $controller = $model->controller;

        $controller->loadByConditions($model);

        $expected = array(
            'id' => '2',
            'name' => 'Peter Taylor',
            'email' => 'test',
            'password' => 'test',
            'length' => '12',
        );
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($expected, $model->get());
    }

    function testLoadWithHasOne() {
        $model = $this->add('Model_Rental');
        $controller = $model->controller;

        $controller->loadByConditions($model);

        $expected = array(
            'id' => '1',
            'is_returned' => 'N',
            'dvd_id' => '1',
            'customer_id' => '1',
            'customer' => 'John Smith',
        );
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($expected, $model->get());
    }

    function testPrefetchAll() {
        $model = $this->add('Model_Rental');
        $controller = $model->controller;

        $expected = array(
            array(
                'id' => "1",
                'is_returned' => "N",
                'dvd_id' => '1',
                'customer_id' => "1",
                'customer' => "John Smith",
            ),
            array(
                'id' => "2",
                'is_returned' => "Y",
                'dvd_id' => '2',
                'customer_id' => "1",
                'customer' => "John Smith",
            )
        );
        $n = 0;
        foreach ($model as $id => $row) {
            $this->assertEquals($row['id'], $id);
            $this->assertEquals($expected[$n], $row);
            $n++;
        }
    }

    function testDelete() {
        $model = $this->add('Model_Rental');
        $controller = $model->controller;

        $controller->delete($model, 1);

        $model->tryLoad(1);
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testDeleteAll() {
        $model = $this->add('Model_Rental');
        $model->addCondition('id', '>', 1);
        $controller = $model->controller;

        $controller->deleteAll($model);

        $model = $this->add('Model_Rental');
        $model->tryLoad(1);
        $this->assertTrue($model->loaded(), 'Model must be loaded');

        $model->addCondition('id', '>', 1);
        $model->tryLoadAny();
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
    }

    function testJoin() {
        $model = $this->add('Model_Rental');
        $customerTable = $model->join('customer', 'customer_id', 'left', 'Customer');
        $customerTable->addField('name');
        $customerTable->addField('email');
        $model->addCondition('name', 'like', '%ohn%');

        $model->loadAny(1);

        $data = $model->get();
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('N', $data['is_returned']);
        $this->assertEquals('1', $data['dvd_id']);
        $this->assertEquals('1', $data['customer_id']);
        $this->assertEquals('John Smith', $data['customer']);
        $this->assertEquals('John Smith', $data['name']);
        $this->assertEquals('demo', $data['email']);
        $this->assertEquals(7, count($data));
    }

    function skiptestReverseJoin() {
        $model = $this->add('Model_Customer');
        $customerTable = $model->join('rental.customer_id', 'id', 'left', 'Rental');
        $customerTable->addField('is_returned');

        $model->loadAny(1);

        $data = $model->get();
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('N', $data['is_returned']);
        $this->assertEquals('John Smith', $data['name']);
        $this->assertEquals('demo', $data['email']);
        $this->assertEquals('demo', $data['password']);
        $this->assertEquals(5, count($data));
    }

    function skiptestJoinMultiple() {
        $model = $this->add('Model_Customer');
        $customerTable = $model->join('rental.customer_id', 'id', 'left', 'Rental');
        $customerTable->addField('is_returned');

        $dvdTable = $customerTable->join('dvd', 'dvd_id', 'left', 'Dvd');
        $dvdTable->addField('code');
        $dvdTable->addField('movie_id');

        $movie = $dvdTable->join('movie', 'movie_id', 'left', 'Movie');
        $name = $movie->addField('movie_name', 'name');

        $model->loadAny(1);

        $data = $model->get();
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('N', $data['is_returned']);
        $this->assertEquals('John Smith', $data['name']);
        $this->assertEquals('demo', $data['email']);
        $this->assertEquals('demo', $data['password']);
        $this->assertEquals('20397728', $data['code']);
        $this->assertEquals('2', $data['movie_id']);
        $this->assertEquals('The Matrix', $data['movie_name']);
        $this->assertEquals(8, count($data));
    }

    function testModify() {
        $model = $this->add('Model_Customer');
        $model->load(1);
        $data = $model->get();
        $model->set('password', 'aaaa');
        
        $model->save();

        $data['password'] = 'aaaa';
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $model = $this->add('Model_Customer');
        $this->assertEquals($data, $model->load(1)->get());
    }

    function testModifyWithId() {
        $model = $this->add('Model_Customer');
        $model->load(1);
        $data = $model->get();
        $model->set('password', 'aaaa')
            ->set('id', 3);
        
        $model->save();

        $data['password'] = 'aaaa';
        $data['id'] = '3';
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($data, $model->get());
        $model = $this->add('Model_Customer');
        $this->assertEquals($data, $model->load(3)->get());
    }

    function testModifyWithConditions() {
        $model = $this->add('Model_Customer');
        $model->load(1);
        $model->addCondition('id', '=', 2);
        $data = $model->get();
        $model->set('password', 'aaaa')
            ->set('id', 3);
        
        $e = $this->assertThrowException('BaseException', $model, 'save');
        $this->assertEquals('Record with specified id was not found', $e->getMessage());

        $this->assertFalse($model->loaded(), 'Model must be unloaded');
        $model = $this->add('Model_Customer');
        $this->assertEquals($data, $model->load(1)->get());
    }

    function testInsert() {
        $model = $this->add('Model_Customer');
        $data = array(
            'name' => 'newEntry',
            'email' => 'newEmail',
            'password' => 'aaaa',
        );
        $model->set($data);
        
        $model->save();

        $data['id'] = $model->id;
        $this->assertNotEmpty($model->id);
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertSame($data, $model->get());
    }

    function testInsertWithId() {
        $model = $this->add('Model_Customer');
        $data = array(
            'id' => '3',
            'name' => 'newEntry',
            'email' => 'newEmail',
            'password' => 'aaaa',
        );
        $model->set($data);

        $model->save();

        $id = $model->id;
        $this->assertNotEmpty($id);
        $this->assertEquals('3', $id);
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($data, $model->get());
    }

    function testInsertWithConditions() {
        $model = $this->add('Model_Customer');
        $model->addCondition('password', '!=', 'aaaa');
        $data = array(
            'id' => '3',
            'name' => 'newEntry',
            'email' => 'newEmail',
            'password' => 'aaaa',
        );
        $model->set($data);

        $e = $this->assertThrowException('BaseException', $model, 'save');

        $this->assertEquals('Record with specified id was not found', $e->getMessage());
        $this->assertFalse($model->loaded(), 'Model must be not loaded');
        $this->assertFalse($this->add('Model_Customer')->tryLoad(3)->loaded(), 'Model must be not loaded');
    }

    function testInsertWithJoin() {
        $model = $this->add('Model_Rental');
        $customerTable = $model->join('customer', 'customer_id', 'left', 'Customer');
        $customerTable->addField('name');
        $customerTable->addField('email');
        $customerTable->addField('password');

        $model->set('is_returned', 'N')
            ->set('name', 'Paperino')
            ->set('email', 'Paperino')
            ->set('password', 'Paperino')
            ->save();

        $id = $model->id;
        $customerId = $model->get('customer_id');

        $this->assertNotEmpty($id);
        $this->assertNotEmpty($customerId);

        $expected = array(
            'id' => $id,
            'is_returned' => 'N',
            'dvd_id' => null,
            'customer_id' => $customerId,
            'customer' => 'Paperino',
        );
        $model = $this->add('Model_Rental');
        $model->load($id);
        $this->assertEquals($expected, $model->get());

        $expected = array(
            'id' => $customerId,
            'name' => 'Paperino',
            'email' => 'Paperino',
            'password' => 'Paperino',
        );
        $model = $this->add('Model_Customer');
        $model->load($customerId);
        $this->assertEquals($expected, $model->get());
    }

    function testUpdateWithJoin() {
        $model = $this->add('Model_Rental');
        $customerTable = $model->join('customer', 'customer_id', 'left', 'Customer');
        $customerTable->addField('name');
        $customerTable->addField('email');
        $customerTable->addField('password');
        $model->load(1);

        $model->set('is_returned', 'Y')
            ->set('name', 'Paperino')
            ->set('email', 'Paperino')
            ->set('password', 'Paperino')
            ->save();

        $id = $model->id;
        $customerId = $model->get('customer_id');

        $this->assertEquals('1', $id);
        $this->assertEquals('1', $customerId);

        $expected = array(
            'id' => $id,
            'is_returned' => 'Y',
            'dvd_id' => '1',
            'customer_id' => $customerId,
            'customer' => 'Paperino',
        );
        $model = $this->add('Model_Rental');
        $model->load($id);
        $this->assertEquals($expected, $model->get());

        $expected = array(
            'id' => $customerId,
            'name' => 'Paperino',
            'email' => 'Paperino',
            'password' => 'Paperino',
        );
        $model = $this->add('Model_Customer');
        $model->load($customerId);
        $this->assertEquals($expected, $model->get());

        // check where in updating...
        $model->load(2);
        $this->assertNotEquals('Paperino', $model->get('name'));
    }

    function skiptestInsertWithReverseJoin() {
        $model = $this->add('Model_Customer');
        $rentalTable = $model->join('rental.customer_id', 'id', 'left', 'Rental');
        $rentalTable->addField('is_returned');
        $rentalTable->addField('rental_id', 'id');

        $model->set('is_returned', 'Y')
            ->set('name', 'Paperino')
            ->set('email', 'Paperino')
            ->set('password', 'Paperino')
            ->save();

        $id = $model->id;
        $expected = array(
            'id' => $id,
            'name' => 'Paperino',
            'email' => 'Paperino',
            'password' => 'Paperino',
        );
        $model = $this->add('Model_Customer');
        $model->load($id);
        $this->assertEquals($expected, $model->get());

        $rentalModel = $model->ref('Rental');
        $rows = $rentalModel->getRows();
        $this->assertEquals(1, count($rows));
        $row = $rows[0];
        $expected = array(
            'id' => $row['id'], // i can't test it... ( max(id) +1 )
            'is_returned' => 'Y',
            'customer_id' => $id,
            'customer' => 'Paperino',
        );
        $this->assertEquals($expected, $row);
    }

    function testDeleteJoin() {
        $model = $this->add('Model_Rental');
        $customerTable = $model->join('customer', 'customer_id', 'left', 'Customer');
        $customerTable->addField('name');
        $customerTable->addField('email');
        $customerTable->addField('password');
        $model->load(1);
        $customerId = $model->get('customer_id');

        $model->delete();

        $model = $this->add('Model_Rental');
        $model->tryLoad(1);
        $this->assertFalse($model->loaded(), 'Model must be unloaded');
        $model->loadAny(); // check where comndition 

        $model = $this->add('Model_Customer');
        $model->tryLoad($customerId);
        $this->assertFalse($model->loaded(), 'Model must be unloaded');
        $model->loadAny(); // check where comndition 
    }

    function testJoinHasOne() {
        $model = $this->add('Model_Rental');
        $dvdTable = $model->join('dvd', 'dvd_id', 'left', 'DVD');
        $dvdTable->addField('code');
        $dvdTable->hasOne('Movie', 'movie_id');

        $model->loadAny();

        $expected = array(
            'id' => '1',
            'is_returned' => 'N',
            'dvd_id' => '1',
            'customer_id' => '1',
            'customer' => 'John Smith',
            'code' => '20397728',
            'movie_id' => '2',
            'movie' => 'The Matrix',
        );
        $this->assertTrue($model->loaded(), 'Model must be loaded');
        $this->assertEquals($expected, $model->get());
    }

    function testJoinHasOneRef() {
        $model = $this->add('Model_Rental');
        $dvdTable = $model->join('dvd', 'dvd_id', 'left', 'DVD');
        $dvdTable->addField('code');
        $dvdTable->hasOne('Movie', 'movie_id');

        $model->loadAny();
        $movieModel = $model->ref('movie_id');

        $expected = array(
            'id' => '2',
            'name' => 'The Matrix',
            'year' => '1999',
            'imdb' => 'http://www.imdb.com/title/tt0133093/'
        );
        $this->assertEquals($expected, $movieModel->get());
    }

    function testJoinHasMany() {
        $model = $this->add('Model_Rental');
        $dvdTable = $model->join('dvd', 'dvd_id', 'left', 'DVD');
        $dvdTable->addField('code');

        $dvdTable->hasMany('Rental', 'dvd_id', 'id', 'reference_name');

        $model->loadAny();

        $rental = $model->ref('reference_name');

        $rows = $rental->getRows();
        $this->assertEquals(1, count($rows));
        $expected = array(
            'id' => '1',
            'is_returned' => 'N',
            'dvd_id' => '1',
            'customer_id' => '1',
            'customer' => 'John Smith',
        );
        $this->assertEquals($expected, $rows[0]);

        $conditions = $rental->conditions;
        $this->assertEquals(1, count($conditions));
        $expected = array('dvd_id', '=', '1');
        $this->assertEquals($expected, $conditions[0]);
    }


    function testDeleteJoinIgnore() {
        $model = $this->add('Model_Rental');
        $customerTable = $model->join('customer', 'customer_id', 'left', 'Customer', null, 'ignore');
        $customerTable->addField('name');
        $customerTable->addField('email');
        $customerTable->addField('password');
        $model->load(1);
        $customerId = $model->get('customer_id');

        $model->delete();

        $model = $this->add('Model_Rental');
        $model->tryLoad(1);
        $this->assertFalse($model->loaded(), 'Model rental must be unloaded');
        $model->loadAny(); // check where comndition 

        $model = $this->add('Model_Customer');
        $model->tryLoad($customerId);
        $this->assertTrue($model->loaded(), 'Model customer must be unloaded');
    }
}