<?php

class Controller_Data_Foo extends Controller_Data {
    public $foundOnLoad = true;
    public $rewind = 0;
    public $next = 0;
    public $supportConditions = true;
    public $supportLimit = true;
    public $supportOrder = true;
    public $supportOperators = array('=' => true, '>' => true, '>=' => true, '<=' => true, '<' => true, '!=' => true);

    function save($model, $id, $data) {
        return $id || 1;
    }

    function delete($model, $id) {
        if (!$model->loaded()) {
            throw $this->exception('Model isn\'t loaded in Controller_Data');
        }
    }

    function loadById($model, $id) {
        if ($model->loaded()) {
            throw $this->exception('Model is loaded in Controller_Data');
        }
        if ($this->foundOnLoad) {
            $model->id = $id;
            foreach ($model->_table[$this->short_name] as $n => $row) {
                if ($row[$model->id_field] === $id) {
                    $model->data = $row;       
                }
            }
            if (empty($model->data)) {
                $model->data = $model->_table[$this->short_name][1];
            }
        } else {
            $model->id = null;
        }
    }

    function loadByConditions($model) {
        if ($model->loaded()) {
            throw $this->exception('Model is loaded in Controller_Data');
        }
        if ($this->foundOnLoad) {
            $model->id = $model->_table[$this->short_name][1][$model->id_field];
            $model->data = $model->_table[$this->short_name][1];
        } else {
            $model->id = null;
        }
    }

    function count($model) {
        return count($model->_table[$this->short_name]);
    }

    function prefetchAll($model) {
        $this->rewind += 1;
        reset($model->_table[$this->short_name]);
    }

    function loadCurrent($model) {
        $this->next +=1;
        list($model->id, $model->data) = each($model->_table[$this->short_name]);
        if ($model->id) {
            $model->data = $model->get();
        }
    }

    function deleteAll($model) {
        if ($model->loaded()) {
            throw $this->exception('Model is loaded in Controller_Data');
        }
    }
}