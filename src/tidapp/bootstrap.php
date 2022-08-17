<?php

declare (strict_types=1);

define('ROOT_DIR', __DIR__ . "/");
/* $serverPath=substr($_SERVER['SCRIPT_NAME'],0,-9);
  define('SERVER_PATH', substr($serverPath,0,-1));

 */
require_once ROOT_DIR . 'routing/routing.php';

$querystring = filter_var($_SERVER['REQUEST_URI'], FILTER_UNSAFE_RAW);
$route = getRoute($querystring);

switch ($route->route) {
    case '/activity/':
        switch ($route->method) {
            case REQUEST_GET:
                if (count($route->params) === 1) {
                    echo "Hämta aktivitetsnr:" . $route->params[0];
                } else {
                    echo "Hämta alla aktiviteter";
                }
                break;
            case REQUEST_POST:
                echo "Spara NY aktivitet";
                break;
            case REQUEST_PUT:
                echo "Uppdatera aktivitetnr:" . $route->params[0];
                break;
            case REQUEST_DELETE:
                echo "Radera aktivitetnr:" . $route->params[0];
                break;
        }
        break;
    case "/tasklist/":
        if (count($route->params) === 1) {
            echo "Hämta uppgifter för sida:" . $route->params[0];
        } else {
            echo "Hämta uppgifter mellan {$route->params[0]} och  {$route->params[1]}";
        }
        break;
    case "/task/":
        switch ($route->method) {
            case REQUEST_GET:
                echo "Hämta uppgiftnr:" . $route->params[0];
            case REQUEST_POST:
                echo "Spara NY aktivitet";
                break;
            case REQUEST_PUT:
                echo "Uppdatera aktivitetnr:" . $route->params[0];
                break;
            case REQUEST_DELETE:
                echo "Radera aktivitetnr:" . $route->params[0];
                break;
        }
        break;
    case "/compilation/":
        echo "Hämta sammanställning mellan {$route->params[0]} och  {$route->params[1]}";
        break;
    default:
        echo "Ogiltigt anrop";
}