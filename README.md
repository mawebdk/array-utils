# array-utils
Use ArrayUtils to create an array from a JSON string and extract typed data from an array.

An ArrayUtilsException will be thrown if the array does not contain the given key or the value has an incorrect datatype.

## Usage
Create an array from a JSON string.
```
try {
    $array = ArrayUtils::decodeJson(json: $json);
} catch (ArrayUtils $e) {
    // Error handling.
}
```

Extract typed data from an array.
```
try {
    $arrayValue         = ArrayUtils::getArrayFromKey(array $array, string $key);
    $arrayOrNullValue   = ArrayUtils::getArrayOrNullFromKey(array $array, string $key);
    $integerValue       = ArrayUtils::getIntegerFromKey(array $array, string $key);
    $integerOrNullValue = ArrayUtils::getIntegerOrNullFromKey(array $array, string $key);
    $floatValue         = ArrayUtils::getFloatFromKey(array $array, string $key);
    $stringValue        = ArrayUtils::getStringFromKey(array $array, string $key);
    $stringOrNullValue  = ArrayUtils::getStringOrNullFromKey(array $array, string $key);
    $booleanValue       = ArrayUtils::getBooleanFromKey(array $array, string $key);
} catch (ArrayUtils $e) {
    // Error handling.
}
```

Use getStringAsIntegerFromKey() if the value is a string with an integer value, e.q. `'123'`.
```
try {
    $integerValue = ArrayUtils::getStringAsIntegerFromKey(array $array, string $key);
} catch (ArrayUtils $e) {
    // Error handling.
}
```

Use getMixedFromKey() if you just want to extract data from an array without check of the datatype.
```
try {
    $integerValue = ArrayUtils::getMixedFromKey(array $array, string $key);
} catch (ArrayUtils $e) {
    // Error handling.
}
```
