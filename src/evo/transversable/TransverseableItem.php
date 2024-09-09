<?php
namespace evo\transversable;

use evo\exception as E;

/**
 * @author hdurham
 */
class TransverseableItem implements TransversableInterface, \ArrayAccess{
    use TransversableTrait;
    
    /**
     *
     * @var mixed
     */
    protected mixed $items = null;

    /**
     * @param mixed|null $data
     *
     */
    public function __construct(mixed $data=null) {
        if(null !== $data){
            if(is_array($data)){
                $this->build($data, true);
            }else{
                $this->items = $data;
            }
        }
    }

    /**
     * @param array $data
     * @param bool $overwrite
     * @return $this
     *
     */
    public function build(array $data, bool $overwrite=true): static
    {
        foreach ($data as $key => $item)
            $this->set($key, $item, $overwrite);

        return $this;
    }

    /**
     * @param array|string|null $key
     * @param mixed|null $default
     * @param bool $extend
     * @return mixed
     */
    public function get(array|string $key = null, mixed $default=null, bool $extend=false): mixed
    {
        $value = null !== $key && is_array($this->items) ? self::transversableGet($key, $this->items, $default) : $this->items;
        return $value && $extend ? new static($value) : $value;
    }

    /**
     * @param array|string $key
     * @param mixed $value
     * @param bool $overwrite
     * @return void
     * @throws \Exception
     */
    public function set(array|string $key, mixed $value, bool $overwrite=true): void
    {

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
     * @param array|string $key - string or delimited string or array of keys to transverse
     * @return bool
     */
    public function isset(array|string $key): bool
    {
        if(!is_array($this->items)){
            return !is_null($this->items);
        }
        
        return self::transversableIsset($key, $this->items);
    }
    
    /**
     * Unset a value
     * @param array|string $key - string or delimited string or array of keys to transverse
     */
    public function unset(array|string $key): void
    {
        if(!is_array($this->items)){
            $this->items = null;
        }else{
            self::transversableUnset($key, $this->items);
        }  
    }

    /**
     * @param string $name
     * @return TransverseableItem|mixed
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }
    
    /**
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set($name,$value);
    }
    
    /**
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->isset($name);
    }

    /**
     *
     * @param string $name
     */
    public function __unset(string $name): void
    {
        $this->unset($name);
    }
    
    /**
     * 
     * @param string $offset
     * @return mixed
     */
    #[\Override] public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }
    
    /**
     *
     * @param string $offset
     * @param mixed $value
     */
    #[\Override] public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * 
     * @param string $offset
     * @return boolean
     */
    #[\Override] public function offsetExists(mixed $offset): bool
    {
        return $this->isset($offset);
    }

    /**
     * 
     * @param string $offset
     */
    #[\Override] public function offsetUnset(mixed $offset): void
    {
        $this->unset($offset);
    }

    /**
     * @return mixed
     */
    #[\Override] public function getAll(): mixed
    {
        return $this->items;
    }

    /**
     * You can use this callback method as the default when you wish to throw an evo\OutOfBoundsException for an unknown key
     *
     * @return callable
     */
    public static function throwUnkownVariable(): callable{
        return function($key) {
            throw new E\OutOfBoundsException("Unknown transversable item [".$key."]");
        };
    }

}