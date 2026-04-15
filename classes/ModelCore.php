<?php
namespace Classes;

use BackedEnum;
use PDO;
use Exceptions\ModelException;
use Enum\ModelColumnEnum;
use ModelCore as GlobalModelCore;

abstract class ModelCore
{
    /** @var string $table Nom de la table */
    public static string $table = "";

    /** @var array $configuration Configuration du modèle
     * 
     * @example
     * protected array $configuration = [
     *     ['column_name' => 'id', 'type' => ModelColumnEnum::INT, 'length' => 11, 'nullable' => false, 'default' => null, 'auto_increment' => true, 'primary_key' => true],
     *     ['column_name' => 'name', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
     *     ['column_name' => 'label', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => true, 'default' => null],
     *     ['column_name' => 'description', 'type' => ModelColumnEnum::TEXT, 'nullable' => true, 'default' => 'Pas de description.'],
     *     ['column_name' => 'version', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
     *     ['column_name' => 'active', 'type' => ModelColumnEnum::BOOLEAN, 'nullable' => false, 'default' => null],
     *     ['column_name' => 'author', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null, 'foreign_key' => true, 'references' => 'users(id_users)'],
     *     ['column_name' => 'created_at', 'type' => ModelColumnEnum::DATETIME, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
     *     ['column_name' => 'updated_at', 'type' => ModelColumnEnum::DATETIME, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
     *     ['column_name' => 'saison', 'type' => ModelColumnEnum::ENUM, 'length' => "SaisonEnum::class", 'nullable' => false, 'default' => 'ETE'],
     *     ['column_name' => 'extra_option', 'type' => ModelColumnEnum::ENUM, 'length' => "option1,option2,option3", 'nullable' => false, 'default' => 'option1'],
     * ];
     */
    public static array $configuration = [];

    /**
     * A la construction du modèle vérifier que la table existe et correspond au modèle.
     * 
     * Si la table n'existe pas, la créer en fonction des attributs du modèle.
     * 
     * Si la table existe mais ne correspond pas au modèle, lancer une exception.
     * 
     * @throws ModelException
     */
    public static function checkTable(string $tableName, string $className): bool
    {
        if(!is_subclass_of(object_or_class: $className, class: ModelCore::class)) {
            return false;
        }

        /** @var ModelCore $className */
        $configuration = $className::$configuration;
        // Vérification et création de la table
        $dbLink = self::connectBd();
        $query = "SHOW TABLES LIKE '" . $tableName . "'";
        $result = $dbLink->query($query);
        if ($result->rowCount() === 0) {
            // La table n'existe pas, la créer
            self::createTable($tableName, $configuration);
            return true;
        } 

        // La table existe, vérifier sa structure
        $query = "DESCRIBE " . $tableName;
        $result = $dbLink->query($query);
        $columns = $result->fetchAll(PDO::FETCH_COLUMN);
        foreach ($configuration as $column) {
            if (!in_array($column['column_name'], $columns)) {
                throw new ModelException("La table " . $tableName . " ne correspond pas au modèle.");
            }
        }
        return true;
    }

    public static function connectBd(): ?PDO
    {
        $dbLink = null;
        try {
            $dbLink = new PDO('mysql:host='.$_ENV["HOSTADRESS"].';dbname='.$_ENV["DATABASE"].';charset=utf8', $_ENV["USERNAME"], $_ENV["MDP"]);
            $dbLink->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException  $e) {
            dd($e->getMessage());
            /** @var \Controllers\MaintenanceController */
            $controller = ControllerCore::getInstanceByName("maintenance");
            $controller->maintenance();
            // Mail::send('lukegontier13@gmail.com', 'Erreur de connexion à la base de données', 'Erreur de connexion à la base de données : ' . $e->getMessage());
            // die;
        }
        // dump('Connected to the database');
        return $dbLink;
    }

    public function __construct(?int $id = null)
    {
        $this->setId($id);
        if($id !== null) {
            $this->autoInstance($id);
        }
    }

