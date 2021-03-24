testCases = [
    // Hämta aktivitet OK!
    {name: 'Hämta enskild aktivitet ok',
        url: 'getActivity.php?id=10',
        method: 'GET',
        status: 200,
        result: {id: 10, activity: '?'}
    },
    // Hämta aktivitet, fel!
    {name: 'Hämta enskild aktivitet ok',
        url: 'getActivity.php?id=10',
        method: 'POST',
        status: 405,
        result: {error: '?'}
    },
    {name: 'Hämta enskild aktivitet (id finns inte)',
        url: 'getActivity.php?id=110',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta enskild aktivitet (ogiltigt id)',
        url: 'getActivity.php?id=fel',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta enskild aktivitet (ogiltigt id)',
        url: 'getActivity.php?id=3sju',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta enskild aktivitet (ogiltigt id)',
        url: 'getActivity.php?id=-12',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    // Ny aktivitet OK
    {name: 'Spara ny aktivitet (OK)',
        url: 'saveActivity.php',
        method: 'POST',
        data: fdNewActivity(),
        status: 200,
        result: {id: '?', message: '?'}
    },
    // Ny aktivitet, fel!
    {name: 'Spara ny aktivitet (Finns)',
        url: 'saveActivity.php',
        method: 'POST',
        data: fdActivityExists(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny aktivitet (Fel metod)',
        url: 'saveActivity.php',
        method: 'GET',
        status: 405,
        result: {error: '?'}
    },
    {name: 'Spara ny aktivitet (aktivity saknas)',
        url: 'saveActivity.php',
        method: 'POST',
        status: 400,
        result: {error: '?'}
    },
    // Uppdatera aktivitet, OK
    {name: 'Uppdatera aktivitet (ok)',
        url: 'saveActivity.php?id=1',
        method: 'POST',
        data: fdNewActivity(),
        status: 200,
        result: {result: true, message: '?'}
    },
    {name: 'Uppdatera aktivitet (ingen förändring)',
        url: 'saveActivity.php?id=1',
        method: 'POST',
        data: fdActivity(),
        status: 200,
        result: {result: false, message: '?'}
    },
    // Uppdatera aktivitet, fel!
    {name: 'Uppdatera aktivitet (fel metod)',
        url: 'saveActivity.php?id=2',
        method: 'GET',
        status: 405,
        result: {error: '?'}
    },
    {name: 'Uppdatera aktivitet ("activity" saknas)',
        url: 'saveActivity.php?id=-2',
        method: 'POST',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Uppdatera aktivitet (ogiltigt id)',
        url: 'saveActivity.php?id=-2',
        method: 'POST',
        data: fdActivity(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Uppdatera aktivitet (ogiltigt id)',
        url: 'saveActivity.php?id=två',
        method: 'POST',
        data: fdActivity(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Uppdatera aktivitet (finns för annat id)',
        url: 'saveActivity.php?id=2',
        method: 'POST',
        data: fdActivity(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Uppdatera aktivitet ("id" finns inte)',
        url: 'saveActivity.php?id=200',
        method: 'POST',
        data: fdActivity(),
        status: 400,
        result: {error: '?'}
    },
    // Radera aktivitet, OK
    {name: 'Radera aktivitet ok',
        url: 'deleteActivity.php',
        method: 'POST',
        data: fdActivity(),
        status: 200,
        result: {result: true, message: '?'}
    },
    {name: 'Radera aktivitet (id finns inte)',
        url: 'deleteActivity.php',
        method: 'POST',
        data: fdActivityDontExists(),
        status: 200,
        result: {result: false, message: '?'}
    },
    // Radera aktivitet, fel!
    {name: 'Radera aktivitet (fel metod)',
        url: 'deleteActivity.php',
        method: 'GET',
        status: 405,
        result: {error: '?'}
    },
    {name: 'Radera aktivitet (id saknas)',
        url: 'deleteActivity.php',
        method: 'POST',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Radera aktivitet (Ogiltigt id)',
        url: 'deleteActivity.php',
        method: 'POST',
        data: fdActivityBadId1(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Radera aktivitet (Ogiltigt id)',
        url: 'deleteActivity.php',
        method: 'POST',
        data: fdActivityBadId2(),
        status: 400,
        result: {error: '?'}
    },
    // Hämta aktiviteter
    {name: 'Hämta aktiveteslista',
        url: 'getActivityList.php',
        method: 'GET',
        status: 200,
        result: {activities: [{id: '?', activity: '?'}]}
    },
    {name: 'Hämta aktiveteslista',
        url: 'getActivityList.php',
        method: 'POST',
        data: fdActivity(),
        status: 405,
        result: {error: '?'}
    },

    // Tasks!

    // Hämta uppgift OK!
    {name: 'Hämta enskild uppgift ok',
        url: 'getTask.php?id=10',
        method: 'GET',
        status: 200,
        result: {id: 10, activity: '?', date: '?', time: '?', activityId: '?', description: '?'}
    },
    // Hämta uppgift, fel!
    {name: 'Hämta enskild aktivitet ok',
        url: 'getTask.php?id=10',
        method: 'POST',
        status: 405,
        result: {error: '?'}
    },
    {name: 'Hämta enskild aktivitet (id finns inte)',
        url: 'getTask.php?id=110',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta enskild aktivitet (ogiltigt id)',
        url: 'getTask.php?id=fel',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta enskild aktivitet (ogiltigt id)',
        url: 'getTask.php?id=3sju',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta enskild aktivitet (ogiltigt id)',
        url: 'getTask.php?id=-12',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    // Hämta lista
    {name: 'Hämta uppgiftslista ok',
        url: 'getTaskList.php?page=3',
        method: 'GET',
        status: 200,
        result: {pages: '?', tasks: [{id: '?', activity: '?', date: '?', time: '?', activityId: '?', description: '?'}]}
    },
    {name: 'Hämta uppgiftslista (sidan finns inte)',
        url: 'getTaskList.php?page=110',
        method: 'GET',
        status: 200,
        result: {pages: '?', message: '?'}
    },
    {name: 'Hämta uppgiftslista ok',
        url: 'getTaskList.php?from=2020-02-02&to=2020-03-03',
        method: 'GET',
        status: 200,
        result: {tasks: [{id: '?', activity: '?', date: '?', time: '?', activityId: '?', description: '?'}]}
    },
    {name: 'Hämta uppgiftslista (inga poster)',
        url: 'getTaskList.php?from=2020-02-02&to=2020-02-02',
        method: 'GET',
        status: 200,
        result: {message: '?'}
    },
    // Hämta lista, fel!
    {name: 'Hämta uppgiftslista (parametrar saknas)',
        url: 'getTaskList.php',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista',
        url: 'getTaskList.php',
        method: 'POST',
        data: fdTask(),
        status: 405,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (fel indata)',
        url: 'getTaskList.php?page=-1',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (fel indata)',
        url: 'getTaskList.php?page=tre',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (to saknas)',
        url: 'getTaskList.php?from=2020-02-02',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (to ogiltigt datum)',
        url: 'getTaskList.php?from=2020-02-02&to=2020-02-31',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (to ogiltigt datum)',
        url: 'getTaskList.php?from=2020-02-02&to=idag',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (from saknas)',
        url: 'getTaskList.php?to=2020-02-02',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (from ogiltigt datum)',
        url: 'getTaskList.php?to=2020-02-02&from=2020-02-31',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (from ogiltigt datum)',
        url: 'getTaskList.php?to=2020-02-02&from=idag',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (to efter from)',
        url: 'getTaskList.php?from=2020-04-02&to=2020-03-03',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (OK)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask(),
        status: 200,
        result: {id: '?', message: '?'}
    },
    {name: 'Spara ny uppgift (No description)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_NoDescription(),
        status: 200,
        result: {id: '?', message: '?'}
    },
    {name: 'Spara ny uppgift (Fel metod)',
        url: 'saveTask.php',
        method: 'GET',
        status: 405,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (post saknas)',
        url: 'saveTask.php',
        method: 'POST',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (date saknas)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_NoDate(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (date felaktigt)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadDate1(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (date felaktigt)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadDate2(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (time saknas)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_NoTime(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (time felaktig)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadTime1(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (time felaktig)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadTime2(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (time felaktig)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadTime3(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (time felaktig)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadTime4(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (felaktig activityId)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadActivity1(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (felaktigt activityId)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadActivity2(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Spara ny uppgift (angivet activityId saknas)',
        url: 'saveTask.php',
        method: 'POST',
        data: fdNewTask_BadActivity3(),
        status: 400,
        result: {error: '?'}
    },
    // Uppdatera uppgift
    {name: 'Uppdatera uppgift (ok)',
        url: 'saveTask.php?id=10',
        method: 'POST',
        data: fdTask(),
        status: 200,
        result: {result: true, message: '?'}
    },
    {name: 'Uppdatera uppgift (ok)',
        url: 'saveTask.php?id=60',
        method: 'POST',
        data: fdTask(),
        status: 200,
        result: {result: false, message: '?'}
    },
    // Radera uppgift
    {name: 'Radera uppgift (ok)',
        url: 'deleteTask.php',
        method: 'POST',
        data: fdTask(),
        status: 200,
        result: {result: true, message: '?'}
    },
    // Radera uppgift, fel!
    {name: 'Radera uppgift (fel metod)',
        url: 'deleteTask.php',
        method: 'GET',
        status: 405,
        result: {error: '?'}
    },
    {name: 'Radera uppgift (post-data saknas)',
        url: 'deleteTask.php',
        method: 'POST',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Radera uppgift (ogiltigt id)',
        url: 'deleteTask.php',
        method: 'POST',
        data: fdDeleteTask_BadId1(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Radera uppgift (ogiltigt id)',
        url: 'deleteTask.php',
        method: 'POST',
        data: fdDeleteTask_BadId2(),
        status: 400,
        result: {error: '?'}
    },
    {name: 'Radera uppgift (ogiltigt id)',
        url: 'deleteTask.php',
        method: 'POST',
        data: fdDeleteTask_BadId3(),
        status: 200,
        result: {result: false, message: '?'}
    },
    
    // Hämta sammanställning
        {name: 'Hämta uppgiftslista ok',
        url: 'getCompilation.php?from=2020-02-02&to=2020-03-03',
        method: 'GET',
        status: 200,
        result: {tasks: [{activity: '?', time: '?', activityId: '?'}]}
    },
    {name: 'Hämta uppgiftslista (inga poster)',
        url: 'getCompilation.php?from=2020-02-02&to=2020-02-02',
        method: 'GET',
        status: 200,
        result: {message: '?'}
    },
{name: 'Hämta uppgiftslista (to saknas)',
        url: 'getCompilation.php?from=2020-02-02',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (to ogiltigt datum)',
        url: 'getCompilation.php?from=2020-02-02&to=2020-02-31',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (to ogiltigt datum)',
        url: 'getCompilation.php?from=2020-02-02&to=idag',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (from saknas)',
        url: 'getCompilation.php?to=2020-02-02',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (from ogiltigt datum)',
        url: 'getCompilation.php?to=2020-04-02&from=2020-02-31',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (from ogiltigt datum)',
        url: 'getCompilation.php?to=2020-02-02&from=idag',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
    {name: 'Hämta uppgiftslista (to efter from)',
        url: 'getCompilation.php?from=2020-04-02&to=2020-03-03',
        method: 'GET',
        status: 400,
        result: {error: '?'}
    },
];

function fdActivity() {
    let fd = new FormData();
    fd.append('id', 1);
    fd.append('activity', 'Kodat frontend');
    return fd;
}
;
function fdNewActivity() {
    let fd = new FormData();
    fd.append('activity', 'test');
    return fd;
}
;
function fdActivityExists() {
    let fd = new FormData();
    fd.append('id', 0);
    fd.append('activity', 'Slappat');
    return fd;
}
function fdActivityDontExists() {
    let fd = new FormData();
    fd.append('id', 100);
    fd.append('activity', 'Slappat');
    return fd;
}
function fdActivityBadId1() {
    let fd = new FormData();
    fd.append('id', -1);
    fd.append('activity', 'Slappat');
    return fd;
}
function fdActivityBadId2() {
    let fd = new FormData();
    fd.append('id', 'två');
    fd.append('activity', 'Slappat');
    return fd;
}
function fdTask() {
    let fd = new FormData();
    fd.append('id', 10);
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "3:15");
    fd.append('description', 'testCase');
    return fd;
}
function fdNewTask() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "3:15");
    fd.append('description', 'testCase');
    return fd;
}
function fdNewTask_NoDescription() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "3:15");
    return fd;
}
function fdNewTask_NoDate() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('time', "3:15");
    fd.append('description', 'testCase');
    return fd;
}

function fdNewTask_BadDate1() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString());
    fd.append('time', "3:15");
    fd.append('description', 'testCase');
    return fd;
}

function fdNewTask_BadDate2() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', '2021-02-31');
    fd.append('time', "3:15");
    fd.append('description', 'testCase');
    return fd;
}

function fdNewTask_NoTime() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('description', 'testCase');
    return fd;
}

function fdNewTask_BadTime1() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "33:15");
    fd.append('description', 'testCase');
    return fd;
}

function fdNewTask_BadTime2() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "3:69");
    fd.append('description', 'testCase');
    return fd;
}

function fdNewTask_BadTime3() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "BadTime");
    fd.append('description', 'testCase');
    return fd;
}
function fdNewTask_BadTime4() {
    let fd = new FormData();
    fd.append('activityId', 1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "BadTime");
    fd.append('description', 'testCase');
    return fd;
}
function fdNewTask_BadActivity1() {
    let fd = new FormData();
    fd.append('activityId', 'tre');
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "BadTime");
    fd.append('description', 'testCase');
    return fd;
}
function fdNewTask_BadActivity2() {
    let fd = new FormData();
    fd.append('activityId', -1);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "BadTime");
    fd.append('description', 'testCase');
    return fd;
}
function fdNewTask_BadActivity3() {
    let fd = new FormData();
    fd.append('activityId', 110);
    fd.append('date', new Date().toISOString().substr(0,10));
    fd.append('time', "BadTime");
    fd.append('description', 'testCase');
    return fd;
}
function fdDeleteTask_BadId1() {
    let fd = new FormData();
    fd.append('id', -11);
    return fd;
}
function fdDeleteTask_BadId2() {
    let fd = new FormData();
    fd.append('id', 'tre');
    return fd;
}
function fdDeleteTask_BadId3() {
    let fd = new FormData();
    fd.append('id', 110);
    return fd;
}
//function fdNewTask_NoDate() {
//    let fd = new FormData();
//    fd.append('activityId', 1);
//    fd.append('date', new Date().toDateString());
//    fd.append('time', "3:15");
//    fd.append('description', 'testCase');
//    return fd;
//}
//
