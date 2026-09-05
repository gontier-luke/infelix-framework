<?php

namespace Models;

use Enum\ModelColumnEnum;
use Classes\ModelCore;

class AdminUserModel extends ModelCore
{
    public static string $table = "admin_user";

    public static array $configuration = [
        ['column_name' => 'id_admin_user', 'type' => ModelColumnEnum::INT, 'length' => 11, 'nullable' => false, 'default' => null, 'auto_increment' => true, 'primary_key' => true],
        ['column_name' => 'username', 'type' => ModelColumnEnum::TEXT, 'nullable' => false, 'default' => null],
        ['column_name' => 'mail_address', 'type' => ModelColumnEnum::VARCHAR, 'length' => 255, 'nullable' => false, 'default' => null],
        ['column_name' => 'password', 'type' => ModelColumnEnum::TEXT, 'nullable' => true, 'default' => null],
    ];

    protected ?int $id_admin_user;
    protected string $username;
    protected string $mail_address;
    protected string $password;
    protected ?string $token;
    public function __construct(?int $id = null)
    {
        $this->id_admin_user = $id;
        parent::__construct($id);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getMailAddress(): string
    {
        return $this->mail_address;
    }

    public function setMailAddress(string $mail_address): void
    {
        $this->mail_address = $mail_address;
    }

    public function getId(): ?int
    {
        return $this->id_admin_user;
    }

    public function setId(?int $id): void
    {
        $this->id_admin_user = $id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}