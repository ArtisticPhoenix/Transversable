<?php
namespace evo\transversable;

/**
 * @author hdurham
 */
class TransverseableItem implements TransversableInterface, \ArrayAccess{  
    
    use TransversableTrait;
    
    /**
     *
     * @var array
     */
    protected $items = [];
    
    
    /**
     * @todo enviroments
     */
    public function __construct($data=null) {
        if(null !== $data){
            if(is_array($data)){
                $this->build($data, true);
            }else{
                $this->items = $data;
            }
        }
    }
    
    /**
     * Set multiple values
     *
     * @param array $data - array of key value pairs to set
     * @param bool $overwrite - overwrite  ( replace or merge with )
     */
    public function build(array $data, $overwrite=true){
        foreach ($data as $key => $item) $this->set($key, $item, $overwrite);
    }
 
    /**
     *
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @param mixed|null $defualt - a default value to return
     * @param bool|$extend - return as a TransverseableItem
     * @return mixed|TransverseableItem
     */
    public function get($key = null, $defualt=null, $extend=false) {    
        $value = null !== $key && is_array($this->items) ? self::transversableGet($key, $this->items, $defualt) : $this->items;
        return $value && $extend ? new TransverseableItem($value) : $value;
    }
    
    /**
     *
     * set a config value
     *
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @param mixed $value - the value to add to array
     * @param bool $overwrite - overwrite  ( replace or merge with )
     */
    public function set($key, $value, $overwrite=true) {       
        if(!is_array($this->items)){
            $this->items = [];
        }
        
        if(!$key){
            if(is_array($value)){
                $this->build($value, $overwrite);
            }else{
                $this->items = $value;
            }
        }else{
            self::transversableSet($key, $value, $this->items, $overwrite);
        }
    }
    
    /**
     * Check if a config value is set
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @return bool
     */
    public function isset($key) {
        if(!is_array($this->items)){
            return !is_null($this->items);
        }
        
        return self::transversableIsset($key, $this->items);
    }
    
    /**
     * Unset a confg value - ( this is only temporary and notpersitant)
     * @param mixed $key - string or delimted string or array of keys to transverse
     */
    public function unset($key) {
        if(!is_array($this->items)){
            $this->items = null;
        }else{
            self::transversableUnset($key, $this->items);
        }  
    }
    
    /**
     *
     * @param string $offset
     * @return mixed
     */
    public function __get($name) {
        return $this->get($name);
    }
    
    /**
     *
     * @param string $offset
     * @return mixed
     */
    public function __set($name, $value) {
        $this->set($name,$value);
    }
    
    /**
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return $this->isset($name);
    }
    
    
    /**
     *
     * @param string $name
     */
    public function __unset($name) {
        $this->unset($name);
    }
    
    /**
     * 
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * 
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->isset($offset);
    }

    /**
     * 
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->unset($offset);
    }

    /**
     * you can use this method as the default when you wish to throw an exception for an unknown key
     */
    public static function throwUnkownVariable() {
        return function($key) {
            throw new \Exception("Unknown transversable item [".$key."]");
        };
    }

}