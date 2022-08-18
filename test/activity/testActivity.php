<?php

declare (strict_types=1);

require_once __DIR__ . '/../../src/tidapp/activity/activities.php';

testGetActivities();
testGetActivity();

function testGetActivities(): void {
    $db = makeTestDb();
    $test = json_encode(json_decode(getActivities($db)));
    $expect = json_encode($db);
    if ($test === $expect) {
        echo "GetActivities OK \n";
    } else {
        echo "GetActivities misslyckades, förväntade: \n\t $expect \nfick \n\t $test\n";
    }
}

function testGetActivity():void {
    $db=makeTestDb();
    $test= json_decode(getActivity($db, 1));
    $expect=new stdClass();
    $expect->id=1;
    $expect->activity="Slappat";
    if(($test->id===$expect->id) && ($test->activity===$expect->activity)) {
        echo "GetActivity OK \n";
    } else {
        echo "GetActivity misslyckades förväntade \n\t" .json_encode($expect) . "\nfick \n\t" .json_encode($test) . "\n";
    }
            
    $test= json_decode(getActivity($db, 2));
    if(isset($test->error) && is_array($test->error)) {
        echo "GetActivity OK \n";
    } else {
        echo "GetActivity misslyckades förväntade array fick \n\t" .json_encode($test) . "\n";
    }
            
}

function makeTestDB(): stdClass {
    $actData = file_get_contents(__DIR__ . "/../functions/activities.json");
    $db = new stdClass();
    $db->activities = json_decode($actData);
    return $db;
}
