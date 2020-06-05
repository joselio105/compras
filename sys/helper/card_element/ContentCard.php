<?php

final class ContentCard{
    
    private $tag;
    private $value;
    private $attrs;
    private $valueField;
    private $labelField;

    public function __construct($value, $tag='div', array $attrs=NULL){
        $this->value = $value;
        $this->tag = $tag;
        $this->attrs = (!is_null($attrs)?$attrs:array());
        $this->labelField = NULL;
    }
    
    public function getTag()
    {
        return $this->tag;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getAttrs()
    {
        return $this->attrs;
    }

    public function getValueField()
    {
        return $this->valueField;
    }

    public function setValueField($valueField)
    {
        $this->valueField = $valueField;
    }
    
    public function getLabelField()
    {
        return $this->labelField;
    }

    public function setLabelField($labelField)
    {
        $this->labelField = $labelField;
    }
    
}

