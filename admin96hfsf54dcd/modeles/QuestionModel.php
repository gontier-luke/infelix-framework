<?php

class QuestionModel extends ModelCore{

    const QUESTION_FIELDS = ['libelle', 'image', 'imageDescription', 'slider'];

    public function getAllQuestion():array
    {
        $questions = [];
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('SELECT `id`, `libelle` FROM QUESTION');
        if(!$query->execute()){
            $query->close();
            throw $query->error;
        }

        $result = $query->get_result();

        while ($row = $result->fetch_assoc()) {
            if (strlen($row['libelle']) > 18) {
                $row['libelle'] = substr($row['libelle'], 0, 18) . '[...]';
            }
            $questions[] = $row;
        }

        return $questions;
    }

    public function removeQuestion(int $id):bool
    {

        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('SELECT * FROM REPONSE WHERE `id_question`=?');
        $query->bind_param('i',$id);

        if(!$query->execute()){
            $query->close();
            throw $query->error;
        }

        $reponses = $query->get_result();

        $query->close();
        $query = $dbLink->prepare('SELECT * FROM RESULTAT WHERE `id_question`=?');
        $query->bind_param('i',$id);

        if(!$query->execute()){
            $query->close();
            throw $query->error;
        }

        $resultats = $query->get_result();
        $query->close();

        if($reponses->num_rows == 0){

            if($resultats->num_rows == 0){
                $query = $dbLink->prepare('DELETE FROM QUESTION WHERE `id`=?');
                $query->bind_param('i',$id);

                if(!$query->execute()){
                    $query->close();
                    throw $query->error;
                }
                return true;
            }
            
            $query = $dbLink->prepare('DELETE res, q FROM RESULTAT res LEFT JOIN QUESTION q ON res.`id_question`=q.`id` WHERE res.`id_question`=?;');
            $query->bind_param('i',$id);

            if(!$query->execute()){
                $query->close();
                throw $query->error;
            }
            return true;
        }

        if ($resultats->num_rows == 0) {
            $query = $dbLink->prepare('DELETE r, q FROM REPONSE r LEFT JOIN QUESTION q ON r.`id_question`=q.`id` WHERE r.`id_question`=?;');
            $query->bind_param('i',$id);

            if(!$query->execute()){
                $query->close();
                throw $query->error;
            }
            return true;
        }

        $query = $dbLink->prepare('DELETE r, res, q FROM RESULTAT res  LEFT JOIN REPONSE r ON r.`id_question`=res.`id_question`LEFT JOIN QUESTION q ON res.`id_question`=q.`id` WHERE res.`id_question`=?;');
       
        $query->bind_param('i',$id);

        if(!$query->execute()){
            $query->close();
            throw $query->error;
        }

        return true;
    }

    public function addFreeTextQuestion($params):bool
    {
        return $this->addQuestionPart(QuestionTypeEnum::FREE_TEXT, $params);
    }

    public function updateFreeTextQuestion($params):bool
    {        
        return $this->updateQuestionPart(QuestionTypeEnum::FREE_TEXT, $params);
    }

    public function addSliderQuestion(array $params):bool
    {  
        if (!isset($params['slider'])) {
            throw new Exception('Il faut un nombre maximum pour le slider.');
        }

        return $this->addQuestionPart(QuestionTypeEnum::SLIDER, $params);
    }

    public function updateSliderQuestion(array $params):bool
    {
        if (!isset($params['slider'])) {
            throw new Exception('Il faut un nombre maximum pour le slider.');
        }

        return $this->updateQuestionPart(QuestionTypeEnum::SLIDER, $params);
    }

    public function addMultiImageQuestion(array $params):bool
    {   
        if (!isset($_FILES['imageResp']) || !is_array($_FILES['imageResp']) || count($_FILES['imageResp']) < 1) {
            throw new Exception('Il faut au moins une image pour ce type de question.');
        }

        if (!isset($params['imageRespDesc']) || !is_array($params['imageRespDesc']) || count($params['imageRespDesc']) < 1) {
            throw new Exception('Il faut une description pour chaque image.');
        }

        $idQuestion = $this->addQuestionPart(QuestionTypeEnum::MULTI_IMAGE, $params);

        foreach ($_FILES['imageResp']['name'] as $key => $image) {
            if (empty($image)) {
                continue;
            }
            $requestResp = 'INSERT INTO REPONSE (`id_question`,`img`,`img_label`) VALUES (?,?,?)';
            $bindResp = 'iss';
            $sqlParamsResp = [$idQuestion, base64_encode(file_get_contents($_FILES['imageResp']['tmp_name'][$key])), $params['imageRespDesc'][$key]];

            $this->executeInsertQuery($requestResp, $bindResp, $sqlParamsResp);
        }

        return true;
    }

    public function updateMultiImageQuestion(array $params):bool
    {
        if (!isset($_FILES['imageResp']) || !is_array($_FILES['imageResp']) || count($_FILES['imageResp']) < 1) {
            throw new Exception('Il faut au moins une image pour ce type de question.');
        }

        if (!isset($params['imageRespDesc']) || !is_array($params['imageRespDesc']) || count($params['imageRespDesc']) < 1) {
            throw new Exception('Il faut une description pour chaque image.');
        }

        $idQuestion = $this->updateQuestionPart(QuestionTypeEnum::MULTI_IMAGE, $params);

        foreach ($_FILES['imageResp']['name'] as $key => $image) {
            if (empty($image)) {
                continue;
            }
            $img = base64_encode(file_get_contents($_FILES['imageResp']['tmp_name'][$key]));
            if ($params['response'][$key] == ''){
                $requestResp = 'INSERT INTO REPONSE (`id_question`,`contenu`) VALUES (?,?)';
                $bindResp = 'is';
                $sqlParamsResp = [$params['id'], $img];

                $this->executeInsertQuery($requestResp, $bindResp, $sqlParamsResp);
                continue;
            }
            $requestResp = 'UPDATE REPONSE SET `img`=?,`img_label`=? WHERE `id`=?';
            $bindResp = 'ssi';
            $sqlParamsResp = [$img , $params['imageRespDesc'][$key],$params['response'][$key]];

            $this->executeInsertQuery($requestResp, $bindResp, $sqlParamsResp);
        }

        return true;
    }

