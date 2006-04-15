<?php
/**
 * To jest pojedynczy logger
 * SVNId: $Id$
 * @author Piotr 'bela_666' Belina
 * @package pl.bela666.odin.logger
 * @version 0.1
 */
class LoggerBean implements Iterator {
    /**
     * Wiadomości
     * @access private
     */
    private $messages = array();

    /**
     * Poziom logowania
     * @access private
     */
    private $level;

    private $time;
    
    private $texts = array(1 => 'debug',
    2 => 'info',
    3 => 'warn',
    4 => 'error',
    5 => 'fatal');
    /**
     * Konstruktor
     * @access public
     * @return void
     */
    public function __construct() {
        if(defined('LOGGER_LEVEL')) {
            $this->level = LOGGER_LEVEL;
        } else {
            $this->level = LOGGER_LEVEL_DEBUG;
        }
    }

    /**
     * Dodaje wiadomość DEBUG, jeśli poziom jest pozwala
     */
    public function debug($message) {
        $this->addMessage($message, LOGGER_LEVEL_DEBUG);
    }

    /**
     * Dodaje wiadomość INFO, jeśli poziom jest pozwala
     */
    public function info($message) {
        $this->addMessage($message, LOGGER_LEVEL_INFO);
    }

    /**
     * Dodaje wiadomość WARN, jeśli poziom jest pozwala
     */
    public function warn($message) {
        $this->addMessage($message, LOGGER_LEVEL_WARN);
    }

    /**
     * Dodaje wiadomość ERROR, jeśli poziom jest pozwala
     */
    public function error($message) {
        $this->addMessage($message, LOGGER_LEVEL_ERROR);
    }

    /**
     * Dodaje wiadomość FATAL, jeśli poziom jest pozwala
     */
    public function fatal($message) {
        $this->addMessage($message, LOGGER_LEVEL_FATAL);
    }

    private function addMessage($message, $level) {
        if($this->level <= $level) {
            $this->messages[] = array('level' => $this->convertConstToText($level),
            'message' => $message,
            'time' => $this->getTime());
        }
    }
    /**
     * Pobiera poziom logowania
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * Ustawia poziom logowania
     */
    public function setLevel($level) {
        $this->level = $level;
    }

    /**
     * Wyświetla błedy
     */
    public function display() {
        print '<h3 class="post-title">Logger Log</h3><div>';
        $this->time = $this->getTime();
        foreach ($this->messages as $k => $v) {
            print '<span style="font-weight: bold; color: #007700">[' . $v['level'] . ']</span> ' . $v['message'] . ' ' . /*($this->time - $v['time']) .*/ "<br />\n";
        }
        print '</div>';
    }

    /**
     * Pobiera wszystkiego wiadomości
     */
    public function fetch() {
        return $this->messages;
    }

    /**
     * Zmienia stałą w tekst
     */
    private function convertConstToText($level) {
        return $this->texts[$level];
    }

    private function getTime() {
        return microtime(true) * 1000;
    }
    /* Iterator */
    public function current() {
        return current($this->messages);
    }

    public function next() {
        return next($this->messages);
    }

    public function key() {
        return key($this->messages);
    }

    public function valid() {
        return $this->current() !== false;
    }

    public function rewind() {
        return reset($this->messages);
    }
}
?>
