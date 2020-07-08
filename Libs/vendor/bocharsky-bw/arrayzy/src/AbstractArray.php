<?php

namespace Arrayzy;

use Arrayzy\Interfaces\ConvertibleInterface;
use Arrayzy\Interfaces\DebuggableInterface;
use Arrayzy\Interfaces\DoubleEndedQueueInterface;
use Arrayzy\Interfaces\SortableInterface;
use Arrayzy\Interfaces\TraversableInterface;
use Arrayzy\Traits\ConvertibleTrait;
use Arrayzy\Traits\DebuggableTrait;
use Arrayzy\Traits\DoubleEndedQueueTrait;
use Arrayzy\Traits\SortableTrait;
use Arrayzy\Traits\TraversableTrait;
use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Defines common methods and method signatures.
 *
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 */
abstract class AbstractArray implements
    ArrayAccess,
    ConvertibleInterface,
    Countable,
    DebuggableInterface,
    DoubleEndedQueueInterface,
    IteratorAggregate,
    SortableInterface,
    TraversableInterface
{
    use ConvertibleTrait;

    use DebuggableTrait;

    use DoubleEndedQueueTrait;

    use SortableTrait;

    use TraversableTrait;

    /**
     * @const string
     */
    const DEFAULT_SEPARATOR = ', ';

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * Construct new instance
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    // The abstract public method list order by ASC

    /**
     * Split array into chunks.
     *
     * @param int $size Size of each chunk
     * @param bool $preserveKeys Whether array keys are preserved or no
     *
     * @return static An array of chunks from the original array
     */
    abstract public function chunk($size, $preserveKeys = false);

    /**
     * Clear array
     *
     * @return static An empty array.
     */
    abstract public function clear();

    /**
     * Create an array using this array as values and the other array as keys.
     *
     * @param array $array Key array
     *
     * @return static An array with keys from the other.
     */
    abstract public function combineTo(array $array);

    /**
     * Create an array using this array as keys and the other array as values.
     *
     * @param array $array Values array
     *
     * @return static An array with values from the other array
     */
    abstract public function combineWith(array $array);

    /**
     * Compute the array of values not present in the other array.
     *
     * @param array $array Array for diff
     *
     * @return static An array containing all the entries from this array that are not present in $array
     */
    abstract public function diffWith(array $array);

    /**
     * Filter the array for elements satisfying the predicate $func.
     *
     * @param callable $func
     *
     * @return static An array with only element satisfying $func
     */
    abstract public function filter(callable $func);

    /**
     * Exchanges all array keys with their associated values.
     *
     * @return static An array with flipped elements
     */
    abstract public function flip();

    /**
     * Apply the given function to the every element of the array, collecting the results.
     *
     * @param callable $func
     *
     * @return static An array with modified elements
     */
    abstract public function map(callable $func);

    /**
     * Merges array with the provided one. This array is overwriting.
     *
     * @param array $array Array to merge with (is overwritten)
     * @param bool $recursively Whether array will be merged recursively or no
     *
     * @return static An array with the keys/values from $array added, that weren't present in the original
     */
    abstract public function mergeTo(array $array, $recursively = false);

    /**
     * Merges this array with the provided one. Latter array is overwriting.
     *
     * @param array $array Array to merge with (overwrites)
     * @param bool $recursively Whether array will be merged recursively or no
     *
     * @return static An array with the keys/values from $array added
     */
    abstract public function mergeWith(array $array, $recursively = false);

    /**
     * Pad array to the specified size with a given value.
     *
     * @param int $size Size of the result array
     * @param mixed $value Empty value by default
     *
     * @return static An array padded to $size with $value
     */
    abstract public function pad($size, $value);

    /**
     * Reindex the array numerically.
     *
     * @return $this An array with numerically-indexed elements
     */
    abstract public function reindex();

    /**
     * Replace the entire array with the other one except keys present in both.
     * For keys present in both arrays the value from this array will be used.
     *
     * @param array $array Array to replace with
     * @param bool $recursively Whether array will be replaced recursively or no
     *
     * @return static An array with keys from $array and values from both.
     */
    abstract public function replaceIn(array $array, $recursively = false);

    /**
     * Replace values in this array with values in the other array that have the
     * same key.
     *
     * @param array $array Array of replacing values
     * @param bool $recursively Whether array will be replaced recursively or no
     *
     * @return static An array with the same keys but new values
     */
    abstract public function replaceWith(array $array, $recursively = false);

    /**
     * Reverse the order of the array values.
     *
     * @param bool $preserveKeys Whether array keys are preserved or no
     *
     * @return $this An array with the order of the elements reversed
     */
    abstract public function reverse($preserveKeys = false);

    /**
     * Randomize element order.
     *
     * @return static An array with the elemant order shuffled
     */
    abstract public function shuffle();

    /**
     * Extract a slice of the array.
     *
     * @param int $offset Slice begin index
     * @param int|null $length Length of the slice
     * @param bool $preserveKeys Whether array keys are preserved or no
     *
     * @return static A slice of the original array with length $length
     */
    abstract public function slice($offset, $length = null, $preserveKeys = false);

    /**
     * Removes duplicate values from the array.
     *
     * @param int|null $sortFlags
     *
     * @return static An array with only unique elements
     */
    abstract public function unique($sortFlags = null);

    /**
     * Apply the given function to every element in the array, discarding the results.
     *
     * @param callable $func
     * @param bool $recursively Whether array will be walked recursively or no
     *
     * @return static The original array with potentially modified elements.
     */
    abstract public function walk(callable $func, $recursively = false);

    // The public static method list order by ASC

    /**
     * Create a new instance.
     *
     * @param array $elements
     *
     * @return static Returns created instance
     */
    public static function create(array $elements = [])
    {
        return new static($elements);
    }

    /**
     * Decode a JSON string to new instance.
     *
     * @param string $json The JSON string being decoded
     * @param int $options Bitmask of JSON decode options
     * @param int $depth Specified recursion depth
     *
     * @return static The created array
     */
    public static function createFromJson($json, $options = 0, $depth = 512)
    {
        return new static(json_decode($json, true, $depth, $options));
    }

    /**
     * Create a new instance filled with values from an object implementing ArrayAccess.
     *
     * @param ArrayAccess $elements Object that implements ArrayAccess
     *
     * @return static Returns created instance
     */
    public static function createFromObject(ArrayAccess $elements)
    {
        $array = new static();

        foreach ($elements as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * Explode a string to new instance by specified separator.
     *
     * @param string $string Converted string
     * @param string $separator Element's separator
     *
     * @return static The created array
     */
    public static function createFromString($string, $separator)
    {
        return new static(explode($separator, $string));
    }

    /**
     * Create a new instance containing a range of elements.
     *
     * @param mixed $low First value of the sequence
     * @param mixed $high The sequence is ended upon reaching the end value
     * @param int $step Used as the increment between elements in the sequence
     *
     * @return static The created array
     */
    public static function createWithRange($low, $high, $step = 1)
    {
        return new static(range($low, $high, $step));
    }

    // The public method list order by ASC

    /**
     * Check if the given value exists in the array.
     *
     * @param mixed $element Value to search for
     *
     * @return bool Returns true if the given value exists in the array, false otherwise
     */
    public function contains($element)
    {
        return in_array($element, $this->elements, true);
    }

    /**
     * Check if the given key/index exists in the array.
     *
     * @param mixed $key Key/index to search for
     *
     * @return bool Returns true if the given key/index exists in the array, false otherwise
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->elements);
    }

    /**
     * Returns the number of values in the array.
     *
     * @link http://php.net/manual/en/function.count.php
     *
     * @return int total number of values
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Clone current instance to new instance.
     *
     * @deprecated Should be removed
     *
     * @return $this Shallow copy of $this
     */
    public function createClone()
    {
        return clone $this;
    }

    /**
     * Find the given value in the array using a closure
     *
     * @param callable $func
     *
     * @return bool Returns true if the given value is found, false otherwise
     */
    public function exists(callable $func)
    {
        $isExists = false;

        foreach ($this->elements as $key => $value) {
            if ($func($key, $value)) {
                $isExists = true;
                break;
            }
        }

        return $isExists;
    }

    /**
     * Returns the first occurrence of a value that satisfies the predicate $func.
     *
     * @param callable $func
     *
     * @return mixed The first occurrence found
     */
    public function find(callable $func)
    {
        $found = null;

        foreach ($this->elements as $key => $value) {
            if($func($value, $key)) {
                $found = $value;
                break;
            }
        }

        return $found;
    }

    /**
     * Create an iterator over this array.
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * Return an array all the keys of this array.
     *
     * @return array An array of all keys
     */
    public function getKeys()
    {
        return array_keys($this->elements);
    }

    /**
     * Pick a random value out of this array.
     *
     * @return mixed Random value of array
     *
     * @throws \RangeException If array is empty
     */
    public function getRandom()
    {
        return $this->offsetGet($this->getRandomKey());
    }

    /**
     * Pick a random key/index from the keys of this array.
     *
     * @return mixed Random key/index of array
     *
     * @throws \RangeException If array is empty
     */
    public function getRandomKey()
    {
        return $this->getRandomKeys(1);
    }

    /**
     * Pick a given number of random keys/indexes out of this array.
     *
     * @param int $number The number of keys/indexes (should be <= $this->count())
     *
     * @return mixed Random keys or key of array
     *
     * @throws \RangeException
     */
    public function getRandomKeys($number)
    {
        $number = (int) $number;

        $count = $this->count();
        if ($number === 0 || $number > $count) {
            throw new \RangeException(sprintf(
                'Number of requested keys (%s) must be equal or lower than number of elements in this array (%s)',
                $number,
                $count
            ));
        }

        return array_rand($this->elements, $number);
    }

    /**
     * Pick a given number of random values with non-duplicate indexes out of the array.
     *
     * @param int $number The number of values (should be > 1 and < $this->count())
     *
     * @return array Random values of array
     *
     * @throws \RangeException
     */
    public function getRandomValues($number)
    {
        $values = [];

        $keys = $number > 1 ? $this->getRandomKeys($number) : [$this->getRandomKeys($number)];
        foreach ($keys as $key) {
            $values[] = $this->offsetGet($key);
        }

        return $values;
    }

    /**
     * Return an array of all values from this array numerically indexed.
     *
     * @return mixed An array of all values
     */
    public function getValues()
    {
        return array_values($this->elements);
    }

    /**
     * Search for a given element and return the index of its first occurrence.
     *
     * @param mixed $element Value to search for
     *
     * @return mixed The corresponding key/index
     */
    public function indexOf($element)
    {
        return array_search($element, $this->elements, true);
    }

    /**
     * Check whether array is associative or not.
     *
     * @return bool Returns true if associative, false otherwise
     */
    public function isAssoc()
    {
        $isAssoc = true;

        if ($this->isEmpty()) {
            $isAssoc = false;
        } else {
            foreach ($this->getKeys() as $key) {
                if (!is_string($key)) {
                    $isAssoc = false;
                    break;
                }
            }
        }

        return $isAssoc;
    }

    /**
     * Check whether the array is empty or not.
     *
     * @return bool Returns true if empty, false otherwise
     */
    public function isEmpty()
    {
        return !$this->elements;
    }

    /**
     * Check whether array is numeric or not.
     *
     * @return bool Returns true if numeric, false otherwise
     */
    public function isNumeric()
    {
        $isNumeric = true;

        if ($this->isEmpty()) {
            $isNumeric = false;
        } else {
            foreach ($this->getKeys() as $key) {
                if (!is_int($key)) {
                    $isNumeric = false;
                    break;
                }
            }
        }

        return $isNumeric;
    }

    /**
     * Whether an offset exists.
     *
     * @param mixed $offset An offset to check for.
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->elements[$offset]);
    }

    /**
     * Retrieve the current offset or null.
     *
     * @param mixed $offset The offset to retrieve.
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->elements[$offset])
            ? $this->elements[$offset]
            : null
        ;
    }

    /**
     * Set an offset for this array.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        if (isset($offset)) {
            $this->elements[$offset] = $value;
        } else {
            $this->elements[] = $value;
        }

        return $this;
    }

    /**
     * Remove a present offset.
     *
     * @param mixed $offset The offset to unset.
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @return $this
     */
    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);

        return $this;
    }

    /**
     * Reduce the array to a single value iteratively combining all values using $func.
     *
     * @param callable $func callback ($carry, $item) -> next $carry
     * @param mixed|null $initial starting value of the $carry
     *
     * @return mixed Final value of $carry
     */
    public function reduce(callable $func, $initial = null)
    {
        return array_reduce($this->elements, $func, $initial);
    }
}