    public function addTextChoiceQuestion(array $params):bool
    {   
        if (!isset($params['choiseText']) || !is_array($params['choiseText']) || count($params['choiseText']) < 2){
            throw new Exception('Il faut plusieurs choix pour ce type de question.');
        }

        $idQuestion = $this->addQuestionPart(QuestionTypeEnum::TEXT_CHOICE, $params);

        foreach ($params['choiseText'] as $key => $content) {
            if ($content == '') {
                continue;
            }
            $requestResp = 'INSERT INTO REPONSE (`id_question`,`contenu`) VALUES (?,?)';
            $bindResp = 'is';
            $sqlParamsResp = [$idQuestion, $content];

            $this->executeInsertQuery($requestResp, $bindResp, $sqlParamsResp);
        }

        return true;
    }

    public function updateTextChoiceQuestion(array $params):bool
    {   
        $this->executeInsertQuery('UPDATE QUESTION SET `slider`=NULL WHERE `id`=?', 'i', [$params['id']]);
        
        if (!isset($params['choiseText']) || !is_array($params['choiseText']) || count($params['choiseText']) < 2){
            throw new Exception('Il faut plusieurs choix pour ce type de question.');
        }
        if($this->updateQuestionPart(QuestionTypeEnum::TEXT_CHOICE, $params)){
            return true;
        }

        foreach ($params['choiseText'] as $key => $content) {
            if ($content == '') {
                continue;
            }
            if ($params['response'][$key] == ''){
                $requestResp = 'INSERT INTO REPONSE (`id_question`,`contenu`) VALUES (?,?)';
                $bindResp = 'is';
                $sqlParamsResp = [$params['id'], $content];

                $this->executeInsertQuery($requestResp, $bindResp, $sqlParamsResp);
                continue;
            }
            $requestResp = 'UPDATE REPONSE SET `contenu` = ? WHERE `id`=?';
            $bindResp = 'si';
            $sqlParamsResp = [$content,$params['response'][$key]];

            $this->executeInsertQuery($requestResp, $bindResp, $sqlParamsResp);
        }

        return true;
    }

    public function getQuestion(int $id):array
    {
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('SELECT * FROM QUESTION WHERE `id`=?');
        $query->bind_param('i',$id);

        if(!$query->execute()){
            $query->close();
            throw $query->error;
        }

        $result = $query->get_result();
        $question = $result->fetch_assoc();
        $query->close();

        $query = $dbLink->prepare('SELECT * FROM REPONSE WHERE `id_question`=?');
        $query->bind_param('i',$id);

        if(!$query->execute()){
            $query->close();
            throw $query->error;
        }

        $result = $query->get_result();
        $question['REPONSES'] = [];
        while ($row = $result->fetch_assoc()) {
            $question['REPONSES'][] = $row;
        }

        $query->close();

        # get question type 

        if (isset($question['SLIDER'])) {
            $question['TYPE'] = QuestionTypeEnum::SLIDER;
        } elseif (isset($question['REPONSES'][0]['IMG'])) {
            $question['TYPE'] = QuestionTypeEnum::MULTI_IMAGE;
        } elseif (isset($question['REPONSES'][0]['CONTENU'])) {
            $question['TYPE'] = QuestionTypeEnum::TEXT_CHOICE;
        } else {
            $question['TYPE'] = QuestionTypeEnum::FREE_TEXT;
        }

        return $question;
    }	

    private function addQuestionPart($type, $params):int
    {
        $request = 'INSERT INTO QUESTION (`libelle`';
        $bind = 's';
        $values = '?';
        $sqlParams = [$params['libelle']];

        $questionColumn = array_intersect(self::QUESTION_FIELDS, $type->getVarForInsert());

        foreach ($questionColumn as $keyAsked) {
            if ($keyAsked === 'libelle') {
                continue;
            }

            list($column, $nextBind, $newValues, $newSqlParams) = $this->processColumn($keyAsked, $params);

            if ($column) {
                $request .= ',`' . $column . '`';
                $bind .= $nextBind;
                $values .= $newValues;
                $sqlParams = array_merge($sqlParams, $newSqlParams);
            }
        }

        $request .= ') VALUES (' . $values . ')';
        return $this->executeInsertQuery($request, $bind, $sqlParams);
    }
    
    private function updateQuestionPart($type, $params):int
    {
        $request = 'UPDATE QUESTION SET `libelle`=?';
        $bind = 's';
        $sqlParams = [$params['libelle']];

        $questionColumn = array_intersect(self::QUESTION_FIELDS, $type->getVarForInsert());

        foreach ($questionColumn as $keyAsked) {
            if ($keyAsked === 'libelle') {
                continue;
            }

            list($column, $nextBind, $newValues, $newSqlParams) = $this->processColumn($keyAsked, $params);

            if ($column) {
                $request .= ',`' . $column . '`=?';
                $bind .= $nextBind;
                $sqlParams = array_merge($sqlParams, $newSqlParams);
            }
        }

        $request .= ' WHERE `id`=?';
        $bind .= 'i';
        $sqlParams[] = $params['id'];

        return $this->executeInsertQuery($request, $bind, $sqlParams);
    }
}
