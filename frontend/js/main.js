var serverPath = 'http://localhost/tidapp/dummy/';
var lastPage = 1;
window.onload = function () {

    let tabs = document.getElementsByClassName('tab');
    for (let t of tabs) {
        t.addEventListener('click', showTab);
    }

    document.getElementById('saveSettings').addEventListener('click', saveSettings);
    document.getElementById('compilation_fetch').addEventListener('click', fetchCompilation);
    document.getElementById('taskslist_fetch').addEventListener('click', fetchTasklistDates);
    document.getElementById('tasklist_from').addEventListener('focus', selectRadioDate);
    document.getElementById('tasklist_from').addEventListener('focus', selectRadioDate);
    document.getElementById('saveTask').addEventListener('click', saveTask);
    document.getElementById('ny_task').addEventListener('click', newTask);
    let btn = document.forms['settings']['settings_standardlista_tasks'];
    for (let b of btn) {
        b.addEventListener('click', toggleTasklistStandard);
    }
    document.getElementById('closeForm').addEventListener('click', () => {
        modal = document.getElementById("modal_form");
        modal.style.display = "none";
    });



    let flik = getCookie('startFlik', 'compilation');
    for (let t of tabs) {
        if (t.getAttribute('data-section') === flik) {
            t.click();
            break;
        }
    }

    // Fyll tabbar med standardvärden och innehåll
    getCompilation();
    fetchCompilation();
    getTasklist();
    getActivities();
    fetchActivities();
    getSettings();
};

function showTab(ev) {
    let tab, tabs, t, section;
    tab = ev.target;
    tabs = document.getElementsByClassName('tab');
    for (let t of tabs) {
        t.className = 'tab';
        section = document.getElementById(t.getAttribute('data-section'));
        section.className = 'hidden';
    }
    tab.className = 'tab active';
    section = document.getElementById(tab.getAttribute('data-section'));
    section.className = 'visible';
    if (getCookie('useSettings', false) == 'true') {
        switch (tab.getAttribute('data-section')) {
            case 'compilation':
                getCompilation();
                fetchCompilation();
            case 'tasklist':
                getTasklist();
                break;
            case 'activities':
                getActivities();
                fetchActivities()
                break;
            case 'settings':
                getSettings();
                break;
        }
    }
}

function saveSettings() {
    let frm = document.forms['settings'];

    let varde = frm['settings_useSettings'].checked;
    setCookie('useSettings', varde);

    varde = frm['settings_startflik'].value;
    setCookie('startFlik', varde);

    varde = frm['settings_intervall'].value;
    setCookie('intervallSammanställning', varde);

    varde = frm['settings_standardlista_tasks'].value;
    setCookie('tasklistStandard', varde);

    varde = frm['settings_tasklistIntervall'].value;
    setCookie('tasklistIntervall', varde);

    varde = frm['settings_tasklistStorlek'].value;
    setCookie('tasklistStorlek', varde);

    varde = frm['settings_standardtid'].value;
    setCookie('standardtid', varde);

    varde = frm['settings_maxlength'].value;
    setCookie('settings_maxlength', varde);

    setPagination();
}

function getSettings() {
    let frm = document.forms['settings'];

    let varde = getCookie('useSettings', false);
    frm['settings_useSettings'].checked = (varde == 'true');

    varde = getCookie('startFlik', 'compilation');
    for (let opt of frm['settings_startflik'].options) {
        opt.selected = (opt.value === varde);
    }

    varde = getCookie('intervallSammanställning', '0w');
    for (let opt of frm['settings_intervall'].options) {
        opt.selected = (opt.value === varde);
    }

    varde = getCookie('tasklistStandard', 'sida');
    for (let btn of frm['settings_standardlista_tasks']) {
        btn.checked = (btn.value === varde);
    }
    if (varde === 'sida') {
        frm['settings_tasklistStorlek'].disabled = false;
        frm['settings_tasklistIntervall'].disabled = true;
    } else {
        frm['settings_tasklistStorlek'].disabled = true;
        frm['settings_tasklistIntervall'].disabled = false;
    }

    varde = getCookie('tasklistIntervall', '3d');
    for (let opt of frm['settings_tasklistIntervall'].options) {
        opt.selected = (opt.value === varde);
    }

    varde = getCookie('tasklistStorlek', '20');
    for (let opt of frm['settings_tasklistStorlek'].options) {
        opt.selected = (opt.value === varde);
    }

    varde = getCookie('standardtid', '1:30');
    frm['settings_standardtid'].value = varde;

    varde = getCookie('settings_maxlength', '70');
    frm['settings_maxlength'].value = varde;

}

