<?php
namespace evo\transversable;

use evo\exception as E;

/**
 * Methods for key transversal of set, get, isset, unset
 * These methods are not intended to be called externally to the class.
 * Instead, implement the TransverableInterface which is for public consumption
 * 
 * For license information please view the LICENSE file included with this source code.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package
 * @version 2.1.0
 */
trait TransversableTrait{
    
    /**
     * ( in PHP dots aren't allowed in Super Globals, so it's a good choice as a separator )
     *
     * @var string - a list of symbols that keys can be split on e.g. '.,-'
     */
    protected static string $DELIMITER = '.';

    /**
     * Normalize the transverse keys to an array
     *
     * @param string|array $key - delimited string (key0.key1.key2) or array of keys ([key0,key1,key2]) to transverse
     * @return array
     * @throws E\InvalidArgumentException
     */
    protected static function transversableSplitKeys(string|array $key): array{
        if(is_array($key)){
            //check for multidimensional keys ( which is not supported )
            if(count($key) != count($key,COUNT_RECURSIVE)){
                throw new E\InvalidArgumentException("Multidimensional transversable array keys are not supported.");
            }
            return $key;
        }

        return preg_split(
            '/['.preg_quote(static::$DELIMITER,'/').']+/',
            $key,
            -1,
            PREG_SPLIT_NO_EMPTY
        );
    }

    /**
     * check if an attribute exists
     *
     * @param string|array $key - delimited string (key0.key1.key2) or array of keys ([key0,key1,key2]) to transverse
     * @param array $array - the array to transverse
     * @return bool
     *
     * @throws E\InvalidArgumentException
     */
    protected static function transversableIsset(string|array $key, array $array): bool {
        $keys = self::transversableSplitKeys($key);

        foreach($keys as $key){
            if(!isset($array[$key])) return false;
            
            $array = $array[$key]; 
        }
        
        return true;
    }

    /**
     * set a value
     *
     * @param string|array $key - delimited string (key0.key1.key2) or array of keys ([key0,key1,key2]) to transverse
     * @param mixed $value - the value to add to array
     * @param array $array - - the array to modify (pass by reference)
     * @param bool $overwrite - overwrite all child arrays (useful for numeric indexes)
     *
     * @return void
     */
    protected static function transversableSet(
        string|array $key,
        mixed $value,
        array &$array,
        bool $overwrite=false
    ): void {
        $keys = self::transversableSplitKeys($key);
        $key = array_shift($keys) ?? 0;
        
        if (empty($keys)){
            switch (gettype($value)){
               case 'array':
                    if(!isset($array[$key])) $array[$key] = [];
                    if($overwrite){
                        $array[$key] =  $value;
                    }else{
                        $array[$key] = array_replace_recursive($array[$key], $value);
                    }
               break;
               default:
                    $array[$key] = $value;
            }
        }else{
            if(!isset($array[$key])) $array[$key] = [];
                
            self::transversableSet($keys, $value, $array[$key], $overwrite); //recursive
        }
    }

    /**
     * Get a value
     *
     * @param string|array $key - delimited string (key0.key1.key2) or array of keys ([key0,key1,key2]) to transverse
     * @param array $array - the array to get from
     * @param mixed $default - return this value when not set (applies to all keys if multiple)
     * @return mixed
     * @throws E\InvalidArgumentException
     */
    protected static function transversableGet(
        string|array $key,
        array $array,
        mixed $default=null
    ): mixed {
        $keys = self::transversableSplitKeys($key);

        foreach ($keys as $key){
            if(!isset($array[$key])){
                if(is_callable($default)){
                    //$key, $array - without default
                    $default = $default(...array_slice(func_get_args(), 0, 2));
                }
                return $default;
            }
            
            $array = $array[$key];
        }
        
        return $array;
    }

    /**
     * Remove an item from $array by $key
     *
     * @param string|array $key - delimited string (key0.key1.key2) or array of keys ([key0,key1,key2]) to transverse
     * @param array $array - the array to modify (pass by reference)
     *
     * @return void
     *
     * @throws E\InvalidArgumentException
     */
    protected static function transversableUnset(string|array $key, array &$array): void{
        $keys = self::transversableSplitKeys($key);
        $key = array_shift($keys);
        
        if (count($keys) == 0){
            unset($array[$key]);
        }else{
            self::transversableUnset($keys, $array[$key]); //recursive
        }
    }

    /**
     * @param string $message
     * @return callable
     */
    public static function throwOutOfBoundsException(string $message=''): callable{
        return static function($key) use ($message){
            throw new E\OutOfBoundsException("Unknown transversable item [".$key."].{$message}");
        };
    }
    
}