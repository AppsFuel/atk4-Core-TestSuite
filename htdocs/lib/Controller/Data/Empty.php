<?php

class Controller_Data_Empty extends Controller_Data {
    
    function save($model, $id) { }

    function delete($model, $id) { }

    function tryLoad($model, $id) { }

    function tryLoadAny($model) { }

    function tryLoadBy($model, $field, $cond, $value) { }

    function prefetchAll($model) { }

    function loadCurrent($model) { }

    function deleteAll($model) { }
}