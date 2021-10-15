<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Tests\Integration;

use ArrayAccess;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use PHPStan\Testing\TestCase;
use ReflectionException;
use ReflectionProperty;

class IntegrationTestCase extends TestCase
{
    protected EntityManagerInterface $entityManager;
    protected static Generator $faker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$faker = Factory::create();
        #todo: здесь не хватает контейнера (надо переопределить в phpunit.xml неймспейс Кернела, ну и написать его),
        # Научиться создавать экземпляр приложения для тестов.
    }

    /**
     * @param object $object
     * @param string $property
     * @param mixed $value
     * @throws ReflectionException
     */
    protected static function bindMock(object $object, string $property, $value): void
    {
        $className = get_class($object);
        try {
            $refProperty = self::getReflectionProperty($className, $property);
            $refProperty->setValue($object, $value);
        } catch (ReflectionException $reflectionException) {
            if ($object instanceof ArrayAccess) {
                $object[$property] = $value;
            } else {
                throw $reflectionException;
            }
        }
    }

    /**
     * @param class-string<mixed> $className
     * @param string $property
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    private static function getReflectionProperty(string $className, string $property): ReflectionProperty
    {
        $refProperty = new ReflectionProperty($className, $property);
        $refProperty->setAccessible(true);
        return $refProperty;
    }
}
