<?php
class FakeHandle implements IHandle {
    private $target;
    public function __construct($class) {
        $this->target = $class;
    }
    
    public function getTarget() {
        return $this->target;
    }
    
    public function getClass() {
        get_class($this->target);
    }
}
?>