function getCompilation() {
    let from = new Date();
    let tom = new Date();
    let intervall = getCookie('intervallSammanställning', '0w');
    switch (intervall) {
        case '0m':
            from.setDate(1);
            break;
        case '1m':
            from.setMonth(from.getMonth() - 1);
            break;
        case '2w':
            from.setDate(from.getDate() - 13);
            break;
        case '0w':
            if (from.getDay() === 0) {
                from.setDate(from.getDate() - 6);
            } else {
                from.setDate(from.getDate() - from.getDay() + 1);
            }
            break;
        case '1w':
        default:
            from.setDate(from.getDate() - 6);
            break;
    }

    document.getElementById('compilation_from').value = from.toLocaleDateString();
    document.getElementById('compilation_tom').value = tom.toLocaleDateString();

}

function fetchCompilation() {
    let from = document.getElementById('compilation_from').value;
    let to = document.getElementById('compilation_tom').value;

    let url = serverPath + 'getCompilation.php?to=' + to + '&from=' + from;
    fetch(url)
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    showToast('error', data.error);
                    return;
                }
                clearTableBody('table_compilation');
                if (data.result) {
                    let table = document.getElementById('table_compilation');
                    for (let task of data.tasks) {
                        let row = document.createElement('tr');
                        let c = row.insertCell()
                        c.innerHTML = task.activity;
                        c = row.insertCell();
                        c.innerHTML = task.time;
                        c.className = 'right';
                        table.tBodies[0].appendChild(row);
                    }
                } else {
                    showToast('info', data.message);
                }
            });
}

function selectRadioDate() {
    let frm = document.forms['tasklist'];
    frm.taskList_listTyp[0].checked = true;
}

function setPagination() {
    let url = serverPath + "getTasklist.php?page=1";
    fetch(url)
            .then(response => {
                return response.json();
            }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }
        let antalSidor = data.pages;
        if (antalSidor < lastPage) {
            lastPage = parseInt(antalSidor);
        }
        let paginering = document.getElementById('tasklist_sidor');
        while (paginering.hasChildNodes()) {
            paginering.removeChild(paginering.childNodes[0]);
        }
        for (let i = 1; i <= antalSidor; i++) {
            let p = document.createElement('span');
            p.innerHTML = i;
            if (i === lastPage && document.forms['tasklist'].taskList_listTyp.value === 'sida') {
                p.className = 'lastPage';
            } else {
                p.className = 'pagination';
                p.addEventListener('click', fetchTasklistPage);
            }
            paginering.appendChild(p);
        }
    });
}

function getTasklist() {
    let from = new Date();
    let tom = new Date();
    let intervall = getCookie('tasklistIntervall', '3d');
    switch (intervall) {
        case '2d':
            from.setDate(from.getDate() - 1);
            break;
        case '3d':
            from.setDate(from.getDate() - 2);
            break;
        case '2w':
            from.setDate(from.getDate() - 13);
            break;
        case '0w':
            if (from.getDay() === 0) {
                from.setDate(from.getDate() - 6);
            } else {
                from.setDate(from.getDate() - from.getDay() + 1);
            }
            break;
        case '1w':
        default:
            from.setDate(from.getDate() - 6);
            break;
    }

    document.getElementById('tasklist_from').value = from.toLocaleDateString();
    document.getElementById('tasklist_tom').value = tom.toLocaleDateString();

    let frm = document.forms['tasklist'];
    let varde = getCookie('tasklistStandard', 'sida');
    for (let btn of frm['taskList_listTyp']) {
        btn.checked = (btn.value === varde);
    }


    if (varde === 'sida') {
        fetchTasklistPage();
    } else {
        fetchTasklistDates();
    }
}

