<?php

enum InputTypeEnum: string{
    case TEXT = 'text';
    case IMAGE = 'fileupload';
    case SELECT = 'select';
    case ENUM = 'enum';
    case PASSWORD = 'password';
    case SUBMIT = 'submit';
    case HIDDEN = 'hidden';
    case TEXTAREA = 'textarea';
    case GROUP = 'group';
    case RADIO = 'radio';
    case NUMBER = 'number';
    case LABEL = 'label';
    case CHECKBOX = 'checkbox';
}
