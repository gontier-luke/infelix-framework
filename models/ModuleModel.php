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
        ['column_name' => 'installed', 'type' => ModelColumnEnum::BOOLEAN, 'nullable' => false, 'default' => false],
        ['column_name' => 'author', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null,],
        ['column_name' => 'created_at', 'type' => ModelColumnEnum::DATETIME, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ['column_name' => 'updated_at', 'type' => ModelColumnEnum::DATETIME, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
    ];

    /** @var int $id_module
     *
     * Identifiant unique du module.
     */
    protected ?int $id_module;

    /** @var ?string $name
     *
     * Le nom du module pour l'identification.
    */
    protected ?string $name;

    /** @var ?string $label
     *
     * Le label du module pour l'affichage.
     */
    protected ?string $label;

    /** @var ?string $description 
     * 
     * La description du module pour ses informations et détails.
    */
    protected ?string $description;

    /** @var ?string $version
     * 
     * La version actuelle du module.
    */
    protected ?string $version;

    /** @var ?bool $active
     * 
     * Indique si le module est actif ou non.
    */
    protected ?bool $active;

    /** @var ?bool $installed
     * 
     * Indique si le module est installé ou non.
    */
    protected ?bool $installed;

    /** @var ?string $author
     * 
     * Le nom de l'auteur du module.
    */
    protected ?string $author;

    /** @var ?string $created_at
     * 
     * La date de création du module.
    */
    protected ?string $created_at;

    /** @var ?string $updated_at
     * 
     * La date de mise à jour du module.
    */
    protected ?string $updated_at;
    
    /**
     * Module constructor.
     * @param int|null $id_module L'identifiant unique du module (optionnel).
     */
    public function __construct(?int $id_module = null) {
        $this->id_module = $id_module;
        parent::__construct($id_module);
    }

    /**
     * Récupère le nom du module.
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Définit le nom du module.
     * @param string $name
     */
    public function setName(string $name): ModuleModel {
        $this->name = $name;
        return $this;
    }

    /**
     * Récupère le label du module.
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * Définit le label du module.
     * @param string $label
     */
    public function setLabel(string $label): ModuleModel {
        $this->label = $label;
        return $this;
    }

    /**
     * Récupère la description du module.
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Définit la description du module.
     * @param string $description
     */
    public function setDescription(string $description): ModuleModel {
        $this->description = $description;
        return $this;
    }

    /**
     * Récupère la version du module.
     * @return string
     */
    public function getVersion(): string {
        return $this->version;
    }

    /**
     * Définit la version du module.
     * @param string $version
     */
    public function setVersion(string $version): ModuleModel {
        $this->version = $version;
        return $this;
    }

    /**
     * Indique si le module est actif.
     * @return bool
     */
    public function isActive(): bool {
        return $this->active;
    }

    /**
     * Définit l'état actif du module.
     * @param bool $active
     */
    public function setActive(bool $active): ModuleModel {
        $this->active = $active;
        return $this;
    }

    /**
     * Récupère le nom de l'auteur du module.
     * @return string
     */
    public function getAuthor(): string {
        return $this->author;
    }

    /**
     * Définit le nom de l'auteur du module.
     * @param string $author
     */
    public function setAuthor(string $author): ModuleModel {
        $this->author = $author;
        return $this;
    }

    /**
     * Récupère la date de création du module.
     * @return string
     */
    public function getCreatedAt(): string {
        return $this->created_at;
    }

    /**
     * Définit la date de création du module.
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at): ModuleModel {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * Récupère l'état d'installation du module.
     * @return bool
     */
    public function isInstalled(): bool {
        return $this->installed;
    }

    /**
     * Définit l'état d'installation du module.
     * @param bool $installed
     */
    public function setInstalled(bool $installed): ModuleModel {
        $this->installed = $installed;
        return $this;
    }

    /**
     * Récupère la date de mise à jour du module.
     * @return string
     */
    public function getUpdatedAt(): string {
        return $this->updated_at;
    }

    /**
     * Définit la date de mise à jour du module.
     * @param string $updated_at
     */
    public function setUpdatedAt(string $updated_at): ModuleModel {
        $this->updated_at = $updated_at;
        return $this;
    }
}