<?php

namespace Models;

use Classes\ModelCore;
use Enum\ModelColumnEnum;

class ModuleModel extends ModelCore {
    public static string $table = "module";

    /** @var array $configuration Configuration du modèle */
    public static array $configuration = [
        ['column_name' => 'id_module', 'type' => ModelColumnEnum::INT, 'length' => 11, 'nullable' => false, 'default' => null, 'auto_increment' => true, 'primary_key' => true],
        ['column_name' => 'name', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
        ['column_name' => 'label', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => true, 'default' => null],
        ['column_name' => 'description', 'type' => ModelColumnEnum::TEXT, 'nullable' => true, 'default' => 'Pas de description.'],
        ['column_name' => 'version', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
        ['column_name' => 'active', 'type' => ModelColumnEnum::BOOLEAN, 'nullable' => false, 'default' => false],
        ['column_name' => 'author', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null,],
        ['column_name' => 'created_at', 'type' => ModelColumnEnum::DATETIME, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ['column_name' => 'updated_at', 'type' => ModelColumnEnum::DATETIME, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
    ];

    /** @var ?int $id_module
     *
     * Identifiant unique du module.
     */
    protected ?int $id_module;

    /** @var string $name
     *
     * Le nom du module pour l'identification.
    */
    protected string $name;

    /** @var string $label
     *
     * Le label du module pour l'affichage.
     */
    protected string $label;

    /** @var string $description 
     * 
     * La description du module pour ses informations et détails.
    */
    protected string $description;

    /** @var string $version
     * 
     * La version actuelle du module.
    */
    protected string $version;

    /** @var bool $active
     * 
     * Indique si le module est actif ou non.
    */
    protected bool $active;

    /** @var string $author
     * 
     * Le nom de l'auteur du module.
    */
    protected string $author;

    /** @var string $created_at
     * 
     * La date de création du module.
    */
    protected string $created_at;

    /**
     * Module constructor.
     * @param string $name
     * @param string $label
     * @param string $description
     * @param string $version
     * @param bool $active
     * @param string $author
     * @param string $created_at
     */
    public function __construct(?int $id_module, string $name, string $label, string $description, string $version, bool $active, string $author, string $created_at) {
        parent::__construct($id_module);
        $this->id_module = $id_module;
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->version = $version;
        $this->active = $active;
        $this->author = $author;
        $this->created_at = $created_at;
    }

    /**
     * Crée une instance de Module et l'enregistre en base de données.
     * @param string $name
     * @param string $label
     * @param string $description
     * @param string $version
     * @param bool $active
     * @param string $author
     * @return ModuleModel
     */
    public static function create(string $name, string $label, string $description, string $version, bool $active, string $author, string $created_at): ModuleModel {
        return new ModuleModel(null, $name, $label, $description, $version, $active, $author, $created_at);
    }

    /**
     * Récupère le nom du module.
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Récupère le label du module.
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * Récupère la description du module.
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Récupère la version du module.
     * @return string
     */
    public function getVersion(): string {
        return $this->version;
    }

    /**
     * Indique si le module est actif.
     * @return bool
     */
    public function isActive(): bool {
        return $this->active;
    }

    /**
     * Récupère le nom de l'auteur du module.
     * @return string
     */
    public function getAuthor(): string {
        return $this->author;
    }

    /**
     * Récupère la date de création du module.
     * @return string
     */
    public function getCreatedAt(): string {
        return $this->created_at;
    }

    /**
    * Met à jour l'id du module
    * @return void
    */
    public function setId(?int $id): void {
        $this->id_module = $id;
    }
}