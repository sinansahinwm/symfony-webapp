<?php

namespace App\Config;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\Form\DataTransformerInterface;

abstract class EnumType extends Type
{

    private static ?array $constCacheArray = NULL;

    public static function getConstants(): array
    {
        if (self::$constCacheArray == NULL)
            self::$constCacheArray = [];

        $calledClass = get_called_class();

        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName($name, $strict = false): bool
    {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value, $strict = true): bool
    {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }

    protected static string $name;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = array_map(function ($val) {
            return "'" . $val . "'";
        }, self::getConstants());

        return "ENUM(" . implode(", ", $values) . ")";
    }

    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $this->checkValue($value);

        return $value;
    }

    /**
     * @param $value
     * @throws InvalidArgumentException
     */
    public function checkValue($value): void
    {
        if (!self::isValidValue($value)) {
            throw new InvalidArgumentException("Invalid '" . static::$name . "' value.");
        }
    }

    public function getName(): string
    {
        return static::$name;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public static function getValuesArray(DataTransformerInterface|NULL $dataTransformer = NULL): array
    {
        $constants = self::getConstants();

        if ($dataTransformer !== NULL) {

            foreach ($constants as $index => $constant) {
                $constants[$index] = $dataTransformer->transform($constant);
            }

            $constants = array_flip($constants);

        }

        return $constants;
    }

    /**
     * @throws Exception
     */
    public static function getChoicesArray(): array
    {
        throw new Exception("Not implemented");
    }
}