<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . "/../../../lib/class_Filter.php";

class FilterTest extends PHPUnit_Framework_TestCase {

	public function testConstructorEmpty () {
		$filter = new Filter();
		
		$sqlObject = new stdClass();
		$sqlObject->sql = "";
		$sqlObject->v = array();
		$sqlObject->t = array();
		$this->assertEquals($sqlObject, $filter->toSql());
	}

	public function testOperatorLike () {
		$filter = new Filter("LIKE", "a", "%a");
		
		$sqlObject = new stdClass();
		$sqlObject->sql = "a LIKE $1";
		$sqlObject->v = array("%a");
		$sqlObject->t = array("s");
		$this->assertEquals($sqlObject, $filter->toSql());
	}

	public function testConstructorOperatorKeyValue () {
		$filter = new Filter("=", "a", "b");
		$sqlObject = new stdClass();
		$sqlObject->sql = "a = $1";
		$sqlObject->v = array("b");
		$sqlObject->t = array("s");
		$this->assertEquals($sqlObject, $filter->toSql());
	}

	public function testConstructorOperatorKeyValueArray () {
		$filter = new Filter("IN", "a", array("a", "b", "c"));
		$sqlObject = new stdClass();
		$sqlObject->sql = "a IN ($1,$2,$3)";
		$sqlObject->v = array("a", "b", "c");
		$sqlObject->t = array("s", "s", "s");
		$this->assertEquals($sqlObject, $filter->toSql());
	}

	public function testConstructorOperatorFilterArray () {
		$filterA = new Filter("=", "a", "b");
		$filterB = new Filter(">=", "b", 3);

		$filter = new Filter("AND", array($filterA, $filterB));
		$sqlObject = new stdClass();
		$sqlObject->sql = "(a = $1 AND b >= $2)";
		$sqlObject->v = array("b", 3);
		$sqlObject->t = array("s", "i");
		$this->assertEquals($sqlObject, $filter->toSql());
	}
	
	public function testConstructorRecursive() {
		$filterA1 = new Filter("=", "a", "b");
		$filterB1 = new Filter(">=", "b", 3);
		$filter1all = new Filter("AND",array($filterA1,$filterB1));
		
		$filterA2 = new Filter("=", "c", "d");
		$filterB2 = new Filter(">=", "e", 4);
		$filter2all = new Filter("AND",array($filterA2,$filterB2));

		$filter = new Filter("AND", array($filter1all, $filter2all));
		$sqlObject = new stdClass();
		$sqlObject->sql = "((a = $1 AND b >= $2) AND (c = $3 AND e >= $4))";
		$sqlObject->v = array("b", 3, "d", 4);
		$sqlObject->t = array("s", "i","s", "i");
		$this->assertEquals($sqlObject, $filter->toSql());
	}
	
	public function testConstructorOneFilter () {
		$filterA = new Filter("=", "a", "b");
		$filterB = new Filter("AND", array($filterA));

		$sqlObject = new stdClass();
		$sqlObject->sql = "(a = $1)";
		$sqlObject->v = array("b");
		$sqlObject->t = array("s");
		
		$this->assertEquals($sqlObject, $filterB->toSql());
	}

	public function testConstructorTwoFiltersOneEmpty () {
		$filterA = new Filter("=", "a", "b");
		$filterB = new Filter();
		$filterC = new Filter("AND", array($filterA, $filterB));

		$sqlObject = new stdClass();
		$sqlObject->sql = "(a = $1)";
		$sqlObject->v = array("b");
		$sqlObject->t = array("s");
		
		$this->assertEquals($sqlObject, $filterC->toSql());
	}

	public function testConstructorTwoFiltersOneEmptyReordered () {
		$filterA = new Filter("AND", array(new Filter()));
		$filterB = new Filter("=", "a", "b");
		$filterC = new Filter("AND", array($filterA, $filterB));

		$sqlObject = new stdClass();
		$sqlObject->sql = "(a = $1)";
		$sqlObject->v = array("b");
		$sqlObject->t = array("s");
		
		$this->assertEquals($sqlObject, $filterC->toSql());
	}

	public function testContructorFalse () {
		$filterA = new Filter("FALSE");

		$sqlObject = new stdClass();
		$sqlObject->sql = "FALSE";
		$sqlObject->v = array();
		$sqlObject->t = array();
		
		$this->assertEquals($sqlObject, $filterA->toSql());
	}

	public function testContructorFalseMultiple () {
		$filterA = new Filter("FALSE");
		$filterB = new Filter("=", "b", 6);
		$filterAnd = new Filter("AND", array($filterA, $filterB));
		
		$sqlObject = new stdClass();
		$sqlObject->sql = "(FALSE AND b = $1)";
		$sqlObject->v = array(6);
		$sqlObject->t = array("i");
		
		$this->assertEquals($sqlObject, $filterAnd->toSql());
	}
}
?>