<?php
namespace Classes;

use Enum\InputTypeEnum;
use Enum\ModelColumnEnum;
use UnitEnum;

class FormBuilder{

    /** @var array<FormInput> $inputs */
    private array $inputs;
    private string $action = '';
    private string $method = 'POST';
    private string $class = '';
   
    public function add(string $name, string $label, InputTypeEnum $type, bool $required = false, ?string $defaultValue = '', array $extra = []):FormBuilder
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

    /**
     * Remplir le formulaire à partir d'un modèle
     * @param ModelCore $model
     * @return FormBuilder
     */
    public function setFromModel(ModelCore $model): FormBuilder
    {
        foreach($model::$configuration as $columnConfig){
            $columnName = $columnConfig['column_name'];
            $label = ucwords(str_replace('_', ' ', $columnName));
            $type = $this->getInputTypeByModelColumnType($columnConfig['type']);
            $length = $columnConfig['length'] ?? null;
            $fileExtensions = $columnConfig['file_extensions'] ?? [];
            $required = !$columnConfig['nullable'];
            $extra = $columnConfig['extra'] ?? [];
            if($columnConfig['auto_increment'] ?? false) {
                continue;
            }
            $defaultValue = '';
            // Si une valeur par défaut est définie dans le modèle, l'utiliser
            if(!is_null($model->getId())){
                $prefixGetter = 'get';
                if($type === InputTypeEnum::CHECKBOX) {
                    $prefixGetter = 'is';
                }
                $defaultValue = $model->{$prefixGetter . ucfirst(snakeToCamel($columnName))}() ?? '';
            }
            if($defaultValue instanceof \BackedEnum) {
                $defaultValue = $defaultValue->value;
            }
            if($columnConfig['type'] === ModelColumnEnum::FILE) {
                /**
                 * @var FileModelCore $model
                 */
                $defaultValue = $model::$filePath . $defaultValue;
                $extra['file_extensions'] = $fileExtensions;
            }
            if($columnConfig['type'] === ModelColumnEnum::ENUM) {
                $extra['enum'] = $columnConfig['length'];
            }
            $this->add($columnName, $label, $type, $required, $defaultValue, extra: $extra);
        }
        return $this;
    }

    /**
     * Obtenir le type d'entrée HTML à partir du type de colonne du modèle
     * @param ModelColumnEnum $modelColumnType
     * @return InputTypeEnum
     */
    private function getInputTypeByModelColumnType(ModelColumnEnum $modelColumnType): InputTypeEnum
    {
        return match($modelColumnType) {
            ModelColumnEnum::INT => InputTypeEnum::NUMBER,
            ModelColumnEnum::VARCHAR => InputTypeEnum::TEXT,
            ModelColumnEnum::TEXT => InputTypeEnum::TEXTAREA,
            ModelColumnEnum::DATE => InputTypeEnum::DATE,
            ModelColumnEnum::DATETIME => InputTypeEnum::DATETIME,
            ModelColumnEnum::BOOLEAN => InputTypeEnum::CHECKBOX,
            ModelColumnEnum::FILE => InputTypeEnum::FILE,
            ModelColumnEnum::ENUM => InputTypeEnum::ENUM,
            default => InputTypeEnum::TEXT,
        };
    }

    /**
     * Modifier un input existant
     * @param string $name
     * @param ?string $label
     * @param ?InputTypeEnum $type
     * @param ?bool $required
     * @param ?string $defaultValue
     * @param ?array $extra
     * @return FormBuilder
     */
    public function editInput(string $name, ?string $label = null, ?InputTypeEnum $type = null , ?bool $required = null, ?string $defaultValue = null, ?array $extra = null): FormBuilder
    {
        foreach($this->inputs as $key => $input){
            if($input->getName() === $name){
                $this->inputs[$key] = new FormInput(
                    $name,
                    $label ?? $input->getLabel(),
                    $type ?? $input->getType(),
                    $required ?? $input->isRequired(),
                    $defaultValue ?? $input->getDefaultValue(),
                    $extra ?? $input->getExtra()
                );
            }
        }
        return $this;
    }

    /**
     * Supprimer un input existant
     * @param string $name
     * @return FormBuilder
     */
    public function removeInput(string $name): FormBuilder
    {
        foreach($this->inputs as $key => $input){
            if($input->getName() === $name){
                unset($this->inputs[$key]);
            }
        }
        return $this;
    }
}