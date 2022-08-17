<?php

declare (strict_types=1);
require_once __DIR__ . '/../../src/tidapp/routing/routing.php';

testRequestMethods();
testGetRoute();

function testRequestMethods(): void {
    if (REQUEST_GET !== 0) {
        echo "REQUEST_GET misslyckad. Förväntade 0 fick " . REQUEST_GET . "\n";
    } else {
        echo "REQUEST_GET OK \n";
    }
    if (REQUEST_POST !== 1) {
        echo "REQUEST_POST misslyckad. Förväntade 1 fick " . REQUEST_POST . "\n";
    } else {
        echo "REQUEST_POST OK \n";
    }
    if (REQUEST_PUT !== 2) {
        echo "REQUEST_PUT misslyckad. Förväntade 2 fick " . REQUEST_PUT . "\n";
    } else {
        echo "REQUEST_PUT OK \n";
    }
    if (REQUEST_DELETE !== 3) {
        echo "REQUEST_DELETE misslyckad. Förväntade 3 fick " . REQUEST_DELETE . "\n";
    } else {
        echo "REQUEST_DELETE OK \n";
    }
}

function testGetRoute(): void {
    // Test 1 - inga parametrar, ingen default metod
    $test = getRoute("/tidapp/activity");
    if ($test->method === REQUEST_GET) {
        echo "Default method==GET OK\n";
    } else {
        echo "Default method misslyckat. Förväntade " . REQUEST_GET . " fick " . $test->method . "\n";
    }
    if ($test->route === "/activity/") {
        echo "Route OK \n";
    } else {
        echo "Route misslyckades. Förväntade '/activity/' fick {$test->route} \n";
    }
    if (count($test->params) === 0) {
        echo "Params OK \n";
    } else {
        echo "Params misslyckades förväntade [] fick " . implode(", ", $test->params) . "\n";
    }

    // Test 2 - Default metod
    $test = getRoute("/tidapp/activity", "GET");
    if ($test->method === REQUEST_GET) {
        echo "Default method==GET OK\n";
    } else {
        echo "Default method misslyckat. Förväntade " . REQUEST_GET . " fick " . $test->method . "\n";
    }
    $test = getRoute("/tidapp/activity", "POST");
    if ($test->method === REQUEST_POST) {
        echo "Method==POST OK\n";
    } else {
        echo "Method misslyckat. Förväntade " . REQUEST_POST . " fick " . $test->method . "\n";
    }

    // Test 3 - med "action" i POST-datan
    $_POST["action"] = "delete";
    $test = getRoute("/tidapp/activity", "POST");
    if ($test->method === REQUEST_DELETE) {
        echo "Method==DELETE OK\n";
    } else {
        echo "Method misslyckat. Förväntade " . REQUEST_DELETE . " fick " . $test->method . "\n";
    }
    $_POST["action"] = "save";
    $test = getRoute("/tidapp/activity/1", "POST");
    if ($test->method === REQUEST_PUT) {
        echo "Method==PUT OK\n";
    } else {
        echo "Method misslyckat. Förväntade " . REQUEST_PUT . " fick " . $test->method . "\n";
    }

    // Test 4 - Med avslutande / i URI:n
    $test = getRoute("/tidapp/activity/", "GET");
    if ($test->route === "/activity/") {
        echo "Route OK \n";
    } else {
        echo "Route misslyckades. Förväntade '/activity/' fick {$test->route} \n";
    }
    if (count($test->params) === 0) {
        echo "Params OK \n";
    } else {
        echo "Params misslyckades förväntade [] fick " . implode(", ", $test->params) . "\n";
    }

    // Test 5 - Många params
    $test = getRoute("/tidapp/activity/1/2/3/4", "GET");
    if (count($test->params) === 4) {
        echo "Flera params OK \n";
    } else {
        echo "Flera params misslyckades förväntade 4 params fick " . count($test->params) . "\n";
    }

    // Test 6 - Ingen rutt (/)
    $test = getRoute("/tidapp", "GET");
    if ($test->route === "/") {
        echo "Ingen rutt lyckades";
    } else {
        echo "Ingen rutt misslyckades förväntade / fick {$test->route}\n";
    }
}
