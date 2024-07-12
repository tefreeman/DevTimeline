<?php

/**
 * Cache class for managing local cache in memory
 */
class cache
{
    static public $cacheArr = array();
    
    static function cacheExists($key)
    {
        if(isset(self::$cacheArr[$key]))
        {
            return true;
        }
        
        return false;
    }
    
    static function getCache($key)
    {
        if(self::cacheExists($key))
        {
            $value = self::$cacheArr[$key]['value'];
            $type = self::$cacheArr[$key]['type'];
            if($type == 'object' || $type == 'array')
            {
                return unserialize($value);
            }
            
            return $value;
        }
        
        return false;
    }
    
    static function setCache($key, $value)
    {
        self::$cacheArr[$key] = array();
        self::$cacheArr[$key]['type'] = gettype($value);
        
        if(is_array($value) || is_object($value))
        {
            $value = serialize($value);
        }
        
        self::$cacheArr[$key]['value'] = $value;

        return true;
    }
    
    static function clearAllCache()
    {
        unset(self::$cacheArr);
        self::$cacheArr = array();
    }
}
