<?php

class Field_Concatenate extends Field_Calculated {
    public $separator = '';
    
    function getValue($model, $data) {
        return $data['field1'] . $this->separator . $data['field2'];
    }
}