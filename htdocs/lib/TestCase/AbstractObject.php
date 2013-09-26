<?php

class TestCase_AbstractObject extends TestCase {
    function testAddHook() {
        $hookName = 'new-hook';
        $obj = $this->add('DummyObject');
        $obj->addHook($hookName, function($obj) { } );

        $this->assertTrue(isset($obj->hooks[$hookName]), 'hook not stored');
    }

    function testHookCall() {
        $hookName = 'new-hook';
        $obj = $this->add('DummyObject');
        $tmp = 0;
        $obj->addHook($hookName, function($obj) use (&$tmp) { $tmp++; } );

        $obj->hook($hookName);

        $this->assertEquals(1, $tmp);
    }

    function testHookParameter() {
        $hookName = 'new-hook';
        $obj = $this->add('DummyObject');
        $tmp = 0;
        $self = $this;
        $callparam = array('1', 0);
        $regparam = array('a',2,'b');
        $obj->addHook($hookName,
            function($first, $cp, $rp) use (&$tmp, $self, $callparam, $obj, $regparam) {
                $self->assertEquals($obj, $first);
                $self->assertEquals($callparam, $cp);
                $self->assertEquals($regparam, $rp);
                $tmp++; 
        }, array($regparam));

        $obj->hook($hookName, array($callparam));

        $this->assertEquals(1, $tmp);
    }
}