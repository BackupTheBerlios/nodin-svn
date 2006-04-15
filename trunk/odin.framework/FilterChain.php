<?php
class FilterChain {
	private $filters = array();
	private $firstFilter;
	
	public function addFilter($filterName) {	
		$this->filters[] = $filter;
	}
	
	public function addFilters($filters) {
		$this->filters = array_merge($this->filters, $filters);
	}
	
	public function process(IHttpContext $context, ActionWrapper $action) {
		$this->makeChain();
		$this->firstFilter->process($context, $action);
	}
	
	private function makeChain() {		
		$prev = null;
		$next = null;
		
		foreach($this->filters as $filter) {
			$next = new $filter;			
			if($this->firstFilter == null) {
				$this->firstFilter = $next;
			}			
			if($prev != null) {
				$prev->setNextFilter($next);
			}
			$prev = $next;			
		}
		$next->setNextFilter(new ExecFilter());
	}
}
?>