<?php

namespace Classes\Interfaces;

interface SliderableInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getLabel(): string;

    public function setLabel(string $label): void;

    public function getDescription(): string;

    public function setDescription(string $description): void;

    public function getImage(): string;

    public function setImage(string $image): void;

    public function getLink(): string;

    public function setLink(string $link): void;

    public function isActive(): bool;

    public function setActive(bool $active): void;
}