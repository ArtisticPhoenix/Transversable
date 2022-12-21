<?php
namespace evo\transversable;


interface TransversableInterface{
 
    /**
     * @todo enviroments
     */
    public function __construct($data=null);
    
    /**
     *
     * set multiple config values
     *
     * @param mixed $data - string or delimted string or array of keys to transverse
     * @param bool $overwrite - overwrite  ( replace or merge with )
     */
    public function build($data, $overwrite=true);
    
    /**
     *
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @param mixed|null $defualt - a default value to return
     * @return mixed|TransverseableItem
     */
    public function get($key = null, $defualt=null);
    
    /**
     *
     * set a config value
     *
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @param mixed $value - the value to add to array
     * @param bool $overwrite - overwrite  ( replace or merge with )
     */
    public function set($key, $value, $overwrite=true);
    
    /**
     * Check if a config value is set
     * @param mixed $key - string or delimted string or array of keys to transverse
     * @return bool
     */
    public function isset($key);
    
    /**
     * Unset a confg value - ( this is only temporary and notpersitant)
     * @param mixed $key - string or delimted string or array of keys to transverse
     */
    public function unset($key);
}