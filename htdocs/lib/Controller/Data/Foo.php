<?php

class Controller_Data_Foo extends Controller_Data {
    public $foundOnLoad = true;
    public $rewind = 0;
    public $next = 0;
    public $supportConditions = true;

    function save($model, $id) {
        return $id || 1;
    }

    function delete($model,$id) {
        if (!$model->loaded()) {
            throw $this->exception('Model isn\'t loaded in Controller_Data');
        }
    }

    function tryLoad($model,$id) {
        if ($model->loaded()) {
            throw $this->exception('Model is loaded in Controller_Data');
        }
        if ($this->foundOnLoad) {
            $model->id = $id;
            $model->data = $model->_table[$this->short_name][1];
        } else {
            $model->id = null;
        }
    }

    function tryLoadAny($model) {
        if ($model->loaded()) {
            throw $this->exception('Model is loaded in Controller_Data');
        }
        if ($this->foundOnLoad) {
            $model->id = 1;
            $model->data = $model->_table[$this->short_name][1];
        } else {
            $model->id = null;
        }
    }

    function tryLoadBy($model,$field,$cond,$value) {
        if ($model->loaded()) {
            throw $this->exception('Model is loaded in Controller_Data');
        }
        if ($this->foundOnLoad) {
            $model->id = 1;
            $model->data = $model->_table[$this->short_name][1];
        } else {
            $model->id = null;
        }
    }

    function count($model) {
        return count($model->_table[$this->short_name]);
    }

    function rewind($model) {
        $this->rewind += 1;
        reset($model->_table[$this->short_name]);
        list($model->id,$model->data) = each($model->_table[$this->short_name]);
    }

    function next($model) {
        $this->next +=1;
        list($model->id,$model->data) = each($model->_table[$this->short_name]);
    }

    function deleteAll($model) {}
}