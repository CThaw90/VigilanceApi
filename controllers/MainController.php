<?php

class MainController {
	
	private $BASE_API_URL_ONLY_REGEX = '/^\/vigilance\/api\/$/';

	private $ALL_USERS_RESOURCE_REGEX = '/^\/vigilance\/api\/users$/';
	private $USER_RESOURCE_REGEX = '/^\/vigilance\/api\/user\/\d{1,}.*/';

	private $ALL_POSTS_RESOURCE_REGEX = '/^\/vigilance\/api\/posts$/';
	private $POST_RESOURCE_REGEX = '/^\/vigilance\/api\/post\/\d{1,}.*/';

	public function execute() {

		$controller = null;
		$return = null;
		if (!isset($_SERVER['REDIRECT_URL'])) {
			return '{"error": "No resource requested"}';
		}
		else if (preg_match($this->BASE_API_URL_ONLY_REGEX, $_SERVER['REDIRECT_URL'])){
			return '{"success": 200}';
		}
		else if (preg_match($this->ALL_USERS_RESOURCE_REGEX, $_SERVER['REDIRECT_URL'])) {
			return '{"users": []}';
		}
		else if (preg_match($this->USER_RESOURCE_REGEX, $_SERVER['REDIRECT_URL'])) {
			return '{"user": {}}';
		}
		else if (preg_match($this->ALL_POSTS_RESOURCE_REGEX, $_SERVER['REDIRECT_URL'])) {
			$controller = new PostController();
			#return '{"posts": []}';
		}
		else if (preg_match($this->POST_RESOURCE_REGEX, $_SERVER['REDIRECT_URL'])) {
			$controller = new PostController();
			#return '{"post": {}}';
		}
		else {
			return '{"error": "Resource not found"}';
		}

		switch ($_SERVER['REQUEST_METHOD']) {
			
			case 'GET':
				preg_match('/\d{1,}/', $_SERVER['REDIRECT_URL'], $matched);
				$return = $controller->get($matched[0]);
				break;
		}

		return $return;
	}
}