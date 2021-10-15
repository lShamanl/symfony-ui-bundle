<?php

namespace Bundle\UIBundle\Tests\Unit\ParamConverter;

use Bundle\UIBundle\Core\Service\InputContractResolver;
use Bundle\UIBundle\Core\Service\RequestParser;
use Bundle\UIBundle\Tests\ExampleInstance\InputContract\InvalidInputContract;
use Bundle\UIBundle\Tests\ExampleInstance\InputContract\ValidInputContract;
use Bundle\UIBundle\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use Bundle\UIBundle\ParamConverter\ContractResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ContractResolverTest extends UnitTestCase
{
    protected ContractResolver $resolver;

    public function setUp(): void
    {
        parent::setUp();

        $this->resolver = new ContractResolver(
            self::createMock(InputContractResolver::class),
            self::createMock(RequestParser::class)
        );
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
}
