<?php

class ExportCSVmodel extends ModelCore
{

    public function getAllUserResult(){
        $dbLink = $this->connectBd();

        //On récupère les questions
        $questions = [];
        $result = $dbLink->query("SELECT ID, LIBELLE FROM QUESTION");
        while($row = $result->fetch_assoc()){
            $questions[$row['ID']] = $row['LIBELLE'];
        }

        //On récupère les résultats
        $userResults = [];
        $result = $dbLink->query("SELECT * FROM RESULTAT");
        while($row = $result->fetch_assoc()){
            $userId = $row['ID_CLIENT'];
            $questionId = $row['ID_QUESTION'];
            $reponse = $row['REPONSE'];
            $date = $row['DATE'];
            $type = $this->getRepStyle($questionId);

            if (!isset($userResults[$userId])) {
                $userResults[$userId] = ['ID' => $userId, 'DATE' => $date, 'REPONSE' => []];
            }

            //Selon le type de réponse, on récupère la réponse a un autre endroit
            switch($type){
                case 2:
                    $userResults[$userId]['REPONSE'][$questionId] = $this->getAllContenuFromMultiIdReponse($reponse);
                    break;
                case 3:
                    $userResults[$userId]['REPONSE'][$questionId] = $this->getAllImageLabelFromMultiIdReponse($reponse);
                    break;
                default:
                    $userResults[$userId]['REPONSE'][$questionId] = $reponse;
                    break;
            }
        }
        $dbLink->close();
        return [$questions, $userResults];
    }

    private function getAllImageLabelFromMultiIdReponse($strIds): string{
        $listeId = $this->convertStringToArrayOfIntegers($strIds);

        $outputSTR = '';
        foreach($listeId as $id){
            $imageLabel = $this->getImageLabelFromIdReponse($id);
            if (is_null($imageLabel)){
                continue;
            }
            $outputSTR .= $imageLabel;
            $outputSTR .= ' / ';
        }
        return $outputSTR;
    }

    private function getImageLabelFromIdReponse($idReponse): ?string{
        $dbLink = $this->connectBd();

        $rsql = 'SELECT IMG_LABEL FROM REPONSE WHERE ID = ?';
        $stmt = $dbLink->prepare($rsql);
        $stmt->bind_param('i', $idReponse);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if (is_null($result)){
            return null;
        }
        return $result['IMG_LABEL'];
    }

    private function getAllContenuFromMultiIdReponse($strIds): string{
        $listeId = $this->convertStringToArrayOfIntegers($strIds);

        $outputSTR = '';
        foreach($listeId as $id){
            $contenu = $this->getContenuFromIdReponse($id);
            if (is_null($contenu)){
                continue;
            }
            $outputSTR .= $contenu;
            $outputSTR .= ' / ';
        }
        return $outputSTR;
    }

    private function getContenuFromIdReponse($idReponse): ?string{
        $dbLink = $this->connectBd();

        $rsql = 'SELECT CONTENU FROM REPONSE WHERE ID = ?';
        $stmt = $dbLink->prepare($rsql);
        $stmt->bind_param('i', $idReponse);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if (is_null($result)){
            return null;
        }
        return $result['CONTENU'];
    }

    private function getRepStyle($idQuestion) : int{
        // 0 : Slider  , 1 : champs libre , 2 : choix multiple text, 3 : choix multiple img
        $dbLink = $this->connectBd();

        //On test si la reponse est de type slider
        $queryReponse = mysqli_prepare($dbLink, 'SELECT CASE WHEN SLIDER IS NOT NULL THEN "True" ELSE "False" END bool_val FROM QUESTION WHERE ID = ?');
        mysqli_stmt_bind_param($queryReponse, "s", $idQuestion);
        mysqli_stmt_execute($queryReponse);
        $result = $queryReponse->get_result()->fetch_assoc();
        mysqli_stmt_close($queryReponse);

        //var_dump($result);
        if ($result['bool_val'] == 'True') {
            return 0;
        }

        //si pas slider et pas de réponse associé = champs libre
        $queryReponse = mysqli_prepare($dbLink, 'SELECT EXISTS(SELECT * FROM REPONSE WHERE ID_QUESTION = ?)');
        mysqli_stmt_bind_param($queryReponse, "s", $idQuestion);
        mysqli_stmt_execute($queryReponse);
        $result = $queryReponse->get_result()->fetch_assoc();
        mysqli_stmt_close($queryReponse);

        //var_dump($result);
        if (! $result["EXISTS(SELECT * FROM REPONSE WHERE ID_QUESTION = ?)"]) {
            return 1;
        }

        //si colonne contenue d'une des réponses n'est pas null = choix multiple text
        $queryReponse = mysqli_prepare($dbLink, 'SELECT CASE WHEN CONTENU IS NOT NULL THEN "True" ELSE "False" END bool_val FROM REPONSE WHERE ID_QUESTION = ?');
        mysqli_stmt_bind_param($queryReponse, "s", $idQuestion);
        mysqli_stmt_execute($queryReponse);
        $result = $queryReponse->get_result()->fetch_assoc();
        mysqli_stmt_close($queryReponse);

        //var_dump($result);
        if ($result['bool_val'] == 'True') {
            return 2;
        }

        //sinon on verifie quand même qu'il y a une des images = choix multiple img
        $queryReponse = mysqli_prepare($dbLink, 'SELECT CASE WHEN (IMG IS NOT NULL AND IMG_LABEL IS NOT NULL) THEN "True" ELSE "False" END bool_val FROM REPONSE WHERE ID_QUESTION = ?');
        mysqli_stmt_bind_param($queryReponse, "s", $idQuestion);
        mysqli_stmt_execute($queryReponse);
        $result = $queryReponse->get_result()->fetch_assoc();
        mysqli_stmt_close($queryReponse);

        //var_dump($result);
        if ($result['bool_val'] == 'True') {
            return 3;
        }

        //sinon on as pas réussie a reconnaître le type de reponse
        throw new Exception("Unrecognized response type (slider, champs libre, choix multiple text ou image)");
    }


    private function convertStringToArrayOfIntegers($inputString) {

        $stringArray = explode(',', $inputString);

        $integerArray = array_map('intval', $stringArray);

        return $integerArray;
    }
}