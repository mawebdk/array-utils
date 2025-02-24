<?php
namespace MawebDK\ArrayUtils;

/**
 * Array utilities.
 */
class ArrayUtils
{
    /**
     * Decode a JSON string and return an array with the decoded JSON string.
     * @param string $json           JSON string to be decoded.
     * @return array                 Array with the decoded JSON string.
     * @throws ArrayUtilsException   Failed to decode JSON string.
     */
    public static function decodeJson(string $json): array
    {
        $array = json_decode(json: $json, associative: true);

        if (!is_array(value: $array)):
            throw new ArrayUtilsException(message: sprintf(
                'Failed to decode JSON string, json_last_error=%d, json_last_error_msg="%s", json="%s".',
                json_last_error(), json_last_error_msg(), $json
            ));
        endif;

        return $array;
    }

    /**
     * Returns an array value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with array value.
     * @return array                 Array value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not an array.
     */
    public static function getArrayFromKey(array $array, string $key): array
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_array(value: $value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, array was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $value;
    }

    /**
     * Returns an array or null value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with array value.
     * @return array|null            Array or null value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not an array.
     */
    public static function getArrayOrNullFromKey(array $array, string $key): ?array
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_array(value: $value) && !is_null(value: $value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, array or null was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $value;
    }

    /**
     * Returns an integer value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with integer value.
     * @return int                   Integer value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not an integer.
     */
    public static function getIntegerFromKey(array $array, string $key): int
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_int(value: $value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, integer was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $value;
    }

    /**
     * Returns an integer or null value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with integer or null value.
     * @return int|null              Integer or null value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not an integer or null.
     */
    public static function getIntegerOrNullFromKey(array $array, string $key): ?int
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_int(value: $value) && !is_null(value: $value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, integer or null was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $value;
    }

    /**
     * Returns a float value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with float value.
     * @return float                 Float value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not a float.
     */
    public static function getFloatFromKey(array $array, string $key): float
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_int($value) && !is_float(value: $value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, integer or float was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $value;
    }

    /**
     * Returns a string value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with string value.
     * @return string                String value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not a string.
     */
    public static function getStringFromKey(array $array, string $key): string
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_string(value: $value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, string was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $value;
    }

    /**
     * Returns a string or null value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with string or null value.
     * @return string|null           String or null value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not a string or null.
     */
    public static function getStringOrNullFromKey(array $array, string $key): ?string
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_string(value: $value) && !is_null(value: $value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, string or null was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $value;
    }

    /**
     * Returns a string value as an integer of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with string value.
     * @return int                   String value as an integer of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not a string with an integer value.
     */
    public static function getStringAsIntegerFromKey(array $array, string $key): int
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_string(value: $value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, string was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        $intValue = (int)$value;

        if ((string)$intValue !== $value):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, string with an integer value was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $intValue;
    }

    /**
     * Returns a boolean value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element with boolean value.
     * @return bool                  Boolean value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found or value is not a boolean.
     */
    public static function getBooleanFromKey(array $array, string $key): bool
    {
        $value = self::getMixedFromKey(array: $array, key: $key);

        if (!is_bool($value)):
            throw new ArrayUtilsException(message: sprintf(
                'Value of array element with key "%s" has datatype %s, boolean was expected, array=%s.',
                $key, gettype(value: $value), json_encode(value: $array)
            ));
        endif;

        return $value;
    }

    /**
     * Returns a value of an array element with the given key.
     * @param array $array           Array to get the element value from.
     * @param string $key            Key to the array element.
     * @return mixed                 Value of the array element with the given key.
     * @throws ArrayUtilsException   Key not found.
     */
    public static function getMixedFromKey(array $array, string $key): mixed
    {
        if (!array_key_exists(key: $key, array: $array)):
            throw new ArrayUtilsException(message: sprintf(
                'Array does not have an element with key "%s", array=%s.',
                $key, json_encode(value: $array)
            ));
        endif;

        return $array[$key];
    }

    /**
     * Private constructor to avoid direct instantiation.
     */
    private function __construct()
    {
        // This body is empty on purpose.
    }
}