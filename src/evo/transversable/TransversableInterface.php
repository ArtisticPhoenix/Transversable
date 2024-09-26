<?php
namespace evo\transversable;

/**
 *  Methods for key transversal of set, get, isset, unset
 *  These methods are not intended to be called externally to the class.
 *  Instead, implement the TransverableInterface which is for public consumption
 *
 *  For license information please view the LICENSE file included with this source code.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package
 * @version 2.1.0
 */
interface TransversableInterface{
    
    /**
     *
     * Set multiple config values
     *
     * @param mixed $data - string or delimited string or array of keys to transverse
     * @param bool $overwrite - overwrite  ( replace or merge with )
     */
    public function build(array $data, bool $overwrite=true): static;
    
    /**
     * Get a specific piece of data
     * The default value should accept a closure as a callback with the following signature
     *     fn(string|array $key, array $array) : mixed
     *
     * @param string|array $key - string or delimited string or array of keys to transverse
     * @param mixed $default - a default value to return
     *
     * @return mixed
     *
     * @example
     * $obj->get('foo.bar', $default);
     * $obj->get(['foo','bar'], $default);
     * - returns $array['foo']['bar']  or  default
     */
    public function get(string|array $key, mixed $default=null): mixed;

    /**
     * get all data
     * @return mixed
     */
    public function getAll(): mixed;
    
    /**
     *
     * set a config value
     *
     * @param mixed $key - string or delimited string or array of keys to transverse
     * @param mixed $value - the value to add to array
     * @param bool $overwrite - overwrite  ( replace or merge with )
     *
     * @return void
     */
    public function set(array|string $key, mixed $value, bool $overwrite=true): void;
    
    /**
     * Check if a config value is set
     * @param mixed $key - string or delimited string or array of keys to transverse
     * @return bool
     */
    public function isset(array|string $key): bool;
    
    /**
     * Unset a  value -
     * @param mixed $key - string or delimited string or array of keys to transverse
     */
    public function unset(array|string $key): void;

    /**
     * Use as a default to throw a missing key exception
     * @param string $message - extra information to added to the exception message
     * @return callable
     */
    public static function throwOutOfBoundsException(string $message=''): callable;
}