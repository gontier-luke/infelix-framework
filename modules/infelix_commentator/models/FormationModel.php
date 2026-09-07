<?php

namespace Models;

use Enum\ModelColumnEnum;
use Classes\ModelCore;
use Classes\Interfaces\SliderableInterface;
class FormationModel extends ModelCore implements SliderableInterface
{
    public static string $table = "formation";

    private int $id_formation;
    protected string $name;
    protected string $label;
    protected string $description;
    protected string $image;
    protected string $link;
    protected string $linkTarget;
    protected bool $active;

    /** @var array $configuration Configuration du modèle */
    public static array $configuration = [
        ['column_name' => 'id_formation', 'type' => ModelColumnEnum::INT, 'length' => 11, 'nullable' => false, 'default' => null, 'auto_increment' => true, 'primary_key' => true],
        ['column_name' => 'name', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
        ['column_name' => 'label', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => true, 'default' => null],
        ['column_name' => 'description', 'type' => ModelColumnEnum::TEXT, 'nullable' => true, 'default' => 'Pas de description.'],
        ['column_name' => 'image', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
        ['column_name' => 'link', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null,],
        ['column_name' => 'linkTarget', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => '_blank'],
        ['column_name' => 'active', 'type' => ModelColumnEnum::BOOLEAN, 'nullable' => false, 'default' => false],
        ['column_name' => 'created_at', 'type' => ModelColumnEnum::DATETIME, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ['column_name' => 'updated_at', 'type' => ModelColumnEnum::DATETIME, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'],
    ];

    private const IMG_REPO = IMAGE_LINK . 'formations/';

    public function __construct(?int $id = null)
    {
        if ($id !== null) {
            $this->autoInstance($id);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id_formation;
    }

    public function setId(?int $id): void
    {
        $this->id_formation = $id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
    
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): string
    {
        return self::IMG_REPO . $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getTarget(): string
    {
        return $this->linkTarget;
    }

    public function setLink(string $link, string $target = '_blank'): void
    {
        $this->link = $link;
        $this->linkTarget = $target;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}