    public function autoInstance(int $id): void
    {
        $dbLink = $this->connectBd();
        $query = "SELECT * FROM " . $this::$table . " WHERE id_". $this::$table ." = " . $id;
        $result = $dbLink->query($query);
        if(!$result) {
            throw new ModelException("Error while fetching configuration : " . $dbLink->errorInfo()[2]);
        }
        $data = $result->fetch(PDO::FETCH_ASSOC);
        $this->hydrate($data);
    }

    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if ($key === 'id_' . $this::$table) {
                $method = 'setId';
            }
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public static function createTable(?string $tableName = null, ?array $configuration = null): void
    {
        $dbLink = self::connectBd();

        $query = "CREATE TABLE " . $tableName . " (";
        $columns = [];
        foreach ($configuration as $column) {
            if(!isset($column['type'])|| !$column['type'] instanceof ModelColumnEnum) {
                throw new ModelException("Type de colonne invalide pour la colonne " . $column['column_name']);
            }
            $type = $column['type'];
            $columnDef = '`'.$column['column_name'] . "` " . $type->value;
            if (isset($column['length'])) {
                $length = $column['length'];
                if($type === ModelColumnEnum::ENUM && enum_exists($length)){
                    $enumValues = implode("','", array_map(fn($case) => $case->value, $length::cases()));
                    $length = "'" . $enumValues . "'";
                }
                $columnDef .= "(" . $length . ")";
            }
            $columnDef .= " " . ($column['nullable'] ? "NULL" : "NOT NULL");
            if (isset($column['default'])) {
                $default = $column['default'];
                if($type === ModelColumnEnum::VARCHAR || $type === ModelColumnEnum::TEXT || $type === ModelColumnEnum::ENUM || $type === ModelColumnEnum::DATETIME) {
                    if($default !== 'CURRENT_TIMESTAMP' && !str_starts_with($default, 'CURRENT_TIMESTAMP')) {
                        $default = "'" . $default . "'";
                    }
                }
                if($type === ModelColumnEnum::BOOLEAN) {
                    $default = $default ? '1' : '0';
                }
                $columnDef .= " DEFAULT " . $default ;
            }
            if (isset($column['auto_increment']) && $column['auto_increment']) {
                $columnDef .= " AUTO_INCREMENT";
            }
            if (isset($column['primary_key']) && $column['primary_key']) {
                $columnDef .= " PRIMARY KEY";
            }
            if (isset($column['foreign_key']) && $column['foreign_key'] && isset($column['references'])) {
                $columnDef .= ", FOREIGN KEY (" . $column['column_name'] . ") REFERENCES " . $column['references'];
            }
            $columns[] = $columnDef;
        }
        $query .= implode(", ", $columns);
        $query .= ")";
        $dbLink->exec($query);
    }

    public function insert(): void
    {
        if( $this->getId() ) {
            throw new ModelException("L'enregistrement existe déjà en base de données.");
        }
        $dbLink = $this->connectBd();
        $query = "INSERT INTO " . $this::$table . " (";

        $data = get_object_vars($this);
        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            if ($key === 'id_' . $this::$table) {
                continue;
            }
            $columns[] = $key;
            $values[':' . $key] = $value;
        }
        $query .= implode(", ", $columns);
        $query .= " ) VALUES (";
        foreach ($values as $key => &$val) {
            if($val instanceof \BackedEnum) {
                $val = $val->value;
            }
            $query .= $key;
            if($val !== end($values)) {
                $query .= ", ";
            }
        }
        $query .= " )";
        $request = $dbLink->prepare($query);
        $request->execute($values);
        $this->setId($dbLink->lastInsertId());
    }

    public function update(): void
    {
        $dbLink = $this->connectBd();
        $query = "UPDATE " . $this::$table . " SET ";

        $data = get_object_vars($this);
        $colums = [];
        $values = [];
        foreach ($data as $key => $value) {
            if ($key === 'id_' . $this::$table) {
                continue;
            }
            $colums[] = $key . " = :" . $key;
            $values[':' . $key] = $value;
        }
        $query .= implode(", ", array: $colums);
        $query .= " WHERE id_" . $this::$table . " = " . $data['id_' . $this::$table];
        
        $request = $dbLink->prepare($query);
        $request->execute($values);
    }

    public function delete(): void
    {
        $dbLink = $this->connectBd();
        $data = get_object_vars($this);
        $query = "DELETE FROM " . $this::$table . " WHERE id_" . $this::$table . " = " . $data['id_' . $this::$table];
        $dbLink->exec($query);
    }

    public function getId(): ?int
    {
        $data = get_object_vars($this);
        return $data['id_' . $this::$table];
    }

    abstract public function setId(?int $id): void;
}
