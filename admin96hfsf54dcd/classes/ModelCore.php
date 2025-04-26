<?php

class ModelCore
{

    public function connectBd():mysqli
    {
        $dbLink = mysqli_connect($_ENV["HOSTNAME"], $_ENV["USERNAME"], $_ENV["MDP"]) or die('Erreur de connexion au serveur : ' . mysqli_connect_error());
        mysqli_select_db($dbLink, $_ENV["DATABASE"]) or die('Erreur dans la sélection de la base : ' . mysqli_error($dbLink));
        if (!($dbLink instanceof mysqli)) {
            throw new Exception('Database connection error');
        }
        return $dbLink;
    }

    protected function processColumn($keyAsked, $params):array
    {
        if ($keyAsked == 'image' && empty($_FILES[$keyAsked]['name'][0])) {
            return ['', '', '', []];
        }

        if (in_array($keyAsked, ['image', 'imageResp']) && !empty($_FILES[$keyAsked]['name'][0])) {
            return $this->handleImageColumn($keyAsked);
        }
        if ($keyAsked === 'slider') {
            return $this->handleIntColumn($params);
        }

        if($keyAsked === 'imageDescription' && empty($params[$keyAsked][0])){
            return ['', '', '', []];
        }

        if ($keyAsked === 'imageDescription' && !empty($params['imageDescription'][0])) {
            return $this->handleImageDescriptionColumn($params);
        }

        # by default, return a string column of the current variable
        if (isset($params[$keyAsked])) {
            $column = $keyAsked;
            $nextBind = 's';
            $newSqlParams = [$params[$keyAsked]];
            $newValues = ',?';

            return [$column, $nextBind, $newValues, $newSqlParams];
        }
    }

    protected function handleIntColumn($params):array
    {
        $column = 'slider';
        $nextBind = 'i';
        $newSqlParams = [intval($params['slider'])];
        $newValues = ',?';

        return [$column, $nextBind, $newValues, $newSqlParams];
    }

    protected function handleImageColumn($keyAsked):array
    {
        $nextBind = 's';
        $column = 'img';
        $newSqlParams = [base64_encode(file_get_contents($_FILES[$keyAsked]['tmp_name'][0]))];
        $newValues = ',?';

        if (!empty($_FILES[$keyAsked]['name'][1])) {
            $column .= '`,`img_second';
            $nextBind .= 's';
            $newSqlParams[] = base64_encode(file_get_contents($_FILES[$keyAsked]['tmp_name'][1]));
            $newValues .= ',?';
        }

        return [$column, $nextBind, $newValues, $newSqlParams];
    }

    protected function handleImageDescriptionColumn($params):array
    {
        $nextBind = 's';
        $column = 'img_desc';
        $newSqlParams = [$params['imageDescription'][0]];
        $newValues = ',?';

        if (!empty($params['imageDescription'][1])) {
            $column .= '`,`img_second_desc';
            $nextBind .= 's';
            $newSqlParams[] = $params['imageDescription'][1];
            $newValues .= ',?';
        }

        return [$column, $nextBind, $newValues, $newSqlParams];
    }

    protected function executeInsertQuery($request, $bind, $sqlParams):int
    {
        $dbLink = $this->connectBd();

        $query = $dbLink->prepare($request);

        if ($query === false) {
            throw new Exception($dbLink->error);
        }

        // Converting the bind string into an array to use with ... operator
        $query->bind_param($bind, ...$sqlParams);

        if (!$query->execute()) {
            $query->close();
            throw new Exception($query->error);
        }

        $query->close();
        return $dbLink->insert_id;
    }
}
