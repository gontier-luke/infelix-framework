<?php

namespace Models;

use Enum\ModelColumnEnum;
use Classes\ModelCore;
use Enum\CalendrierUserRoleEnum;

class CalendrierUserModel extends ModelCore
{
    public static string $table = "calendrier_user";

    public static array $configuration = [
        ['column_name' => 'id_calendrier_user', 'type' => ModelColumnEnum::INT, 'length' => 11, 'nullable' => false, 'default' => null, 'auto_increment' => true, 'primary_key' => true],
        ['column_name' => 'username', 'type' => ModelColumnEnum::TEXT, 'nullable' => false, 'default' => null],
        ['column_name' => 'password', 'type' => ModelColumnEnum::TEXT, 'nullable' => false, 'default' => null],
        ['column_name' => 'role', 'type' => ModelColumnEnum::ENUM, 'length'=> CalendrierUserRoleEnum::class, 'nullable' => false, 'default' => null],
    ];

    protected ?int $id_calendrier_user;
    protected string $username;
    protected string $password;
    protected CalendrierUserRoleEnum $role;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getId(): ?int
    {
        return $this->id_calendrier_user;
    }

    public function setId(?int $id): void
    {
        $this->id_calendrier_user = $id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRole(): CalendrierUserRoleEnum
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = CalendrierUserRoleEnum::from($role);
    }

}