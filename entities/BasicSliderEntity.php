<?php
namespace Entities;

use Classes\Interfaces\SliderableInterface;

class BasicSliderEntity implements SliderableInterface
{
    protected string $name;
    protected string $label;
    protected string $description;
    protected string $image;
    protected string $link;
    protected bool $active;

    public function __construct(string $name, string $label, string $description, string $image, string $link, bool $active)
    {
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->image = $image;
        $this->link = $link;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
        return $this->image;
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

    public static function getAll(): array
    {
        return [];
    }
}