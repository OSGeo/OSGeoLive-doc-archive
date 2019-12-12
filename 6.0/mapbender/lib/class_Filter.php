<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

class Filter {
	// simple filter
	protected $operator;
	protected $key;
	protected $value;

	// complex filter
	protected $booleanOperator;
	protected $filterArray;

	const OPERATORS = "=,>,>=,<,<=,<>,LIKE,ILIKE,IN";
	const BOOLEAN_OPERATORS = "AND,OR";
	const BOOLEAN = "TRUE,FALSE";

	public function __construct () {
		if (func_num_args() === 3) {
			if (!in_array(func_get_arg(0), explode(",", Filter::OPERATORS))) {
				throw new Exception ("Filter: Invalid operator " . func_get_arg(0)); 
			}
			$this->operator = func_get_arg(0);
			
			if (!is_string(func_get_arg(1)) || func_get_arg(1) === "") {
				throw new Exception ("Filter: Invalid key " . func_get_arg(1)); 
			}
			$this->key = func_get_arg(1);
			
			$this->value = func_get_arg(2);
		}
		else if (func_num_args() === 2) {
			if (!in_array(func_get_arg(0), explode(",", Filter::BOOLEAN_OPERATORS))) {
				throw new Exception ("Filter: Invalid boolean operator " . func_get_arg(0)); 
			}
			$this->booleanOperator = func_get_arg(0);
			
			if (is_array(func_get_arg(1))) {
				foreach (func_get_arg(1) as $filter) {
					if (!is_a($filter, "Filter")) {
						throw new Exception("Filter: Not a valid filter.");
					}
				}
				$this->filterArray = func_get_arg(1);
			}
		}
		else if (func_num_args() === 1) {
			if (in_array(strtoupper(func_get_arg(0)), explode(",", Filter::BOOLEAN))) {
				$this->value = strtoupper(func_get_arg(0));
			}
		}
		else {
			
		}
	}
	
	public function toSql ($parameterCount = 1) {
		$sqlObject = new stdClass();
		$sqlObject->sql = "";
		$sqlObject->v = array();
		$sqlObject->t = array();			
		
		if ($this->isComplex()) {
			$i = $parameterCount;
			$initialized = false;
			foreach ($this->filterArray as $filter) {
				$currentSqlObject = $filter->toSql($i);
				
				if ($currentSqlObject->sql === "") {
					continue;
				}
				$currentBooleanOperator = (!$initialized) ? 
					"" : " " . $this->booleanOperator . " ";
				$initialized = true;
				$sqlObject->sql .= $currentBooleanOperator . 
					$currentSqlObject->sql;
				$sqlObject->v = array_merge($sqlObject->v, $currentSqlObject->v);
				$sqlObject->t = array_merge($sqlObject->t, $currentSqlObject->t);
				$i += count($currentSqlObject->v);
			}
			$sqlObject->sql = $sqlObject->sql !== "" ?
				"(" . $sqlObject->sql . ")" : $sqlObject->sql;
			return $sqlObject;
		}
		else {
			if (is_array($this->value)) {
				if ($this->operator === "IN") {
					$parameters = array();
					foreach ($this->value as $value) {
						$parameters[]= "$" . $parameterCount++;
						$sqlObject->v[]= $value;
						$sqlObject->t[]= $this->getType($value);			
					}
					$sqlObject->sql = $this->key . " " . $this->operator . 
						" (" . implode(",", $parameters) . ")";
				}
				else {
					throw new Exception("Filter: Multiple values only supported for IN.");
				}
			}
			else {
				$sqlObject->sql = !is_null($this->key) && !is_null($this->operator) ? 
					$this->key . " " . $this->operator . " $" . $parameterCount : 
						(!is_null($this->value) ? $this->value : "");
				$sqlObject->v = !is_null($this->key) && !is_null($this->operator) && !is_null($this->value) ? 
					array($this->value) : array();
				$sqlObject->t = !is_null($this->key) && !is_null($this->operator) && !is_null($this->value) ? 
					array($this->getType($this->value)) : array();
			}
			return $sqlObject;			
		}
	}

	protected function isComplex () {
		if (in_array($this->booleanOperator, explode(",", Filter::BOOLEAN_OPERATORS)) &&
			is_array($this->filterArray)
		) {
			foreach ($this->filterArray as $filter) {
				if (!is_a($filter, "Filter")) {
					throw new Exception("Filter: Not a valid filter.");
					return false;
				}
			}
			return true;
		}
		return false;
	}
	
	private function getType ($v) {
		if (is_float($v) || is_integer($v)) {
			return "i";
		}
		return "s";
	}
	
}
?>