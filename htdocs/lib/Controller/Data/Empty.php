<?php

class Controller_Data_Empty extends Controller_Data {
    
    function save($model, $id, $data) { }

    function delete($model, $id) { }

    function loadById($model, $id) { }

    function loadByConditions($model) { }

    function prefetchAll($model) { }

    function loadCurrent($model) { }

    function deleteAll($model) { }
}