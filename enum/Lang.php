<?php
namespace Enum;

enum LangEnum: string{
    case FR = 'fr';
    case EN = 'en';

    public static function getLangDetails(string $value): array
    {
        return match ($value) {
            self::FR => ['name' => 'Français', 'icon' => ''],
            self::EN => ['name' => 'English', 'icon' => ''],
        };
    }

    public static function getCurrentLang(): string
    {
        return $_SESSION['lang'] ?? 'fr';
    }

    public static function getCodes(): array
    {
        return array_map(fn($lang) => $lang->value, self::toArray());
    }

    public static function toArray(): array
    {
        return [
            self::FR,
            self::EN,
        ];
    }

    public static function trans(string $value, string $domain = 'base'): string
    {
        // Create a Language repository to get the translations
        // if the translation is not found, return the value
        return $value;
    }
}
