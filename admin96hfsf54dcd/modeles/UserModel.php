<?php

class UserModel extends ModelCore{

    public function userExist(string $login, string $inputPassword):bool
    {
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('SELECT count(*) FROM COMPTES WHERE `identifiant`= ?');
        $query->bind_param('s',$login);
        $query->execute();
        $query->bind_result($compte);
        $query->fetch();
        if ( $compte >= 1 ) {
            $dbLink->close();
            $dbLink= $this->connectBd();
            $password = $dbLink->prepare('SELECT mdp FROM COMPTES WHERE `identifiant`= ?');
            $password->bind_param('s',$login);
            $password->execute();
            $password->bind_result($passwordHash);
            $password->fetch();
            if (password_verify($inputPassword, $passwordHash)) {
                return true;
            }
        }
        return false;
    }

    public function isOwner(string $login):bool
    {
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('SELECT `owner` FROM COMPTES WHERE `identifiant`= ?');
        $query->bind_param('s',$login);
        $query->execute();
        $query->bind_result($owner);
        $query->fetch();

        return $owner == 1;
    }
    
    public function getAllUser():array
    {
        $users = [];
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('SELECT `date`, `identifiant` FROM COMPTES');
        $query->execute();
        $result = $query->get_result();

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    public function removeUser(string $login):bool
    {
        if ($login == $_SESSION['login']) {
            return false;
        }
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('DELETE FROM COMPTES WHERE `identifiant`=?');
        $query->bind_param('s',$login);
        $query->execute();

        return true;
    }

    public function addUser(string $login, string $password):bool
    {
        if ($login == $_SESSION['login']) {
            return false;
        }

        $password = password_hash($password, PASSWORD_DEFAULT);
        
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('INSERT INTO COMPTES (`identifiant`,`mdp`,`date`) VALUES (?,?, NOW())');
        $query->bind_param('ss',$login,$password);

        return $query->execute();;
    }
}