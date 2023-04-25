<?php

require_once __DIR__ . './includes/init.php';
require_once __DIR__ . './includes/classes.php';
require_once __DIR__ . './includes/database.php';
require_once __DIR__ . './includes/requests.php';

$db = get_database();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'PUT':
        check_login();

        check_url();

        

        exit;

    case 'GET':
        exit;

    case 'PATCH':
        check_login();

        // The "user" URL param is required.
        if (!isset($_GET['user'])) {
            http_response_code(BAD_REQUEST);
            exit;
        }

        // The "user" URL param must be equal to the currently logged-in user.
        if ($_GET['user'] != $_SESSION['user']) {
            http_response_code(FORBIDDEN);
            exit;
        }
        exit;

    case 'DELETE':
        check_login();
        exit;
}
