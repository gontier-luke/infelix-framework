<?php 
namespace Classes;

use Enum\InputTypeEnum;

require_once BASE_PATH .'/enum/InputType.php';

class FormInput{

    public function __construct(   
        private string $name,
        private string $label,
        private InputTypeEnum $type,
        private bool $required = false,
        private string $defaultValue = '',
        private array $extra = [],
    )
    {
        
        if ($type == InputTypeEnum::ENUM) {
            $this->extra['options']= [];
            foreach ($this->extra['enum']::cases() as $case) {
                $this->extra['options'][$case->name] = $case->value;
            }
        }

        if (in_array($this->type, [InputTypeEnum::SELECT, InputTypeEnum::ENUM]) && !key_exists('options', $this->extra) ) {
            $this->extra['options'] = ['Aucune option renseignée' => 'error'];
        }
        if ($this->type == InputTypeEnum::GROUP && !key_exists('children', $this->extra)) {
            $this->extra['error'] = ['Aucune option renseignée' => 'error'];
        }
    }

    public function renderInput():string
    {
        $prefix = '';
        $innerHtml = '';
        $suffix = '>';
        $label = '';
        $for = $this->name;
        $acceptedFileTypes = '';
        if($this->type == InputTypeEnum::FILE && isset($this->extra['file_extensions'])) {
            $acceptedFileTypes = implode(',', $this->extra['file_extensions']);
        }

        switch ($this->type) {
            case InputTypeEnum::QUILLJS :
                $prefix = '<div ';
                $innerHtml = '>' . $this->generateQuillJsInput();
                $suffix = '</div>';
                break;         
            case InputTypeEnum::ENUM :
            case InputTypeEnum::SELECT :
                $prefix = '<select ';
                $innerHtml .= '>';
                foreach($this->extra['options'] as $name => $value){
                    $innerHtml .= '<option class="admin-form_option" value="'.$name.'" '. ($this->defaultValue === $name ? 'selected' : '') .'>'.$value.'</option>';
                }
                $suffix = '</select>';
                break;
            case InputTypeEnum::FILE :
                $prefix = '<input type="file" accept="'.$acceptedFileTypes.'" ';
                break;
            case InputTypeEnum::GROUP :
                $prefix = '<div ';
                $innerHtml = '>';
                foreach ($this->extra['children'] as $input) {
                    $innerHtml .= $input->renderInput();
                }
                $suffix = '</div>';
                break;
            case InputTypeEnum::TEXTAREA :
                $prefix = '<textarea ';
                $innerHtml = '>' . $this->defaultValue;
                $suffix = '</textarea>';
                break;
            case InputTypeEnum::LABEL :
                
                break;
            case InputTypeEnum::SUBMIT :
                $prefix = '<input type="'.$this->type->value.'" class="btn btn-primary w-100 rounded" ';
                $this->defaultValue = $this->label;
                $this->label = '';
                break;
            case InputTypeEnum::RADIO :
                $for = isset($this->extra['id']) ? $this->extra['id'] : $this->name;
            default:
                $prefix = '<input type="'.$this->type->value.'"';
                break;
        
        }
        
        if ($this->label != '') {
            $label = '<label for="'. $for .'" class="admin-form_label">'.$this->label.'</label>';
        }
        $attributes = '';
        foreach($this->extra as $attribute => $attrValue){
            if ($attribute != 'options' && !is_array($attrValue)) {
                $attributes .= ' ' . $attribute . '="' . $attrValue . '"';
            }
        }
        if ($this->required) {
            $attributes .= ' required="required"';
        }
        $groupClass = '';
        $classes = isset($this->extra['class']) ? $this->extra['class'] : '';
        if(isset($this->extra['class'])){
            if($this->type == InputTypeEnum::GROUP){
                $groupClass = $this->extra['class'];
                $classes = '';
            }
        }
        if ($this->type == InputTypeEnum::LABEL) {
            return '<label class="admin-form_label"'. $attributes. '>'.$this->label.'</label>';
        }
        $value = isset($_POST[$this->name]) ? $_POST[$this->name] : $this->defaultValue;
        
        if ($this->type == InputTypeEnum::RADIO || $this->type == InputTypeEnum::CHECKBOX) {
            $attributes .= ($value ? ' checked ' : '');
        }
        $html = '<div class="admin-form_group '.$groupClass.'">'.$label . $prefix . 'class="admin-form_input '.$classes.'" name="'.$this->name.'" id="' . $this->name . '" value="'.$value.'"' . $attributes . $innerHtml . $suffix.'</div>';

        return $html;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getLabel():string
    {
        return $this->label;
    }

    public function getType():InputTypeEnum
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    protected function generateQuillJsInput(): string
    {
        $inputId = 'quill-input-' . $this->name;
        $toolbarId = $this->name;
        $html = '<div id="' . 'toolbar-container-' . $toolbarId . '" class="toolbar-container" style="background-color: white;">
            <span class="ql-formats">
                <select class="ql-font"></select>
                <select class="ql-size"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-strike"></button>
            </span>
            <span class="ql-formats">
                <select class="ql-color"></select>
                <select class="ql-background"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-script" value="sub"></button>
                <button class="ql-script" value="super"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-header" value="1"></button>
                <button class="ql-header" value="2"></button>
                <button class="ql-blockquote"></button>
                <button class="ql-code-block"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
                <button class="ql-indent" value="-1"></button>
                <button class="ql-indent" value="+1"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-direction" value="rtl"></button>
                <select class="ql-align"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-link"></button>
                <button class="ql-image"></button>
                <button class="ql-video"></button>
                <button class="ql-formula"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-clean"></button>
            </span>
        </div>';
        $html .= '<div id="quill-editor-' . $this->name . '" class="bg-white quill-editor" data-form-content-input="#' . $inputId . '" data-toolbar-id="' . $toolbarId . '"></div>';
        $html .= '<input type="hidden" name="' . $this->name . '" id="' . $inputId . '" value="' . htmlspecialchars($this->defaultValue) . '">';
        return $html;
    }

}