function fetchTasklistPage(ev) {
    if (ev) {
        lastPage = parseInt(ev.target.innerHTML);
    }
    let frm = document.forms['tasklist'];
    frm.taskList_listTyp.value = 'sida';
    let page=lastPage;
    let url = serverPath + 'getTasklist.php?page=' + page + '&records=' + getCookie('tasklistStorlek', 20);
    fetch(url)
            .then(response => {
                return response.json();
            }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }
        if(data.pages<page){
            lastPage=data.pages;
            fetchTasklistPage();
            return;
        }
        clearTableBody('table_tasklist');
        fillTasklist(data.tasks);
        setPagination();
    });
}
function fetchTasklistDates() {
    let from = document.getElementById('tasklist_from').value;
    let to = document.getElementById('tasklist_tom').value;
    let frm = document.forms['tasklist'];
    frm.taskList_listTyp.value = 'datum';

    let url = serverPath + 'getTasklist.php?to=' + to + '&from=' + from;
    fetch(url)
            .then(response => {
                return response.json();

            }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }
        clearTableBody('table_tasklist');
        setPagination();
        fillTasklist(data.tasks);
    });
}

function fillTasklist(tasks) {
    let table = document.getElementById('table_tasklist');
    for (let task of tasks) {
        let row = document.createElement('tr');
        let c = row.insertCell()
        let img = document.createElement('img');
        img.src = "images/edit.png";
        img.height = "16";
        img.setAttribute('data-taskid', task.id);
        img.addEventListener('click', editTask);
        c.appendChild(img);

        c = row.insertCell()
        c.innerHTML = task.date;
        c = row.insertCell();
        c.innerHTML = task.activity;
        c = row.insertCell();
        c.innerHTML = task.time;
        c.className = 'right';
        c = row.insertCell();
        if (task.description.replace('/r/n', '').length > getCookie('settings_maxlength', 70)) {
            c.innerHTML = chopText(task.description.replace('\r\n', '<br>'), getCookie('settings_maxlength', 70));
            c.setAttribute('title', task.description);
        } else {
            c.innerHTML = task.description.replace('\r\n', '<br>');
        }
        c = row.insertCell();
        img = document.createElement('img');
        img.src = "images/delete.png";
        img.height = "16";
        img.setAttribute('data-taskid', task.id);
        img.addEventListener('click', deleteTask);
        c.appendChild(img);
        table.tBodies[0].appendChild(row);
    }
}
function deleteTask(ev) {
    let url = serverPath + 'deleteTask.php';
    let id = ev.target.getAttribute('data-taskid');
    ev.cancelBubble = true;
    let fd = new FormData();
    fd.append('id', id);

    fetch(url, {
        method: 'POST',
        body: fd
    }).then(response => {
        return response.json();
    }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }
        if (data.result) {
            showToast('ok', data.message);
        } else {
            showToast('error', data.message);
        }
    }).finally(() => {
        setPagination();
        getTasklist();
    });
}

function editTask(ev) {
    ev.cancelBubble = true;
    let id = ev.target.getAttribute('data-taskid');
    let url = serverPath + 'getTask.php?id=' + id;

    fetch(url).then(response => {
        return response.json();
    }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }
        editTask(data);
    })

}
function saveTask(ev) {
    let frm = document.forms['tasksDetail'];
    let fd = new FormData(frm);
    fd.delete('id');
    let url = serverPath + 'saveTask.php';
    if (frm.id.value !== '') {
        url += '?id=' + frm.id.value;
    }
    fetch(url, {
        method: 'POST',
        body: fd
    }).then(response => {
        return response.json();
    }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }
        document.getElementById('modal_form').style.display = 'none';
        showToast('ok', data.message);
        getTasklist();
    })
}

function toggleTasklistStandard(ev) {
    let frm = document.forms['settings'];

    if (ev.target.value === 'sida') {
        frm['settings_tasklistStorlek'].disabled = false;
        frm['settings_tasklistIntervall'].disabled = true;
    } else {
        frm['settings_tasklistStorlek'].disabled = true;
        frm['settings_tasklistIntervall'].disabled = false;
    }

}

function getActivities() {
    // Funktioner som ska göras i samband med att aktivitetsfliken väljs
}

