<?php

class ConfigurationModel extends ModelCore
{
    protected string $table = "configuration";

    private int $id_configuration;
    private string $name;
    private string $value;

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

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getId(): int
    {
        return $this->id_configuration;
    }

    public function setId(int $id): void
    {
        $this->id_configuration = $id;
    }

}