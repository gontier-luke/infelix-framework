<?php

class FormBuilder{

    /** @var array<FormInput> $inputs */
    private array $inputs;
    private string $action = '';
    private string $method = 'POST';
    private string $class = '';
   
    public function add(string $name, string $label, InputTypeEnum $type, bool $required = false, string $defaultValue = '', array $extra = []):FormBuilder
    {
        $this->inputs[] = new FormInput($name, $label, $type,$required,$defaultValue,$extra);
        return $this;
    }

    public function setMethod(string $method):FormBuilder
    {
        $this->method = $method;
        return $this;
    }

    public function setAction(string $action):FormBuilder
    {
        $this->action = $action;
        
        return $this;
    }

    public function setClass(string $class):FormBuilder
    {
        $this->class = $class;
        
        return $this;
    }

    public function renderForm(){
        $formStart = '<form class="'.$this->class.'" action="'. $this->action . '" method="'.$this->method.'" enctype="multipart/form-data" >'; 

        $innerForm = ''; 
                
        foreach($this->inputs as $input){
            $innerForm .= "\n" . $input->renderInput();
        }

        return $formStart . $innerForm . '</form>';
    }
}
