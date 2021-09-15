<?php

namespace Bundle\UIBundle\Tests\Integration\ParamConverter;

use Bundle\UIBundle\Tests\ExampleInstance\InputContract\InvalidInputContract;
use Bundle\UIBundle\Tests\ExampleInstance\InputContract\ValidInputContract;
use Bundle\UIBundle\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Bundle\UIBundle\ParamConverter\ContractResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ContractResolverTest extends IntegrationTestCase
{
    protected ContractResolver $resolver;

    public function setUp(): void
    {
        parent::setUp();
//        $this->resolver = self::$container->get(ContractResolver::class);
    }

    public function testSupportsSuccess(): void
    {
        $argumentMetadata = new ArgumentMetadata(
            self::$faker->name,
            ValidInputContract::class,
            self::$faker->boolean,
            self::$faker->boolean,
            self::$faker->text
        );

        self::assertTrue($this->resolver->supports(new Request(), $argumentMetadata));
    }

    public function testSupportsNotSuccess(): void
    {
        $argumentMetadata = new ArgumentMetadata(
            self::$faker->name,
            InvalidInputContract::class,
            self::$faker->boolean,
            self::$faker->boolean,
            self::$faker->text
        );

        self::assertFalse($this->resolver->supports(new Request(), $argumentMetadata));
    }

    public function testResolve(): void
    {
        self::markTestSkipped();
    }
}
