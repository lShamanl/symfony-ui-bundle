<?php

namespace Bundle\UIBundle\Tests\Unit\Core\Contract;

use Bundle\UIBundle\Core\Contract\ApiFormatter;
use Bundle\UIBundle\Tests\Unit\UnitTestCase;

/**
 * @covers \Bundle\UIBundle\Core\Contract\ApiFormatter
 */
class ApiFormatterTest extends UnitTestCase
{
    /**
     * @dataProvider getCases
     * @param array<string,string> $data
     * @param int $status
     * @param array<string,string> $errors
     * @param bool $isErrors
     */
    public function testPrepare(array $data, int $status, array $errors, bool $isErrors): void
    {
        $prepared = ApiFormatter::prepare($data, $status, $errors);

        self::assertEquals($data, $prepared['data']);
        self::assertEquals($status, $prepared['status']);
        self::assertEquals($errors, $prepared['errors']);
        self::assertEquals($isErrors, $prepared['isError']);
    }

    /**
     * @dataProvider getCases
     * @param array<string,string> $data
     * @param int $status
     * @param array<string,string> $errors
     * @param bool $isErrors
     */
    public function testToArray(array $data, int $status, array $errors, bool $isErrors): void
    {
        $apiOutputFormatter = new ApiFormatter($data, $status, $errors);

        $toArray = $apiOutputFormatter->toArray();

        self::assertEquals($data, $toArray['data']);
        self::assertEquals($status, $toArray['status']);
        self::assertEquals($errors, $toArray['errors']);
        self::assertEquals($isErrors, $toArray['isError']);
    }

    /**
     * @dataProvider getCases
     * @param array<string,string> $data
     * @param int $status
     * @param array<string,string> $errors
     * @param bool $isErrors
     */
    public function testCreate(array $data, int $status, array $errors, bool $isErrors): void
    {
        $apiOutputFormatter = new ApiFormatter($data, $status, $errors);

        self::assertEquals($data, $apiOutputFormatter->data);
        self::assertEquals($status, $apiOutputFormatter->status);
        self::assertEquals($errors, $apiOutputFormatter->errors);
        self::assertEquals($isErrors, $apiOutputFormatter->isError);
    }

    public function getCases(): array
    {
        $data = [
            'field_1' => 'value_1',
            'field_2' => 'value_2',
        ];

        return [
            'withErrors' => [
                'data' => $data,
                'status' => 400,
                'errors' => [
                    'field_3' => 'value_3',
                ],
                'isErrors' => true
            ],
            'withoutErrors' => [
                'data' => $data,
                'status' => 200,
                'errors' => [],
                'isErrors' => false
            ],
        ];
    }
}
