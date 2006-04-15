<?php

/**
 * @package phiend
 * @subpackage core
 * @author Maciej Jarzebski
 * @author Piotr Belina
 */

/**
 * A wrapper for an action's object and all assorted attributes.
 * This is basically a structure used by the controller to store data.
 * Attributes available are:
 * - sName: string, the action's name
 * - aParams: array, the action's parameters as an associative array (name => value)
 * - aConfig: array, the action's filter configuration as an associative array of arrays (filter name => filter's configuration as an array), available once the config factory has been called
 * - obj: IPerformable, the action itself, available once the action factory has been called
 * - sError: string, action's error code, available after the action is performed, if ActionErrorFilter is used
 * - mResult: mixed, value returned by action
 */
class ActionWrapper {
	
	private $aAttributes;

	public function __construct($sName, $aParams) {
		$this->aAttributes = array(
    		'sName'     => $sName,
			'aParams'   => $aParams,
        	'aConfig'	=> null,
        	'obj'       => null,
        	'sError'	=> null,
        	'mResult'   => null,
        	'output'	=> null,
    	);
	}

	public function __get($sName) {
		if (array_key_exists($sName, $this->aAttributes)) {
			return $this->aAttributes[$sName];
		}
		throw new NoSuchAttributeException();
	}
	
	public function __set($sName, $mValue) {
		if (array_key_exists($sName, $this->aAttributes) && is_null($this->aAttributes[$sName])) {
			$this->aAttributes[$sName] = $mValue;
        } else {
		    throw new NoSuchAttributeException("Unable to set $sName");
        }
	}
}

?>