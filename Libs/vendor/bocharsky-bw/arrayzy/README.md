# Arrayzy

The wrapper for all PHP built-in array functions and easy, object-oriented array
manipulation library. In short: Arrays on steroids.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e0235f5d-a89b-4add-b3c6-45813d2bf9eb/mini.png)](https://insight.sensiolabs.com/projects/e0235f5d-a89b-4add-b3c6-45813d2bf9eb)
[![Build Status](https://travis-ci.org/bocharsky-bw/Arrayzy.svg?branch=master)](https://travis-ci.org/bocharsky-bw/Arrayzy)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bocharsky-bw/Arrayzy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bocharsky-bw/Arrayzy/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/bocharsky-bw/Arrayzy/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bocharsky-bw/Arrayzy/?branch=master)

## ArrayImitator

This is the *main* class of this library. Each method, which associated with
the corresponding native PHP function, keep its behavior. In other words:
methods could creates a new array (leaving the original array unchanged),
operates on the same array (returns the array itself and **DOES NOT** create
a new instance) or return some result.

> **NOTE:** If method creates a new array but you don't need the first array
  you operate on, you can override it manually:

``` php
use Arrayzy\ArrayImitator as A;

$a = A::create(['a', 'b', 'c']);
$a = $a->reverse(); // override instance you operates on, because $a !== $a->reverse()
```

> **NOTE:** If method operates on the same array but you need to keep the first
  array you operate on as unchanged, you can clone it manually first:

``` php
use Arrayzy\ArrayImitator as A;

$a = A::create(['a', 'b', 'c']);
$b = clone $a;
$b->shuffle(); // keeps $a unchanged, because $a !== $b
```

# Contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Creation](#creation)
* [Usage](#usage)
    * [Chaining](#chaining)
* [Public method list](#public-method-list)
    * [add](#add)
    * [chunk](#chunk)
    * [clear](#clear)
    * [combineTo](#combineto)
    * [combineWith](#combinewith)
    * [contains](#contains)
    * [containsKey](#containskey)
    * [count](#count)
    * [create](#create)
    * [createClone](#createclone)
    * [createFromJson](#createfromjson)
    * [createFromObject](#createfromobject)
    * [createFromString](#createfromstring)
    * [createWithRange](#createwithrange)
    * [current](#current)
    * [customSort](#customsort)
    * [customSortKeys](#customsortkeys)
    * [debug](#debug)
    * [diffWith](#diffwith)
    * [each](#each)
    * [exists](#exists)
    * [export](#export)
    * [filter](#filter)
    * [find](#find)
    * [first](#first)
    * [flip](#flip)
    * [getIterator](#getiterator)
    * [getKeys](#getkeys)
    * [getRandom](#getrandom)
    * [getRandomKey](#getrandomkey)
    * [getRandomKeys](#getrandomkeys)
    * [getRandomValues](#getrandomvalues)
    * [getValues](#getvalues)
    * [indexOf](#indexof)
    * [isAssoc](#isassoc)
    * [isEmpty](#isempty)
    * [isNumeric](#isnumeric)
    * [key](#key)
    * [last](#last)
    * [map](#map)
    * [mergeTo](#mergeto)
    * [mergeWith](#mergewith)
    * [next](#next)
    * [offsetExists](#offsetexists)
    * [offsetGet](#offsetget)
    * [offsetSet](#offsetset)
    * [offsetUnset](#offsetunset)
    * [pad](#pad)
    * [pop](#pop)
    * [previous](#previous)
    * [push](#push)
    * [reduce](#reduce)
    * [reindex](#reindex)
    * [replaceIn](#replacein)
    * [replaceWith](#replacewith)
    * [reverse](#reverse)
    * [shift](#shift)
    * [shuffle](#shuffle)
    * [slice](#slice)
    * [sort](#sort)
    * [sortKeys](#sortkeys)
    * [toArray](#toarray)
    * [toJson](#tojson)
    * [toReadableString](#toreadablestring)
    * [toString](#tostring)
    * [unique](#unique)
    * [unshift](#unshift)
    * [walk](#walk)
* [Contribution](#contribution)
* [Links](#links)

## Requirements

* PHP `5.4` or higher
* PHP `JSON` extension

## Installation

The preferred way to install this package is to use [Composer][1]:

``` bash
$ composer require bocharsky-bw/arrayzy
```

If you don't use `Composer` - register this package in your autoloader manually
or download this library and `require` the necessary files directly in your scripts:

``` php
require_once __DIR__ . '/path/to/library/src/ArrayImitator.php';
```

## Creation

Create a new empty array with the `new` statement.

``` php
use Arrayzy\ArrayImitator;

$a = new ArrayImitator; // Creates a new instance with the "use" statement
// or
$a = new \Arrayzy\ArrayImitator; // Creates a new array by fully qualified namespace
```

> **NOTE:** Don't forget about namespaces. You can use [namespace aliases][2]
  for simplicity if you want:

```php
use Arrayzy\ArrayImitator as A;

$a = new A; // Creates a new instance using namespace alias
```

Create a new array with default values, passed it to the constructor as an array:

``` php
$a = new A([1, 2, 3]);
// or
$a = new A([1 => 'a', 2 => 'b', 3 => 'c']);
```

Also, new objects can be created with one of the public static methods
prefixed with 'create':

* [create](#create)
* [createFromJson](#createfromjson)
* [createFromObject](#createfromobject)
* [createFromString](#createfromstring)
* [createWithRange](#createwithrange)

## Usage

You can get access to the values like with the familiar PHP array syntax:

``` php
use Arrayzy\ArrayImitator as A;

$a = A::create(['a', 'b', 'c']);

$a[] = 'e';    // or use $a->offsetSet(null, 'e') method
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'e']

$a[3] = 'd';   // or use $a->offsetSet(3, 'd') method
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd']

print $a[1]; // 'b'
// or use the corresponding method
print $a->offsetGet(1); // 'b'
```

*NOTE: The following methods and principles apply to the `ArrayImitator` class.
In the examples provided below the `ArrayImitator` aliased with `A`.*

### Chaining

Methods may be chained for ease of use:

``` php
$a = A::create(['a', 'b', 'c']);

$a
    ->offsetSet(null, 'e')
    ->offsetSet(3, 'd')
    ->offsetSet(null, 'e')
    ->shuffle() // or any other method that returns $this
;

$a->toArray(); // [0 => 'c', 1 => 'a', 2 => 'e', 3 => 'd', 4 => 'b']
```

### Converting

Easily convert instance array elements to a simple PHP `array`, `string`,
readable `string` or JSON format:

* [toArray](#toarray)
* [toJson](#tojson)
* [toReadableString](#toreadablestring)
* [toString](#tostring)

### Debugging

* [debug](#debug)
* [export](#export)

## Public method list

### add

``` php
$a = A::create(['a', 'b', 'c']);
$a->add('d');
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd']
```

### chunk

``` php
$a = A::create(['a', 'b', 'c']);
$a->chunk(2);
$a->toArray(); // [0 => [0 => 'a', 1 => 'b'], 1 => [0 => 'c']]
```

### clear

``` php
$a = A::create(['a', 'b', 'c']);
$a->clear();
$a->toArray(); // []
```

### combineTo

``` php
$a = A::create(['a', 'b', 'c']);
$a->combineTo([1, 2, 3]);
$a->toArray(); // [1 => 'a', 2 => 'b', 3 => 'c']
```

### combineWith

``` php
$a = A::create([1, 2, 3]);
$a->combineWith(['a', 'b', 'c']);
$a->toArray(); // [1 => 'a', 2 => 'b', 3 => 'c']
```

### contains

``` php
$a = A::create(['a', 'b', 'c']);
$a->contains('c'); // true
```

### containsKey

``` php
$a = A::create(['a', 'b', 'c']);
$a->containsKey(2); // true
```

### count

``` php
$a = A::create(['a', 'b', 'c']);
$a->count(); // 3
```

### create

``` php
$a = A::create(['a', 'b', 'c']);
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### createClone

Creates a shallow copy of the array.

Keep in mind, that in PHP variables contain only references to the object, **NOT** the object itself:

``` php
$a = A::create(['a', 'b', 'c']);
$b = $a; // $a and $b are different variables referencing the same object ($a === $b)
```

So if you **DO NOT** want to modify the current array, you need to clone it manually first:

``` php
$a = A::create(['a', 'b', 'c']);
$b = clone $a; // $a and $b are different instances ($a !== $b)
// or do it with built-in method
$b = $a->createClone(); // $a !== $b
```

### createFromJson

Creates an array by parsing a JSON string:

``` php
$a = A::createFromJson('{"a": 1, "b": 2, "c": 3}');
$a->toArray(); // ['a' => 1, 'b' => 2, 'c' => 3]
```

### createFromObject

Creates an instance array from any `object` that implemented `\ArrayAccess` interface:

``` php
$a = A::create(['a', 'b', 'c']);
$b = A::createFromObject($a); // where $a could be any object that implemented \ArrayAccess interface
$b->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### createFromString

Creates an array from a simple PHP `string` with specified separator:

``` php
$a = A::createFromString('a;b;c', ';');
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### createWithRange

Creates an array of a specified range:

``` php
$a = A::createWithRange(2, 6, 2);
$a->toArray(); // [0 => 2, 1 => 4, 2 => 6]
```

### current

Position of the iterator.

``` php
$a = A::create(['a', 'b', 'c']);
$a->current(); // 'a'
```

### customSort

``` php
$a = A::create(['b', 'a', 'c']);
$a->customSort(function($a, $b) {
    if ($a === $b) {
        return 0;
    }

    return ($a < $b) ? -1 : 1;
});
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### customSortKeys

``` php
$a = A::create([1 => 'b', 0 => 'a', 2 => 'c']);
$a->customSortKeys(function($a, $b) {
    if ($a === $b) {
        return 0;
    }

    return ($a < $b) ? -1 : 1;
});
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### debug

``` php
$a = A::create(['a', 'b', 'c']);
$a->debug(); // Array ( [0] => a [1] => b [2] => c )
```

### diffWith

``` php
$a = A::create(['a', 'b', 'c']);
$a->diffWith(['c', 'd']);
$a->toArray(); // [0 => 'a', 1 => 'b']
```

### each

``` php
$a = A::create(['a', 'b', 'c']);
$a->each(); // [0 => 0, 'key' => 0, 1 => 'a', 'value' => 'a']
```

### exists

A custom contains method where you can supply your own custom logic in any callable function. 

``` php
$a = A::create(['a', 'b', 'c']);

$a->exists(function($key, $value) {
   return 1 === $key and 'b' === $value;
}); // true
```

### export

``` php
$a = A::create(['a', 'b', 'c']);
$a->export(); // array ( 0 => 'a', 1 => 'b', 2 => 'c', )
```

### filter

``` php
$a = A::create(['a', 'z', 'b', 'z']);
$a->filter(function($value) {
    return 'z' !== $value; // exclude 'z' value from array
});
$a->toArray(); // [0 => 'a', 2 => 'b']
```

### find

``` php
$a = A::create(['a', 'b', 'c']);
$a->find(function($value, $key) {
    return 'b' == $value && 0 < $key;
}); // 'b'
```

### first

``` php
$a = A::create(['a', 'b', 'c']);
$a->first(); // 'a'
```

### flip

``` php
$a = A::create(['a', 'b', 'c']);
$a->flip();
$a->toArray(); // ['a' => 0, 'b' => 1, 'c' => 2]
```

### getIterator

Creates an external Iterator. Check the [iteratorAggregate][6] documentation for more information.

### getKeys

``` php
$a = A::create(['a' => 1, 'b' => 2, 'c' => 3]);
$a->getKeys(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### getRandom

``` php
$a = A::create(['a', 'b', 'c', 'd']);
$a->getRandom(); // 'c'
```

### getRandomKey

``` php
$a = A::create(['a', 'b', 'c', 'd']);
$a->getRandomKey(); // 2
```

### getRandomKeys

``` php
$a = A::create(['a', 'b', 'c', 'd']);
$a->getRandomKeys(2); // [0, 2]
```

### getRandomValues

``` php
$a = A::create(['a', 'b', 'c', 'd']);
$a->getRandomValues(2); // ['b', 'd']
```

### getValues

``` php
$a = A::create([1 => 'a', 2 => 'b', 3 => 'c']);
$a->getValues(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### indexOf

``` php
$a = A::create(['a', 'b', 'c']);
$a->indexOf('b'); // 1
```

### isAssoc

``` php
$a = A::create(['key' => 'value']);
$a->isAssoc(); // true
```

### isEmpty

``` php
$a = A::create([]);
$a->isEmpty(); // true
```

### isNumeric

``` php
$a = A::create(['a', 'b', 'c']);
$a->isNumeric(); // true
```

### key

``` php
$a = A::create(['a', 'b', 'c']);
$a->current(); // 'a'
$a->key();     // 0
$a->next();    // 'b'
$a->key();     // 1
```

### last

``` php
$a = A::create(['a', 'b', 'c']);
$a->last(); // 'c'
```

### map

``` php
$a = A::create(['a', 'b', 'c']);
$a->map(function($value) {
    return $value . $value;
});
$a->toArray(); // [0 => 'aa', 1 => 'bb', 2 => 'cc']
```

### mergeTo

``` php
// indexed array behavior
$a = A::create(['a', 'b', 'c']); // create indexed array
$a->mergeTo(['c', 'd']); // [0 => 'c', 1 => 'd', 2 => 'a', 3 => 'b', 4 => 'c']

// assoc array behavior
$b = A::create(['a' => 1, 'b' => 2, 'c' => 99]); // create assoc array
$b->mergeTo(['c' => 3, 'd' => 4]); // ['c' => 99, 'd' => 4, 'a' => 1, 'b' => 2]
```

### mergeWith

``` php
// indexed array behavior
$a = A::create(['a', 'b', 'c']); // create indexed array
$a->mergeWith(['c', 'd']); // [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'c', 4 => 'd']

// assoc array behavior
$b = A::create(['a' => 1, 'b' => 2, 'c' => 99]); // create assoc array
$b->mergeWith(['c' => 3, 'd' => 4]); // ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]
```

### next

``` php
$a = A::create(['a', 'b', 'c']);
$a->next(); // 'b'
$a->next(); // 'c'
```

### offsetExists

``` php
$a = A::create(['a', 'b', 'c']);
$a->offsetExists(2); // true (or use isset($a[2]))
$a->offsetExists(3); // false (or use isset($a[3]))
```

### offsetGet

``` php
$a = A::create(['a', 'b', 'c']);
$a->offsetGet(1); // 'b' (or use $a[1])
```

### offsetSet

``` php
$a = A::create(['a', 'b', 'd']);
// add a new value
$a->offsetSet(null, 'd'); // or use $a[] = 'd';
$a->toArray();            // [0 => 'a', 1 => 'b', 2 => 'd', 3=> 'd']
// replace an existing value by key
$a->offsetSet(2, 'c');    // or use $a[2] = 'c';
$a->toArray();            // [0 => 'a', 1 => 'b', 2 => 'c', 3=> 'd']
```

### offsetUnset

``` php
$a = A::create(['a', 'b', 'c']);
$a->offsetUnset(1); // or use unset($a[1]);
$a->toArray();      // [0 => 'a', 2 => 'c']
```

### pad

``` php
$a = A::create(['a', 'b', 'c']);
$a->pad(5, 'z');
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'z', 4 => 'z']
```

### pop

``` php
$a = A::create(['a', 'b', 'c']);
$a->pop();     // 'c'
$a->toArray(); // [0 => 'a', 1 => 'b']
```

### previous

``` php
$a = A::create(['a', 'b', 'c']);
$a->next();     // 'b'
$a->next();     // 'c'
$a->previous(); // 'b'
```

### push

``` php
$a = A::create(['a', 'b']);
$a->push('c', 'd');
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd']
```

> The `push()` method allows multiple arguments.

### reduce

``` php
$a = A::create(['a', 'b', 'c']);
$a->reduce(function($result, $item) {
    return $result . $item;
}); // 'abc'
```

### reindex

``` php
$a = A::create([2 => 'a', 1 => 'b', 3 => 'c']);
$a->reindex();
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### replaceIn

``` php
$a = A::create([1 => 'b', 2 => 'c']);
$a->replaceIn(['a', 'd', 'e']);
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### replaceWith

``` php
$a = A::create(['a', 'd', 'e']);
$a->replaceWith([1 => 'b', 2 => 'c']);
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### reverse

``` php
$a = A::create(['a', 'b', 'c']);
$a->reverse();
$a->toArray(); // [0 => 'c', 1 => 'b', 2 => 'a']
```

### shift

``` php
$a = A::create(['a', 'b', 'c']);
$a->shift();
$a->toArray(); // [0 => 'b', 1 => 'c']
```

### shuffle

``` php
$a = A::create(['a', 'b', 'c']);
$a->shuffle();
$a->toArray(); // [0 => 'c', 1 => 'a', 2 => 'b']
```

### slice

``` php
$a = A::create(['a', 'b', 'c', 'd']);
$a->slice(1, 2);
$a->toArray(); // [0 => 'b', 1 => 'c']
```

### sort

``` php
$a = A::create(['b', 'a', 'd', 'c']);
$a->sort(SORT_DESC);
$a->toArray(); // [0 => 'd', 1 => 'c', 2 => 'b', 3 => 'a']
```

### sortKeys

``` php
$a = A::create([3 => 'a', 1 => 'b', 2 => 'c', 0 => 'd']);
$a->sortKeys(SORT_ASC);
$a->toArray(); // [0 => 'd', 1 => 'b', 2 => 'c', 3 => 'a']
```

### toArray

Convert the array to a simple PHP `array`:

``` php
$a = A::create(['a', 'b', 'c']);
$a->toArray(); // [0 => 'a', 1 => 'b', 2 => 'c']
```

### toJson

Creates a JSON string from the array:

``` php
$a = A::create(['a' => 1, 'b' => 2, 'c' => 3]);
$a->toJson(); // { "a": 1, "b": 2, "c": 3 }
```

### toReadableString

Converts instance array to a readable PHP `string`:

``` php
$a = A::create(['a', 'b', 'c']);
$a->toReadableString(', ', ' and '); // 'a, b and c'
```

### toString

Converts instance array to a simple PHP `string`:

``` php
$a = A::create(['a', 'b', 'c']);
$a->toString(', '); // 'a, b, c'
```

### unique

``` php
$a = A::create(['a', 'b', 'b', 'c']);
$a->unique();
$a->toArray(); // [0 => 'a', 1 => 'b', 3 => 'c']
```

### unshift

``` php
$a = A::create(['a', 'b']);
$a->unshift('y', 'z');
$a->toArray(); // [0 => 'y', 1 => 'z', 2 => 'a', 3 => 'b']
```

> Method `unshift()` allow multiple arguments.

### walk

``` php
$a = A::create(['a', 'b', 'c']);
$a->walk(function(&$value, $key) {
    $key++; // the $key variable passed by value, (original value will not modified)
    $value = $value . $key; // the $value variable passed by reference (modifies original value)
});
$a->toArray(); // [0 => 'a1', 1 => 'b2', 2 => 'c3']
```

## Contribution

Feel free to submit an [Issue][3] or create a [Pull Request][4] if you find a bug
or just want to propose an improvement suggestion.

In order to propose a new feature the best way is to submit an [Issue][3] and discuss it first.

## Links

**Arrayzy** was inspired by Doctrine [ArrayCollection][7] class and [Stringy][5] library.

Look at the [Stringy][5] if you are looking for a PHP **string** manipulation library in an OOP way.

[Move UP](#arrayzy)


[1]: https://getcomposer.org/
[2]: http://php.net/manual/en/language.namespaces.importing.php
[3]: https://github.com/bocharsky-bw/Arrayzy/issues
[4]: https://github.com/bocharsky-bw/Arrayzy/pulls
[5]: https://github.com/danielstjules/Stringy
[6]: http://php.net/manual/en/class.iteratoraggregate.php
[7]: https://github.com/doctrine/collections/blob/master/lib/Doctrine/Common/Collections/ArrayCollection.php
