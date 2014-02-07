<?php

namespace Skip\Test\Helper;

class GenericTestClass {

	public $deps = array();
	public $params = array();

	public function __construct($dep1, $dep2) {
		$this->deps[] = $dep1;
		$this->deps[] = $dep2;
	}

	public function setParamA($value) {
		$this->params[] = $value;
	}

	public function setParamB($value) {
		$this->params[] = $value;	
	}
}