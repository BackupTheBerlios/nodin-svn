<?php
class SimpleHandle implements IHandle {
    private $name;
    private $args;
    private $target;
    
    public function __construct($name, $args = null) {
        $this->name = $name;
        $this->args = $args;
    }
    
    public function getTarget() {
        if($this->target == null) {
            $this->target = $this->createTarget();
        }
        return $this->target;
    }
    
    public function getClass() {
        return $this->name;
    }
    
    private function createTarget() {
        switch(count($this->args)) {
            case 0:
                return new $this->name;
            case 1:
                return new $this->name($this->args[0]);
            case 2:
                return new $this->name($this->args[0], $this->args[1]);
            case 2:
                return new $this->name($this->args[0], $this->args[1], $this->args[2]);
            default:
                throw new NotYetImplementedException();
        }
    }
}
?>
