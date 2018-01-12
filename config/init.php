<?php

require('./config/database.php');
require('./lib/system/core/Controller.php');
require('./lib/function.php');

define('ROOT', dirname(dirname(__file__)));
define('VIEW_PATH', ROOT . '/app/view/');
define('ASSETS_IMAGE_PATH', 'assets/image/');
define('UPLOADS_IMAGE_PATH', 'uploads/images/');
