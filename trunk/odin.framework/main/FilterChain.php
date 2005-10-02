<?php
class FilterChain {
    private $filters;
    private $firstFilter;
    
    public function addFilter($name, IHandle $filter) {    
        $this->filters[] = new FilterWrapper($name, $filter);
    }
    
    public function process() {
		$this->addFilter('exec', new SimpleHandle('ExecFilter'));
        $this->makeChain();
        $this->firstFilter->process();
    }
    
    public function makeChain() {
        $prev = null;
        $next = null;
        
        foreach($this->filters as $filter) {
			$filter = $filter->filter->getTarget();
            if($this->firstFilter == null) {
                $this->firstFilter = $filter;
            }            
            if($prev != null) {
                $prev->setNextFilter($filter);
            }
            $prev = $filter;            
        }
    }
}

class FilterWrapper {
	public $name;
	public $filter;

	public function __construct($name, IHandle $filter) {
		$this->name = $name;
		$this->filter = $filter;
	}
}

class TestFilter extends AFilter {
    public function process() {
        print 'Test Filter pre-processing<br/>';
        $this->processNext();
        print 'Test Filter pre-processing<br/>';
    }
}

class SecondTestFilter extends AFilter {
    public function process() {
        print 'Second Test Filter pre-processing<br/>';
        $this->processNext();
        print 'Second Test Filter pre-processing<br/>';
    }
}
?>
