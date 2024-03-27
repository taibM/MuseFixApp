<?php
// src/Doctrine/Type/EnumType.php


namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EnumType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // Assuming MySQL 8.0 platform, update this as needed for other platforms
        return "ENUM('" . implode("', '", $fieldDeclaration['enum']) . "') COMMENT '(DC2Type:enum)'";
    }

    public function getName()
    {
        return 'enum';
    }
}
