<?php

class Debugger {
	
	public function log ($message, $debug_level) {
		if (isset($_GET['debug']) && $_GET['debug'] >= intval($debug_level)) {
			print $message;
		}
	}
}