<?php
namespace evo\transversable;

/**
 * Methods for key transversal of set, get, isset, unset
 * These methods are not inteneded to be called externally to the class.  
 * Instead impliment the TransverableInterface which is for public consumption
 * 
 * For license information please view the LICENSE file included with this source code.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package
 * @version 1.0.0
 */
trait TransversableTrait{
    
    /**
     * 
     * @var string - a list of symbols keys can be split on ( in PHP dots arn't allowed in Super Globals, so it's a good choice as a seperator )
     */
    protected static $DELIMITER = '.';

    /**
     * Split the key into an array And/OR normalize to an array
     *
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @return array
     */
    private static function transversableSplitKeys($key){
        if(is_null($key)) return null;
        
        if(is_array($key)){
            //check for multi-dimensional keys ( which is not supported )
            if(count($key) != count($key,COUNT_RECURSIVE)){
                print_rr($key);
                throw new \Exception('invalid transversable key type['.gettype($key).']');
            }
            
            return $key;
        }
        
        switch (gettype($key)){
            case 'boolean':
            case 'integer':     
            case 'double':    
                return [$key];  
                //exit method
            case 'string':
                return preg_split('/['.preg_quote(static::$DELIMITER,'/').']+/', $key, -1, PREG_SPLIT_NO_EMPTY);
                //exit method
        } //end switch

        throw new \Exception('invalid transversable key type['.gettype($key).']');
    }
    
    /**
     *
     * check if an attribute exists
     *
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @param array $array - the array to transverse
     * @return bool
     */
    protected static function transversableIsset($key, array $array){
        $keys = self::transversableSplitKeys($key);

        foreach($keys as $key){
            if(!isset($array[$key])) return false;
            
            $array = $array[$key]; 
        }
        
        return true;
    }
    
    /**
     *
     * set an attribute
     *
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @param mixed $value - the value to add to array
     * @param array $array - the array to modify
     * @param bool $overwrite - overwrite all child arrays (usefull for numeric indexes)
     */
    protected static function transversableSet($key, $value, array &$array, $overwrite=false){  
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
                   if(is_array($key)) throw new \Exception();
                   
                   
                    $array[$key] = $value;
            }
        }else{
            if(!isset($array[$key])) $array[$key] = [];
                
            self::transversableSet($keys, $value, $array[$key], $overwrite); //recursive
        }
    }
    
    /**
     * Get attribute
     *
     * @param mixed $key - string or delimted string or array
     * @param array $array - the arry to get from
     * @param mixed $default - return this value when not set (applies to all keys if multiple)
     * @return mixed
     */
    protected static function transversableGet($key, array $array, $default=null){        
        $keys = self::transversableSplitKeys($key);

        foreach ($keys as $key){
            if(!isset($array[$key])){
                if(is_callable($default)){
                    $default = $default(...array_slice(func_get_args(), 0, 2)); //$key, $array - without default
                }
                return $default;
            }
            
            $array = $array[$key];
        }
        
        return $array;
    }
    
    /**
     * remove an item from $array by $key
     *
     * @param mixed $key - string or delimted string or array
     * @param array $array - the array to modify
     */
    protected static function transversableUnset($key, array &$array){
        $keys = self::transversableSplitKeys($key);
        $key = array_shift($keys);
        
        if (count($keys) == 0){
            unset($array[$key]);
        }else{
            self::transversableUnset($keys, $array[$key]); //recursive
        }
    }
    
    
}