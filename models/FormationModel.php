<?php

class FormationModel extends ModelCore implements SliderableInterface
{
    protected string $table = "formation";

    private int $id_formation;
    protected string $name;
    protected string $label;
    protected string $description;
    protected string $image;
    protected string $link;
    protected bool $active;

    private const IMG_REPO = 'formations/';

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

    public function getId(): int
    {
        return $this->id_formation;
    }

    public function setId(int $id): void
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

    public function setLink(string $link): void
    {
        $this->link = $link;
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