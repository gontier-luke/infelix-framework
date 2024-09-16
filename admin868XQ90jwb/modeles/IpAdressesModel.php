<?php

class IpAdressesModel extends ModelCore{
    
    public function getAllIp():array
    {
        $ipAdresses = [];
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('SELECT `date_last_co`, `ip` FROM IP');
        $query->execute();
        $result = $query->get_result();

        while ($row = $result->fetch_assoc()) {
            $ipAdresses[] = $row;
        }

        return $ipAdresses;
    }

    public function removeIp(string $ip):bool
    {
        $dbLink = $this->connectBd();
        $query = $dbLink->prepare('DELETE FROM IP WHERE `ip`=?');
        $query->bind_param('s',$ip);
        $query->execute();

        return true;
    }
}