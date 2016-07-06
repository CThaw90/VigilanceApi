<?php

class OptionController {
	
	private $option;
	public function __construct () {
		$this->option = new Option();
	}

	public function options() {
		return $this->option->generate();
	}
}