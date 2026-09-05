<?php

namespace Models;

use Enum\ModelColumnEnum;
use Classes\ModelCore;

class ConfigurationModel extends ModelCore
{
    public static string $table = "configuration";

    public static array $configuration = [
        ['column_name' => 'id_configuration', 'type' => ModelColumnEnum::INT, 'length' => 11, 'nullable' => false, 'default' => null, 'auto_increment' => true, 'primary_key' => true],
        ['column_name' => 'name', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
        ['column_name' => 'value', 'type' => ModelColumnEnum::TEXT, 'nullable' => false, 'default' => null],
    ];

    protected int $id_configuration;
    protected string $name;
    protected string $value;

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