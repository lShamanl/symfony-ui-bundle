<?php

declare(strict_types=1);

namespace Bundle\UIBundle\Tests\Unit\Core\Service;

use Bundle\UIBundle\Core\Service\RequestParser;
use Bundle\UIBundle\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestParserTest extends UnitTestCase
{
    /**
     * @dataProvider resolveTestCase
     * @param array $query
     * @param array $post
     * @param array $content
     */
    public function testResolve(array $query, array $post, array $content): void
    {
        $expected = array_merge($query, $post, $content);

        $request = new Request(
            query: $query,
            request: $post,
            content: (string) json_encode($content),
        );

        $requestParser = new RequestParser();
        $payload = $requestParser->parse($request);

        self::assertEquals($expected, $payload);
    }

    public function testResolveWithNull(): void
    {
        $request = new Request(
            query: [],
            request: [],
            content: '',
        );

        $requestParser = new RequestParser();
        $payload = $requestParser->parse($request);

        self::assertEquals([], $payload);
    }

    public function testResolveWithInvalidContent(): void
    {
        $request = new Request(
            query: [],
            request: [],
            content: 'invalid-json-value',
        );

        $requestParser = new RequestParser();
        $payload = $requestParser->parse($request);

        self::assertEquals([], $payload);
    }

    public function resolveTestCase(): array
    {
        $payloadOne = $this->getPayloadOne();
        $payloadTwo = $this->getPayloadTwo();
        $payloadThree = $this->getPayloadThree();

        return [
            'onlyGetQuery' => [
                'get' => array_merge($payloadOne, $payloadTwo, $payloadThree),
                'post' => [],
                'content' => [],
            ],
            'onlyJson' => [
                'get' => [],
                'post' => [],
                'content' => array_merge($payloadOne, $payloadTwo, $payloadThree),
            ],
            'onlyPostQuery' => [
                'get' => [],
                'post' => array_merge($payloadOne, $payloadTwo, $payloadThree),
                'content' => [],
            ],
            'mixedPostQueryAndGetQuery' => [
                'get' => $payloadOne,
                'post' => array_merge($payloadTwo, $payloadThree),
                'content' => [],
            ],
            'mixedJsonAndGetQuery' => [
                'get' => $payloadOne,
                'post' => [],
                'content' => array_merge($payloadTwo, $payloadThree),
            ],
            'mixedJsonAndPostQuery' => [
                'get' => [],
                'post' => $payloadOne,
                'content' => array_merge($payloadTwo, $payloadThree),
            ],
            'mixedAll' => [
                'get' => $payloadOne,
                'post' => $payloadTwo,
                'content' => $payloadThree,
            ],
        ];
    }

    protected function getPayloadOne(): array
    {
        return [
            'field_1' => 'value_1',
            'field_2' => 'value_2',
            'field_3' => 'value_3',
        ];
    }

    protected function getPayloadTwo(): array
    {
        return [
            'field_4' => 'value_4',
            'field_5' => 'value_5',
            'field_6' => 'value_6',
        ];
    }

    protected function getPayloadThree(): array
    {
        return [
            'field_7' => 'value_7',
            'field_8' => 'value_8',
            'field_9' => 'value_9',
        ];
    }
}
