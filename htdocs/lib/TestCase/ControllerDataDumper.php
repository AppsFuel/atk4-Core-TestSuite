<?php

class TestCase_ControllerDataDumper extends TestCase {
    function testLog() {
        $model = $this->add('Model_TestModel');
        $controller = $this->add('Controller_Data_Dumper');
        $controller->setWatchedControllerData($model, 'Array');

        $controller->setSource($model,array());

        $controller->save($model, null, array());
        $controller->save($model, 1, array());

        $controller->loadById($model, 1);

        $controller->loadByConditions($model);

        $controller->delete($model, 1);
        $controller->deleteAll($model, 1);

        $log = $controller->getLog();
        $expected = array(
            "controller_data_array::setSource with (Model_TestModel model_testmodel, Array)",
            "controller_data_array::setSource return Controller_Data_Array controller_data_array",
            "controller_data_array::save with (Model_TestModel model_testmodel, , Array)",
            "controller_data_array::save return 524d6c04cd8db",
            "controller_data_array::save with (Model_TestModel model_testmodel, 1, Array)",
            "controller_data_array::save return ",
            "controller_data_array::loadById with (Model_TestModel model_testmodel, 1)",
            "controller_data_array::loadById return ",
            "controller_data_array::loadByConditions with (Model_TestModel model_testmodel)",
            "controller_data_array::loadByConditions return ",
            "controller_data_array::delete with (Model_TestModel model_testmodel, 1)",
            "controller_data_array::delete return ",
            "controller_data_array::deleteAll with (Model_TestModel model_testmodel)",
            "controller_data_array::deleteAll return ",
        );
    }
}