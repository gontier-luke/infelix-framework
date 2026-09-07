<?php

namespace Models;

use Enum\ModelColumnEnum;
use Classes\ModelCore;

class PageModel extends ModelCore
{
    public static string $table = "page";

    public static array $configuration = [
        ['column_name' => 'id_page', 'type' => ModelColumnEnum::INT, 'length' => 11, 'nullable' => false, 'default' => null, 'auto_increment' => true, 'primary_key' => true],
        ['column_name' => 'app_name', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
        ['column_name' => 'name', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
        ['column_name' => 'active', 'type' => ModelColumnEnum::BOOLEAN, 'nullable' => false, 'default' => null],
    ];

    protected ?int $id_page;
    protected string $app_name = '';
    protected string $name;
    protected bool $active;
    protected string $password;
    protected ?string $token;
    public function __construct(?int $id = null)
    {
        $this->id_page = $id;
        parent::__construct($id);
    }

    public function getAppName(): string
    {
        return $this->app_name;
    }

    public function setAppName(string $app_name): void
    {
        $this->app_name = $app_name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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