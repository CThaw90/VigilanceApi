<?php

# Authentication
include_once 'auth/Authentication.php';

# PHP Controllers
include_once 'controllers/OrganizationController.php';
include_once 'controllers/TopFiveController.php';
include_once 'controllers/OptionsController.php';
include_once 'controllers/CommentController.php';
include_once 'controllers/SchoolController.php';
include_once 'controllers/CourseController.php';
include_once 'controllers/ImageController.php';
include_once 'controllers/LogoutController.php';
include_once 'controllers/LoginController.php';
include_once 'controllers/PostController.php';
include_once 'controllers/UserController.php';
include_once 'controllers/MainController.php';

# PHP Entities
include_once 'entity/Entity.php';
include_once 'entity/Organization.php';
include_once 'entity/TopFive.php';
include_once 'entity/Option.php';
include_once 'entity/Comment.php';
include_once 'entity/Course.php';
include_once 'entity/School.php';
include_once 'entity/Image.php';
include_once 'entity/Post.php';
include_once 'entity/User.php';
include_once 'entity/Logout.php';
include_once 'entity/Login.php';

# PHP Database Connectors
include_once 'utils/DbConn.php';

# PHP Utilities
include_once 'utils/Debugger.php';
include_once 'utils/FileManager.php';

# PHP Static Properties
include_once '.ignore/Properties.php';