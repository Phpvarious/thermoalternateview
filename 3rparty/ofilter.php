<?php

/**
 * Filter an array of objects.
 *
 * You can pass in one or more properties on which to filter.
 *
 * If the key of an array is an array, then it will filtered down to that
 * level of node.
 *
 * Example usages:
 * <code>
 * ofilter($items, 'size'); // filter anything that has value in the 'size' property
 * ofilter($items, ['size' => 3, 'name']); // filter anything that has the size property === 3 and a 'name' property with value
 * ofilter($items, ['size', ['user', 'forename' => 'Bob'], ['user', 'age' => 30]) // filter w/ size, have the forename value of 'Bob' on the user object of and age of 30
 * ofilter($items, ['size' => function($prop) { return ($prop > 18 && $prop < 50); }]);
 * </code>
 *
 * @param  array $array
 * @param  string|array $properties
 * @return array
 */
function ofilter($array, $properties)
{
    if (empty($array)) {
        return;
    }
    if (is_string($properties)) {
        $properties = [$properties];
    }
    $isValid = function($obj, $propKey, $propVal) {
        if (is_int($propKey)) {
            if (!property_exists($obj, $propVal) || empty($obj->{$propVal})) {
                return false;
            }
        } else {
            if (!property_exists($obj, $propKey)) {
                return false;
            }
            if (is_callable($propVal)) {
                return call_user_func($propVal, $obj->{$propKey});
            }
            if (strtolower($obj->{$propKey}) != strtolower($propVal)) {
                return false;
            }
        }
        return true;
    };
    return array_filter($array, function($v) use ($properties, $isValid) {
        foreach ($properties as $propKey => $propVal) {
            if (is_array($propVal)) {
                $prop = array_shift($propVal);
                if (!property_exists($v, $prop)) {
                    return false;
                }
                reset($propVal);
                $key = key($propVal);
                if (!$isValid($v->{$prop}, $key, $propVal[$key])) {
                    return false;
                }
            } else {
                if (!$isValid($v, $propKey, $propVal)) {
                    return false;
                }
            }
        }
        return true;
    });
}