<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Tests\Unit;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    protected static Generator $faker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$faker = Factory::create();
    }
}
