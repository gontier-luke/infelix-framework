<?php

enum QuestionTypeEnum: string{
    case FREE_TEXT = 'freeText';
    case SLIDER = 'slider';
    case MULTI_IMAGE = 'multiImage';
    case TEXT_CHOICE = 'textChoice';

    public function getVarForInsert(): array
    {
        return match ($this->value) {
            'freeText' => ['libelle', 'image', 'imageDescription'],
            'slider' => ['libelle', 'image', 'imageDescription','slider'],
            'multiImage' => ['libelle', 'image', 'imageDescription', 'imageResp', 'imageRespDesc'],
            'textChoice' => ['libelle', 'image', 'imageDescription', 'choiseText'],
        };
    }

    public function addNewQuestion($params): void{
        if (!isset($params['libelle']) || $params['libelle'] == '') {
            throw new Exception('La question ne peut pas être vide.');
        }

        match ($this->value) {
            'freeText' => (new QuestionModel())->addFreeTextQuestion($params),
            'slider' => (new QuestionModel())->addSliderQuestion($params),
            'multiImage' => (new QuestionModel())->addMultiImageQuestion($params),
            'textChoice' => (new QuestionModel())->addTextChoiceQuestion($params),
            default => null,
        };
    }

    public function updateQuestion($params): void{
        if (!isset($params['libelle']) || $params['libelle'] == '') {
            throw new Exception('La question ne peut pas être vide.');
        }

        match ($this->value) {
            'freeText' => (new QuestionModel())->updateFreeTextQuestion($params),
            'slider' => (new QuestionModel())->updateSliderQuestion($params),
            'multiImage' => (new QuestionModel())->updateMultiImageQuestion($params),
            'textChoice' => (new QuestionModel())->updateTextChoiceQuestion($params),
            default => null,
        };
    }
}