function fetchActivities() {
    let url = serverPath + 'getActivityList.php';
    fetch(url)
            .then(response => {
                return response.json();
            }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }

        // Rensa tabellen
        clearTableBody('table_activities');

        // Rensa inmatningsrutan för uppgiter
        let sel = document.getElementById('activitiesSelect');
        while (sel.options.length > 0) {
            sel.remove(0);
        }

        // Skapa tabellen
        let table = document.getElementById('table_activities');
        let row = document.createElement('tr');
        let c = row.insertCell();
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.id = 'activityId';
        c.appendChild(input);
        c = row.insertCell();
        input = document.createElement('input');
        input.type = 'text';
        input.name = 'activity';
        input.id = 'activity';
        c.appendChild(input);
        c = row.insertCell();
        let img = document.createElement('img');
        img.src = "images/add.png";
        img.height = "16";
        img.id = 'saveIcon';
        img.addEventListener('click', saveActivity);
        c.appendChild(img);
        table.tBodies[0].appendChild(row);
        for (let act of data.activities) {
            row = document.createElement('tr');
            row.addEventListener('click', fillInputRow);
            let c = row.insertCell();
            c.innerHTML = act.id;
            c.className = 'right';
            c = row.insertCell();
            c.innerHTML = act.activity;
            c = row.insertCell();
            img = document.createElement('img');
            img.src = "images/delete.png";
            img.height = "16";
            img.setAttribute('data-activityid', act.id);
            img.addEventListener('click', deleteActivity);
            c.appendChild(img);
            table.tBodies[0].appendChild(row);

            // Skapa options för tasksinmatningen
            let opt = new Option(act.activity, act.id);
            sel.add(opt);
        }
    });

}

function deleteActivity(ev) {
    let url = serverPath + 'deleteActivity.php'
    ev.cancelBubble = true;
    let fd = new FormData();
    let id = ev.target.getAttribute('data-activityid');
    fd.append('id', id);

    fetch(url, {
        method: 'POST',
        body: fd
    }).then(response => {
        return response.json();
    }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }
        if (data.result) {
            showToast('ok', data.message);
        } else {
            showToast('error', data.message);
        }
    }).finally(() => {
        fetchActivities();
    });
}
function saveActivity() {
    let fd = new FormData();
    let url = serverPath + 'saveActivity.php'
    if (document.getElementById('activityId').value !== '') {
        url += '?id=' + document.getElementById('activityId').value;
    }
    fd.append('activity', document.getElementById('activity').value);

    fetch(url, {
        method: 'POST',
        body: fd
    }).then(response => {
        return response.json();
    }).then(data => {
        if (data.error) {
            showToast('error', data.error);
            return;
        }
        showToast('ok', data.message);
    }).finally(() => {
        fetchActivities();
    });
}

function fillInputRow(ev) {
    let row = ev.target.parentNode;

    document.getElementById('activityId').value = row.cells[0].innerHTML;
    document.getElementById('activity').value = row.cells[1].innerHTML;
    document.getElementById('saveIcon').src = "images/save.png";
    ev.cancelBubble = true;

}

function showToast(type, messages) {
    let out = '', text, toast = document.getElementById('toast');
    for (text of messages) {
        out += text + "<br>";
    }
    toast.innerHTML = out;
    toast.className = type;

    setTimeout(function () {
        toast.className = '';
    }, 3000);

}

function editTask(task) {
    let frm = document.forms['tasksDetail'];
    frm.id.value = task.id;
    let sel = frm.activityId
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value === task.activityId) {
            sel.options[i].selected = true;
            break;
        }
    }
    frm.time.value = task.time;
    frm.date.value = task.date;
    frm.description.value = task.description;
    document.getElementById('modal_form').style.display = 'block';
}

function newTask() {
    let frm = document.forms['tasksDetail'];
    frm.id.value = '';
    let sel = frm.activityId;
    sel.selectedIndex = -1;
    frm.time.value = getCookie('standardtid', '1:30');
    let d = new Date();
    frm.date.value = d.toLocaleDateString();
    frm.description.value = '';
    document.getElementById('modal_form').style.display = 'block';
}