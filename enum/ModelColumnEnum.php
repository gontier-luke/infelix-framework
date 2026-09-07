<?php

namespace Enum;

/**
 * Enumération des types de colonnes en base de données pour la création et la validation des modèles.
 */
enum ModelColumnEnum: string
{
    // Integer types
    case INT = 'INT';
    case TINYINT = 'TINYINT';
    case SMALLINT = 'SMALLINT';
    case BIGINT = 'BIGINT';

    // String types
    case VARCHAR = 'VARCHAR';
    case TEXT = 'TEXT';
    case TINYTEXT = 'TINYTEXT';
    case MEDIUMTEXT = 'MEDIUMTEXT';
    case LONGTEXT = 'LONGTEXT';

    // Date and time types
    case DATE = 'DATE';
    case DATETIME = 'DATETIME';

    // Other types
    case BOOLEAN = 'BOOLEAN';
    case FLOAT = 'FLOAT';
    case DOUBLE = 'DOUBLE';
    case DECIMAL = 'DECIMAL';
    case BLOB = 'BLOB';
    case ENUM = 'ENUM';
    case FILE = 'FILE';
}
    