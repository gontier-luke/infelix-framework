<?php

class ModelCore
{
    protected string $table;

    protected function connectBd(): ?PDO
    {
        $dbLink = null;
        try {
            $dbLink = new PDO('mysql:host='.$_ENV["HOSTADRESS"].';dbname='.$_ENV["DATABASE"].';charset=utf8', $_ENV["USERNAME"], $_ENV["MDP"]);
        } catch (Exception $e) {
            /** @var MaintenanceController */
            $controller = ControllerCore::getInstanceByName("maintenance");
            $controller->maintenance();
            dd($e->getMessage());
            // Mail::send('lukegontier13@gmail.com', 'Erreur de connexion à la base de données', 'Erreur de connexion à la base de données : ' . $e->getMessage());
            // die;
        }
        // dump('Connected to the database');
        return $dbLink;
    }

    public static function getConnection(): PDO
    {
        return (new self())->connectBd();
    }

    protected function autoInstance(int $id): void
    {
        $dbLink = $this->connectBd();
        $query = "SELECT * FROM " . $this->table . " WHERE id_". $this->table ." = " . $id;
        $result = $dbLink->query($query);
        if(!$result) {
            throw new Exception("Error while fetching configuration : " . $dbLink->errorInfo()[2]);
        }
        $data = $result->fetch(PDO::FETCH_ASSOC);
        $this->hydrate($data);
    }

    protected function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if ($key === 'id_' . $this->table) {
                $method = 'setId';
            }
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
}