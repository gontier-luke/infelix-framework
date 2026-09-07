<?php
namespace Enum;

enum InputTypeEnum: string{
    case TEXT = 'text';
    case FILE = 'file';
    case SELECT = 'select';
    case ENUM = 'enum';
    case PASSWORD = 'password';
    case SUBMIT = 'submit';
    case QUILLJS = 'quilljs';
    case HIDDEN = 'hidden';
    case TEXTAREA = 'textarea';
    case GROUP = 'group';
    case RADIO = 'radio';
    case NUMBER = 'number';
    case LABEL = 'label';
    case CHECKBOX = 'checkbox';
    case DATE = 'date';
    case DATETIME = 'datetime-local';
    case EMAIL = 'email';
}
