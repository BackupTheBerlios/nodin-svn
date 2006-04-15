<?php
/**
 * Repozytorium loggerow ;]
 */
class LoggerRepository {
    /**
     * Tablica z loggeram
     */
    private static $repository = array();
    
    /**
     * Pobiera logger
     * @static 
     */
    public static function getLogger($name) {
        if(!array_key_exists($name, self::$repository)) {
            self::$repository[$name] = new LoggerBean();
        }
        return self::$repository[$name];
    }
}
?>