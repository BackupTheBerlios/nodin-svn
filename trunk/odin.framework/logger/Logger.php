<?php
define('LOGGER_LEVEL_DEBUG', 1);
define('LOGGER_LEVEL_INFO',  2);
define('LOGGER_LEVEL_WARN',  3);
define('LOGGER_LEVEL_ERROR', 4);
define('LOGGER_LEVEL_FATAL', 5);

/**
 * Logger
 * 
 * SVNId: $Id$
 * @author Piotr 'bela_666' Belina
 */
class Logger {
    /**
     * Messages
     *      
     * @access private
     * @staticvar array
     */
    private static $messages = array();

    /**
     * Level of logging
     * 
     * @access private
     * @staticvar int
     */
    private static $level;

    /**
     * Start time
     *
     * @access private
     * @staticvar int
     */
    private static $startTime;
    
    /**
     * Instance of Timer
     *
     * @access private
     * @staticvar Timer
     */
    private static $timer;
	
    /**
     * Values needed to format output text
     *
     * @access private
     * @staticvar array
     */
    private static $texts = array(1 => 'debug',
    2 => 'info',
    3 => 'warn',
    4 => 'error',
    5 => 'fatal');
    
    /**
     * Constructor
     * Initialize Logger
     * 
     * @access public     
     */
    public function __construct() {
        if(defined('LOGGER_LEVEL')) {
            self::$level = LOGGER_LEVEL;
        } else {
            self::$level = LOGGER_LEVEL_DEBUG;
        }
        self::$timer = Timer::getInstance();
    }

    /**
     * Adds debug message
     * 
     * @access public
     * @static 
     * @param string Message
     */
    public static function debug($message) {
        self::addMessage($message, LOGGER_LEVEL_DEBUG);
    }

    /**
     * Adds info message
     * 
     * @access public
     * @static 
     * @param string Message
     */
    public static function info($message) {
        self::addMessage($message, LOGGER_LEVEL_INFO);
    }

    /**
     * Adds warn message
     * 
     * @access public
     * @static 
     * @param string Message
     */
    public static function warn($message) {
        self::addMessage($message, LOGGER_LEVEL_WARN);
    }

    /**
     * Adds error message
     * 
     * @access public
     * @static 
     * @param string Message
     */
    public static function error($message) {
        self::addMessage($message, LOGGER_LEVEL_ERROR);
    }

    /**
     * Adds fatal message
     * 
     * @access public
     * @static 
     * @param string Message
     */
    public static function fatal($message) {
        self::addMessage($message, LOGGER_LEVEL_FATAL);
    }

    /**
     * Adds message to inner array if level is adequate
     * 
     * @access private
     * @static 
     * @param string Message
     * @param int Level of message
     */
    private static function addMessage($message, $level) {
        if(self::$level <= $level) {
            self::$messages[] = array('level' => self::convertConstToText($level),
            'message' => $message,
            'time' => self::$timer->start($message));
        }
    }
    /**
     * Retunrs level of logging
     * 
     * @access public
     * @return int Level
     * @static 
     */
    public static function getLevel() {
        return self::$level;
    }

    /**
     * Sets level of logging
     * 
     * @access public
     * @param int Level
     * @static 
     */
    public static function setLevel($level) {
        self::$level = $level;
    }

    /**
     * Displays log
     * 
     * @access public
     * @static 
     */
    public static function display() {
        print '<h3 class="post-title">Logger Log</h3><table>';
        foreach (self::$messages as $k => $v) {
        	$i = 0;
        	$delta = self::$timer->stop($v['message']);
        	print '<tr class="info-' . $i % 2 . '"><td><span style="font-weight: bold; color: #007700;">[' . $v['level'] . ']</span></td><td>' .$v['message'] .'</td><td>' . $delta . '</td></tr>';
            $i++;
	}
        print '</table>';
    }

    /**
     * Returns all fetched messages
     * 
     * @access public
     * @return array Messages
     * @static 
     */
    public static function fetch() {
        return $this->messages;
    }

    /**
     * Converse level constant into text
     * 
     * @access private
     * @param int Level
     * @return string Level text
     * @static 
     */
    private static function convertConstToText($level) {
        return self::$texts[$level];
    }

    /**
     * Returns time diffrence between now and initialize
     *
     * @access private
     * @return float Time diffrence
     * @static 
     */
    private static function getTime() {
        if(!isset(self::$startTime))
            self::$startTime = microTime(true);
        return microtime(true) - self::$startTime;
    }
}
?>
