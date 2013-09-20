<?php
/**
 * <code>
 * //doctest:TestCase_Model::testAddField()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testAddField();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testAddTwiceField()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testAddTwiceField();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testSet()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testSet();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testSetWithArray()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testSetWithArray();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testSetInexistentField()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testSetInexistentField();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testSetToSameValue()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testSetToSameValue();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testGet()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testGet();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testGetInesistentFieldNoStrict()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testGetInesistentFieldNoStrict();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testGetInesistentFieldStrict()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testGetInesistentFieldStrict();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testGetActualFields()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testGetActualFields();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testGetSetActualFields()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testGetSetActualFields();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testGroupActualFields()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testGroupActualFields();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testCommaSeparatedGroupActualFields()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testCommaSeparatedGroupActualFields();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testSetSource()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testSetSource();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testSetSourceInvalidController()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testSetSourceInvalidController();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoad()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoad();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadHooks()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadHooks();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadHooksFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadHooksFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadLoadedModel()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadLoadedModel();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoad()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoad();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadLoadedModel()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadLoadedModel();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadLoadedModelFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadLoadedModelFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadHook()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadHook();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadHookFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadHookFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadAny()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadAny();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadAnyFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadAnyFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadAnyLoadedModel()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadAnyLoadedModel();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadAnyLoadedModelFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadAnyLoadedModelFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadAny()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadAny();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadAnyHooks()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadAnyHooks();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadAnyHooksFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadAnyHooksFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadAnyFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadAnyFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadAnyLoadedModel()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadAnyLoadedModel();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadAnyLoadedModelFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadAnyLoadedModelFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadAnyHooks()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadAnyHooks();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadAnyHooksFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadAnyHooksFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadBy()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadBy();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadByFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadByFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadByLoadedModel()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadByLoadedModel();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadByLoadedModelFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadByLoadedModelFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadByHooks()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadByHooks();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testLoadByHooksFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testLoadByHooksFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadBy()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadBy();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadByFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadByFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadByLoadedModel()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadByLoadedModel();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadByLoadedModelFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadByLoadedModelFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadByHooks()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadByHooks();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testTryLoadByHooksFail()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testTryLoadByHooksFail();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testUnload()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testUnload();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testUnloadHooks()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testUnloadHooks();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testUnloadHooksNotLoaded()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testUnloadHooksNotLoaded();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testInsert()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testInsert();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testUpdate()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testUpdate();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testInsertHook()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testInsertHook();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testUpdateHook()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testUpdateHook();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testDelete()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testDelete();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testDeleteModelLoaded()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testDeleteModelLoaded();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testDeleteModelLoadedWithId()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testDeleteModelLoadedWithId();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testDeleteModelLoadedWithSameId()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testDeleteModelLoadedWithSameId();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testDeleteNothing()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testDeleteNothing();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testCount()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testCount();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testCountBuiltin()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testCountBuiltin();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 * <code>
 * //doctest:TestCase_Model::testIterable()
 * $testCase = $this->add('TestCase_Model');
 * $testCase->testIterable();
 * echo 1;
 * // expects:
 * // 1
 * </code>
 */