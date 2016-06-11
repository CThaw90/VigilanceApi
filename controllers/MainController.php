<?php

class MainController {
	
	private $BASE_API_URL_ONLY_REGEX = '/^\/vigilance\/api\/ping$/';
	private $BASE_API_LOGIN_REGEX = '/^\/vigilance\/api\/login($|\?.*)/';
	private $BASE_API_LOGOUT_REGEX = '/^\/vigilance\/api\/logout($|\?.*)/';

	private $ALL_USERS_RESOURCE_REGEX = '/^\/vigilance\/api\/users($|\?.*)/';
	private $USER_RESOURCE_REGEX = '/^\/vigilance\/api\/user\/\d{1,}.*/';
	private $USER_OBJECT_REGEX = '/^\/vigilance\/api\/user($|\?.*)/';

	private $ALL_POSTS_RESOURCE_REGEX = '/^\/vigilance\/api\/posts($|\?.*)/';
	private $POST_RESOURCE_REGEX = '/^\/vigilance\/api\/post\/\d{1,}.*/';
	private $POST_OBJECT_REGEX = '/^\/vigilance\/api\/post($|\?.*)/';

	private $ALL_SCHOOLS_RESOURCE_REGEX = '/^\/vigilance\/api\/schools($|\?.*)/';
	private $SCHOOL_RESOURCE_REGEX = '/^\/vigilance\/api\/school\/\d{1,}.*/';
	private $SCHOOL_OBJECT_REGEX = '/^\/vigilance\/api\/school($|\?.*)/';

	private $ALL_ORGANIZATIONS_RESOURCE_REGEX = '/^\/vigilance\/api\/organizations($|\?.*)/';
	private $ORGANIZATION_RESOURCE_REGEX = '/^\/vigilance\/api\/organization\/\d{1,}.*/';
	private $ORGANIZATION_OBJECT_REGEX = '/^\/vigilance\/api\/organization($|\?.*)/';

	private $ALL_COMMENTS_RESOURCE_REGEX = '/^\/vigilance\/api\/comments($|\?.*)/';
	private $COMMENTS_RESOURCE_REGEX = '/^\/vigilance\/api\/comment\/\d{1,}.*/';
	private $COMMENT_OBJECT_REGEX = '/^\/vigilance\/api\/comment($|\?.*)/';

	private $ALL_COURSES_RESOURCE_REGEX = '/^\/vigilance\/api\/courses($|\?.*)/';
	private $COURSES_RESOURCE_REGEX = '/^\/vigilance\/api\/course\/\d{1,}.*/';
	private $COURSE_OBJECT_REGEX = '/^\/vigilance\/api\/course($|\?.*)/';

    private $ALL_TOPFIVE_RESOURCE_REGEX = '/^\/vigilance\/api\/topfives($|\?.*)/';
    private $TOPFIVE_RESOURCE_REGEX = '/^\/vigilance\/api\/topfive\/\d{1,}.*/';
    private $TOPFIVE_OBJECT_REGEX = '/^\/vigilance\/api\/topfive($|\?.*)/';

    private $debug;

    public function __construct () {
    	$this->debug = new Debugger("MainController.php");
    }

	public function execute() {

		$this->debug->log("[INFO] Entering Main Controller execution", 5);
		$this->debug->log("[INFO] Invoked with HTTP REQUEST_METHOD " . $_SERVER['REQUEST_METHOD'], 5);
		$return = '{"status": 404, "error": "Resource not found"}';
		$controller = null;
		if (!isset($_SERVER['REDIRECT_URL'])) {
			$this->debug->log("[FATAL] Server did not redirect url. Check .htaccess configurations", 1);
			return '{"error": "No resource requested"}';
		}
		else if (preg_match($this->BASE_API_URL_ONLY_REGEX, $_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
			$this->debug->log("[INFO] Hit heart beat server api url check", 4);
			return '{"success": 200, "message": "Vigilance Api is up and running"}';
		}
		else if (preg_match($this->BASE_API_LOGIN_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new LoginController();
		}
		else if (preg_match($this->BASE_API_LOGOUT_REGEX, $_SERVER['REQUEST_URI'])) {
			$controller = new LogoutController();
			if ($_SERVER['REQUEST_METHOD'] === 'DELETE') { // Skip normal delete logic path
				return $controller->delete();
			}
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
        else if (preg_match($this->ALL_TOPFIVE_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
            $controller = new TopFiveController();
            return $controller->all();
        }
        else if (preg_match($this->TOPFIVE_RESOURCE_REGEX, $_SERVER['REQUEST_URI'])) {
            $controller = new TopFiveController();
        }
        else if (preg_match($this->TOPFIVE_OBJECT_REGEX, $_SERVER['REQUEST_URI'])) {
            $controller = new TopFiveController();
        }
		else {
			return $return;
		}

		switch ($_SERVER['REQUEST_METHOD']) {
			
			case 'GET':
				preg_match("/\d{1,}/", $_SERVER['REQUEST_URI'], $matched);

				if (count($matched))
					$return = $controller->get($matched[0]);
				break;

			case 'POST':
				$return = $controller->post(file_get_contents('php://input'));
				break;

			case 'PUT':
				$return = $controller->put(file_get_contents('php://input'));
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