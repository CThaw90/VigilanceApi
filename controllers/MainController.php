<?php

class MainController {
	
	private $BASE_API_URL_ONLY_REGEX = '/^\/vigilance\/api\/$/';

	private $ALL_USERS_RESOURCE_REGEX = '/^\/vigilance\/api\/users$/';
	private $USER_RESOURCE_REGEX = '/^\/vigilance\/api\/user\/\d{1,}.*/';
	private $USER_OBJECT_REGEX = '/^\/vigilance\/api\/user$/';

	private $ALL_POSTS_RESOURCE_REGEX = '/^\/vigilance\/api\/posts$/';
	private $POST_RESOURCE_REGEX = '/^\/vigilance\/api\/post\/\d{1,}.*/';
	private $POST_OBJECT_REGEX = '/^\/vigilance\/api\/post$/';

	private $ALL_SCHOOLS_RESOURCE_REGEX = '/^\/vigilance\/api\/schools$/';
	private $SCHOOL_RESOURCE_REGEX = '/^\/vigilance\/api\/school\/\d{1,}.*/';
	private $SCHOOL_OBJECT_REGEX = '/^\/vigilance\/api\/school$/';

	private $ALL_ORGANIZATIONS_RESOURCE_REGEX = '/^\/vigilance\/api\/organizations$/';
	private $ORGANIZATION_RESOURCE_REGEX = '/^\/vigilance\/api\/organization\/\d{1,}.*/';
	private $ORGANIZATION_OBJECT_REGEX = '/^\/vigilance\/api\/organization$/';

	private $ALL_COMMENTS_RESOURCE_REGEX = '/^\/vigilance\/api\/comments$/';
	private $COMMENTS_RESOURCE_REGEX = '/^\/vigilance\/api\/comment\/\d{1,}.*/';
	private $COMMENT_OBJECT_REGEX = '/^\/vigilance\/api\/comment$/';

	private $ALL_COURSES_RESOURCE_REGEX = '/^\/vigilance\/api\/courses$/';
	private $COURSES_RESOURCE_REGEX = '/^\/vigilance\/api\/course\/\d{1,}.*/';
	private $COURSE_OBJECT_REGEX = '/^\/vigilance\/api\/course$/';

	public function execute() {

		$controller = null;
		$return = null;
		if (!isset($_SERVER['REDIRECT_URL'])) {
			return '{"error": "No resource requested"}';
		}
		else if (preg_match($this->BASE_API_URL_ONLY_REGEX, $_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
			return '{"success": 200}';
		}
		else if (preg_match($this->ALL_USERS_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new UserController();
			return $controller->all();
		}
		else if (preg_match($this->USER_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new UserController();
		}
		else if (preg_match($this->USER_OBJECT_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new UserController();
		}
		else if (preg_match($this->ALL_POSTS_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new PostController();
			return $controller->all();
		}
		else if (preg_match($this->POST_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new PostController();
		}
		else if (preg_match($this->POST_OBJECT_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new PostController();
		}
		else if (preg_match($this->ALL_SCHOOLS_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new SchoolController();
			return $controller->all();
		}
		else if (preg_match($this->SCHOOL_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new SchoolController();
		}
		else if (preg_match($this->SCHOOL_OBJECT_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new SchoolController();
		}
		else if (preg_match($this->ALL_ORGANIZATIONS_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new OrganizationController();
			return $controller->all();
		}
		else if (preg_match($this->ORGANIZATION_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new OrganizationController();
		}
		else if (preg_match($this->ORGANIZATION_OBJECT_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new OrganizationController();
		}
		else if (preg_match($this->ALL_COURSES_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new CourseController();
			return $controller->all();
		}
		else if (preg_match($this->COURSES_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new CourseController();
		}
		else if (preg_match($this->COURSE_OBJECT_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new CourseController();
		}
		else if (preg_match($this->ALL_COMMENTS_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new CommentController();
			return $controller->all();
		}
		else if (preg_match($this->COMMENTS_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new CommentController();
		}
		else if (preg_match($this->COMMENT_OBJECT_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new CommentController();
		}
		else {
			return '{"error": "Resource not found"}';
		}

		switch ($_SERVER['REQUEST_METHOD']) {
			
			case 'GET':
				preg_match("/\d{1,}/", $_SERVER['REQUEST_URI'], $matched);
				$return = $controller->get($matched[0]);
				break;

			case 'POST':
				$return = $controller->post(file_get_contents('php://input'));
				break;

			case 'PUT':
				$return = "{'status': 'You hit the APIs put method logic branch'}";
				break;

			case 'DELETE':
				preg_match("/\d{1,}/", $_SERVER['REQUEST_URI'], $matched);
				$return = $controller->delete($matched[0]);
				break;

			default:
				break;
		}

		return $return;
	}
}