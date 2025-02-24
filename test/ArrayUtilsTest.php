<?php
namespace Maweb\ArrayUtils\Test;

use MawebDK\ArrayUtils\ArrayUtils;
use MawebDK\ArrayUtils\ArrayUtilsException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrayUtilsTest extends TestCase
{
    /**
     * @throws ArrayUtilsException
     */
    #[DataProvider('dataProviderDecodeJson')]
    public function testDecodeJson(string $json, array $expectedArray)
    {
        $this->assertSame(
            expected: $expectedArray,
            actual: ArrayUtils::decodeJson(json: $json)
        );
    }

    public static function dataProviderDecodeJson(): array
    {
        return [
            '[]' => [
                'json'          => '[]',
                'expectedArray' => []
            ],
            '{}' => [
                'json'          => '{}',
                'expectedArray' => []
            ],
            'Simple' => [
                'json'          => '{"name":"John Doe","age":18,"isAdult":true,"isChild":false,"children":null}',
                'expectedArray' => ['name' => 'John Doe', 'age' => 18, 'isAdult' => true, 'isChild' => false, 'children' => null]
            ],
            'JSON with simple array' => [
                'json'          => '{"name":"John Doe","children":["Anna","Brian","Charlie"]}',
                'expectedArray' => ['name' => 'John Doe', 'children' => ['Anna', 'Brian', 'Charlie']]
            ],
            'JSON with associative array' => [
                'json'          => '{"resource":"cURL","options":{"url":"maweb.dk","timeout":10,"useSslVerification":false}}',
                'expectedArray' =>
                    ['resource' => 'cURL', 'options' => ['url' => "maweb.dk", 'timeout' => 10, 'useSslVerification' => false]]
            ],
        ];
    }

    #[DataProvider('dataProviderDecodeJson_ArrayUtilsException')]
    public function testDecodeJson_ArrayUtilsException(string $json, string $expectedExceptionMessage)
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: $expectedExceptionMessage);

        ArrayUtils::decodeJson(json: $json);
    }

    public static function dataProviderDecodeJson_ArrayUtilsException(): array
    {
        return [
            'Empty' => [
                'json'                     => '',
                'expectedExceptionMessage' => 'Failed to decode JSON string, json_last_error=4, json_last_error_msg="Syntax error", json="".',
            ],
            'Missing end tag #1' => [
                'json'                     => '{',
                'expectedExceptionMessage' => 'Failed to decode JSON string, json_last_error=4, json_last_error_msg="Syntax error", json="{".',
            ],
            'Missing end tag #2' => [
                'json'                     => '{"name":"John Doe","age":18,"isAdult":true,"isChild":false,"children":null',
                'expectedExceptionMessage' => 'Failed to decode JSON string, json_last_error=4, json_last_error_msg="Syntax error", json="{"name":"John Doe","age":18,"isAdult":true,"isChild":false,"children":null".',
            ],
            '[] with names' => [
                'json'                     => '{"resource":"cURL","options":["url":"maweb.dk","timeout":10,"useSslVerification":false]}',
                'expectedExceptionMessage' => 'Failed to decode JSON string, json_last_error=4, json_last_error_msg="Syntax error", json="{"resource":"cURL","options":["url":"maweb.dk","timeout":10,"useSslVerification":false]}".',
            ],
            '\xB1\x31 (malformed UTF-8 characters)' => [
                'json'                     => "\xB1\x31",
                'expectedExceptionMessage' => 'Failed to decode JSON string, json_last_error=5, json_last_error_msg="Malformed UTF-8 characters, possibly incorrectly encoded", json="' . "\xB1\x31" . '".',
            ]
        ];
    }

    /**
     * @throws ArrayUtilsException
     */
    #[DataProvider('dataProviderGetArrayFromKey')]
    public function testGetArrayFromKey(array $array, string $key, array $expectedArray)
    {
        $this->assertSame(
            expected: $expectedArray,
            actual: ArrayUtils::getArrayFromKey(array: $array, key: $key)
        );
    }

    public static function dataProviderGetArrayFromKey(): array
    {
        return [
            'associative' => [
                'array'         => ['name' => 'John Doe', 'children' => ['Anna', 'Brian']],
                'key'           => 'children',
                'expectedArray' => ['Anna', 'Brian']
            ],
            'list' => [
                'array'         => [0 => 'John Doe', 1 => ['Anna', 'Brian']],
                'key'           => '1',
                'expectedArray' => ['Anna', 'Brian']
            ],
        ];
    }

    public function testGetArrayFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","children":["Anna","Brian"]}.');

        ArrayUtils::getArrayFromKey(array: ['name' => 'John Doe', 'children' => ['Anna', 'Brian']], key: 'unknownKey');
    }

    public function testGetArrayFromKey_ArrayUtilsException_ValueNotArray()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "name" has datatype string, array was expected, array={"name":"John Doe","children":["Anna","Brian"]}.');

        ArrayUtils::getArrayFromKey(array: ['name' => 'John Doe', 'children' => ['Anna', 'Brian']], key: 'name');
    }

    /**
     * @throws ArrayUtilsException
     */
    #[DataProvider('dataProviderGetArrayOrNullFromKey')]
    public function testGetArrayOrNullFromKey(array $array, string $key, ?array $expectedArray)
    {
        $this->assertSame(
            expected: $expectedArray,
            actual: ArrayUtils::getArrayOrNullFromKey(array: $array, key: $key)
        );
    }

    public static function dataProviderGetArrayOrNullFromKey(): array
    {
        return [
            'associative' => [
                'array'         => ['name' => 'John Doe', 'children' => ['Anna', 'Brian']],
                'key'           => 'children',
                'expectedArray' => ['Anna', 'Brian']
            ],
            'list' => [
                'array'         => [0 => 'John Doe', 1 => ['Anna', 'Brian']],
                'key'           => '1',
                'expectedArray' => ['Anna', 'Brian']
            ],
            'null' => [
                'array'         => ['name' => 'John Doe', 'children' => null],
                'key'           => 'children',
                'expectedArray' => null
            ],
        ];
    }

    public function testGetArrayOrNullFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","children":["Anna","Brian"]}.');

        ArrayUtils::getArrayOrNullFromKey(array: ['name' => 'John Doe', 'children' => ['Anna', 'Brian']], key: 'unknownKey');
    }

    public function testGetArrayOrNullFromKey_ArrayUtilsException_ValueNotArrayOrNull()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "name" has datatype string, array or null was expected, array={"name":"John Doe","children":["Anna","Brian"]}.');

        ArrayUtils::getArrayOrNullFromKey(array: ['name' => 'John Doe', 'children' => ['Anna', 'Brian']], key: 'name');
    }

    /**
     * @throws ArrayUtilsException
     */
    #[DataProvider('dataProviderGetIntegerFromKey')]
    public function testGetIntegerFromKey(array $array, string $key, int $expectedValue)
    {
        $this->assertSame(
            expected: $expectedValue,
            actual: ArrayUtils::getIntegerFromKey(array: $array, key: $key)
        );
    }

    public static function dataProviderGetIntegerFromKey(): array
    {
        $array = [
            'PHP_INT_MIN' => PHP_INT_MIN,
            '-1'          => -1,
            '0'           => 0,
            '1'           => 1,
            'PHP_INT_MAX' => PHP_INT_MAX,
        ];

        return [
            'PHP_INT_MIN' => ['array' => $array, 'key' => 'PHP_INT_MIN', 'expectedValue' => PHP_INT_MIN],
            '-1'          => ['array' => $array, 'key' => '-1',          'expectedValue' => -1],
            '0'           => ['array' => $array, 'key' => '0',           'expectedValue' => 0],
            '1'           => ['array' => $array, 'key' => '1',           'expectedValue' => 1],
            'PHP_INT_MAX' => ['array' => $array, 'key' => 'PHP_INT_MAX', 'expectedValue' => PHP_INT_MAX],
        ];
    }

    public function testGetIntegerFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","age":18}.');

        ArrayUtils::getIntegerFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'unknownKey');
    }

    public function testGetIntegerFromKey_ArrayUtilsException_ValueNotInteger()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "name" has datatype string, integer was expected, array={"name":"John Doe","age":18}.');

        ArrayUtils::getIntegerFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'name');
    }

    /**
     * @throws ArrayUtilsException
     */
    #[DataProvider('dataProviderGetIntegerOrNullFromKey')]
    public function testGetIntegerOrNullFromKey(array $array, string $key, ?int $expectedValue)
    {
        $this->assertSame(
            expected: $expectedValue,
            actual: ArrayUtils::getIntegerOrNullFromKey(array: $array, key: $key)
        );
    }

    public static function dataProviderGetIntegerOrNullFromKey(): array
    {
        $array = [
            'PHP_INT_MIN' => PHP_INT_MIN,
            '-1'          => -1,
            '0'           => 0,
            '1'           => 1,
            'PHP_INT_MAX' => PHP_INT_MAX,
            'null'        => null
        ];

        return [
            'PHP_INT_MIN' => ['array' => $array, 'key' => 'PHP_INT_MIN', 'expectedValue' => PHP_INT_MIN],
            '-1'          => ['array' => $array, 'key' => '-1',          'expectedValue' => -1],
            '0'           => ['array' => $array, 'key' => '0',           'expectedValue' => 0],
            '1'           => ['array' => $array, 'key' => '1',           'expectedValue' => 1],
            'PHP_INT_MAX' => ['array' => $array, 'key' => 'PHP_INT_MAX', 'expectedValue' => PHP_INT_MAX],
            'null'        => ['array' => $array, 'key' => 'null',        'expectedValue' => null],
        ];
    }

    public function testGetIntegerOrNullFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","age":18}.');

        ArrayUtils::getIntegerOrNullFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'unknownKey');
    }

    public function testGetIntegerOrNullFromKey_ArrayUtilsException_ValueNotIntegerOrNull()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "name" has datatype string, integer or null was expected, array={"name":"John Doe","age":18}.');

        ArrayUtils::getIntegerOrNullFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'name');
    }

    /**
     * @throws ArrayUtilsException
     */
    #[DataProvider('dataProviderGetFloatFromKey')]
    public function testGetFloatFromKey(array $array, string $key, float $expectedValue)
    {
        $this->assertSame(
            expected: $expectedValue,
            actual: ArrayUtils::getFloatFromKey(array: $array, key: $key)
        );
    }

    public static function dataProviderGetFloatFromKey(): array
    {
        $array = [
            'PHP_INT_MIN' => PHP_INT_MIN,
            '-1.99'       => -1.99,
            '-1.00'       => -1.00,
            '-1'          => -1,
            '0'           => 0,
            '0.00'        => 0.00,
            '1'           => 1,
            '1.00'        => 1.00,
            '1.99'        => 1.99,
            'PHP_INT_MAX' => PHP_INT_MAX,
        ];

        return [
            'PHP_INT_MIN' => ['array' => $array, 'key' => 'PHP_INT_MIN', 'expectedValue' => PHP_INT_MIN],
            '-1.99'       => ['array' => $array, 'key' => '-1.99',       'expectedValue' => -1.99],
            '-1.00'       => ['array' => $array, 'key' => '-1.00',       'expectedValue' => -1.00],
            '-1'          => ['array' => $array, 'key' => '-1',          'expectedValue' => -1],
            '0'           => ['array' => $array, 'key' => '0',           'expectedValue' => 0],
            '0.00'        => ['array' => $array, 'key' => '0.00',        'expectedValue' => 0.00],
            '1'           => ['array' => $array, 'key' => '1',           'expectedValue' => 1],
            '1.00'        => ['array' => $array, 'key' => '1.00',        'expectedValue' => 1.00],
            '1.99'        => ['array' => $array, 'key' => '1.99',        'expectedValue' => 1.99],
            'PHP_INT_MAX' => ['array' => $array, 'key' => 'PHP_INT_MAX', 'expectedValue' => PHP_INT_MAX],
        ];
    }

    public function testGetFloatFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","age":18}.');

        ArrayUtils::getFloatFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'unknownKey');
    }

    public function testGetFloatFromKey_ArrayUtilsException_ValueNotFloat()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "name" has datatype string, integer or float was expected, array={"name":"John Doe","age":18}.');

        ArrayUtils::getFloatFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'name');
    }

    /**
     * @throws ArrayUtilsException
     */
    public function testGetStringFromKey()
    {
        $this->assertSame(
            expected: 'John Doe',
            actual: ArrayUtils::getStringFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'name')
        );
    }

    public function testGetStringFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","age":18}.');

        ArrayUtils::getStringFromKey(array: ['name' => 'John Doe', 'age' => 18], key:'unknownKey');
    }

    public function testGetStringFromKey_ArrayUtilsException_ValueNotString()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "age" has datatype integer, string was expected, array={"name":"John Doe","age":18}.');

        ArrayUtils::getStringFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'age');
    }

    /**
     * @throws ArrayUtilsException
     */
    public function testGetStringOrNullFromKey_String()
    {
        $this->assertSame(
            expected: 'John Doe',
            actual: ArrayUtils::getStringOrNullFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'name')
        );
    }

    /**
     * @throws ArrayUtilsException
     */
    public function testGetStringOrNullFromKey_Null()
    {
        $this->assertSame(
            expected: null,
            actual: ArrayUtils::getStringOrNullFromKey(array: ['name' => null, 'age' => 18], key: 'name')
        );
    }

    public function testGetStringOrNullFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","age":18}.');

        ArrayUtils::getStringOrNullFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'unknownKey');
    }

    public function testGetStringOrNullFromKey_ArrayUtilsException_ValueNotStringOrNull()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "age" has datatype integer, string or null was expected, array={"name":"John Doe","age":18}.');

        ArrayUtils::getStringOrNullFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'age');
    }

    /**
     * @throws ArrayUtilsException
     */
    public function testGetStringAsIntegerFromKey()
    {
        $this->assertSame(
            expected: 18,
            actual: ArrayUtils::getStringAsIntegerFromKey(array: ['name' => 'John Doe', 'age' => '18'], key: 'age')
        );
    }

    public function testGetStringAsIntegerFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","age":18}.');

        ArrayUtils::getStringAsIntegerFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'unknownKey');
    }

    public function testGetStringAsIntegerFromKey_ArrayUtilsException_ValueNotString()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "isParent" has datatype boolean, string was expected, array={"name":"John Doe","isParent":true}.');

        ArrayUtils::getStringAsIntegerFromKey(array: ['name' => 'John Doe', 'isParent' => true], key: 'isParent');
    }

    public function testGetStringAsIntegerFromKey_ArrayUtilsException_ValueNotStringWithIntegerValue()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Value of array element with key "name" has datatype string, string with an integer value was expected, array={"name":"John Doe","age":18}.');

        ArrayUtils::getStringAsIntegerFromKey(array: ['name' => 'John Doe', 'age' => 18], key: 'name');
    }

    /**
     * @throws ArrayUtilsException
     */
    #[DataProvider('dataProviderGetBooleanFromKey')]
    public function testGetBooleanFromKey(array $array, string $key, bool $expectedValue)
    {
        $this->assertSame(
            expected: $expectedValue,
            actual: ArrayUtils::getBooleanFromKey(array: $array, key: $key)
        );
    }

    public static function dataProviderGetBooleanFromKey(): array
    {
        return [
            'true' => [
                'array'         => ['isTrue' => true, 'isFalse' => false],
                'key'           => 'isTrue',
                'expectedValue' => true
            ],
            'false' => [
                'array'         => ['isTrue' => true, 'isFalse' => false],
                'key'           => 'isFalse',
                'expectedValue' => false
            ],
        ];
    }

    public function testGetBooleanFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","isTrue":true,"isFalse":false}.');

        ArrayUtils::getBooleanFromKey(array: ['name' => 'John Doe', 'isTrue' => true, 'isFalse' => false], key: 'unknownKey');
    }

    public function testGetBooleanFromKey_ArrayUtilsException_ValueItNotBoolean()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage( message: 'Value of array element with key "name" has datatype string, boolean was expected, array={"name":"John Doe","isTrue":true,"isFalse":false}.');

        ArrayUtils::getBooleanFromKey(array: ['name' => 'John Doe', 'isTrue' => true, 'isFalse' => false], key: 'name');
    }

    /**
     * @throws ArrayUtilsException
     */
    #[DataProvider('dataProviderGetMixedFromKey')]
    public function testGetMixedFromKey(array $array, string $key, mixed $expectedValue)
    {
        $this->assertSame(
            expected: $expectedValue,
            actual: ArrayUtils::getMixedFromKey(array: $array, key: $key)
        );
    }

    public static function dataProviderGetMixedFromKey(): array
    {
        return [
            'array' => [
                'array'         => ['name' => 'John Doe', 'children' => ['Anna', 'Brian']],
                'key'           => 'children',
                'expectedValue' => ['Anna', 'Brian']
            ],
            'PHP_INT_MIN' => [
                'array'         => ['PHP_INT_MIN' => PHP_INT_MIN, '-1' => -1, '0' => 0, '1' => 1, 'PHP_INT_MAX' => PHP_INT_MAX],
                'key'           => 'PHP_INT_MIN',
                'expectedValue' => PHP_INT_MIN
            ],
            '-1' => [
                'array'         => ['PHP_INT_MIN' => PHP_INT_MIN, '-1' => -1, '0' => 0, '1' => 1, 'PHP_INT_MAX' => PHP_INT_MAX],
                'key'           => '-1',
                'expectedValue' => -1
            ],
            '0' => [
                'array'         => ['PHP_INT_MIN' => PHP_INT_MIN, '-1' => -1, '0' => 0, '1' => 1, 'PHP_INT_MAX' => PHP_INT_MAX],
                'key'           => '0',
                'expectedValue' => 0
            ],
            '1' => [
                'array'         => ['PHP_INT_MIN' => PHP_INT_MIN, '-1' => -1, '0' => 0, '1' => 1, 'PHP_INT_MAX' => PHP_INT_MAX],
                'key'           => '1',
                'expectedValue' => 1
            ],
            'PHP_INT_MAX' => [
                'array'         => ['PHP_INT_MIN' => PHP_INT_MIN, '-1' => -1, '0' => 0, '1' => 1, 'PHP_INT_MAX' => PHP_INT_MAX],
                'key'           => 'PHP_INT_MAX',
                'expectedValue' => PHP_INT_MAX
            ],
            'string' => [
                'array'         => ['name' => 'John Doe', 'age' => 18],
                'key'           => 'name',
                'expectedValue' => 'John Doe'
            ],
            'true' => [
                'array'         => ['isTrue' => true, 'isFalse' => false],
                'key'           => 'isTrue',
                'expectedValue' => true
            ],
            'false' => [
                'array'         => ['isTrue' => true, 'isFalse' => false],
                'key'           => 'isFalse',
                'expectedValue' => false
            ],
        ];
    }

    public function testGetMixedFromKey_ArrayUtilsException_KeyNotFound()
    {
        $this->expectException(exception: ArrayUtilsException::class);
        $this->expectExceptionMessage(message: 'Array does not have an element with key "unknownKey", array={"name":"John Doe","isTrue":true,"isFalse":false}.');

        ArrayUtils::getMixedFromKey(array: ['name' => 'John Doe', 'isTrue' => true, 'isFalse' => false], key: 'unknownKey');
    }
}
