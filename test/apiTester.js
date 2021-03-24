let testOK = 0;
let testFail = 0;
window.onload = function () {
    document.getElementById('testKnapp').addEventListener('click', doTest);
    for (let test of testCases) {
        addTestToCombo(test);
    }
};

function doTest() {
    document.getElementById('resultat').innerHTML = '';
    tR = document.getElementById('testResultat');
    tR.innerHTML = '';
    testOK = 0;
    testFail = 0;
    let tc = document.getElementById('testCases');
    let parent = tc.parentNode;
    tc.parentNode.removeChild(tc);
    tc = document.createElement('script');
    tc.setAttribute('id', 'testCases');
    tc.setAttribute('src', 'testCases.js');
    parent.appendChild(tc);
    tc.addEventListener('load', function () {
        testFile = document.getElementById('url').value;
        for (let test of testCases) {
            if (testFile == '*' || test.url.startsWith(testFile)) {
                appendTest(test);
            } 
            addTestToCombo(test);
        }
    });
}

function appendTest(testCase) {
    let cont = document.getElementById('resultat');
    cont.style.display = "flex";
    let testDiv = document.createElement('div');
    let head = document.createElement('h2');
    head.innerText = testCase.name;
    let expected = document.createElement('p');
    let path = document.getElementById('api').value;
    let url = '';
    if (path.substring(-1) !== "/") {
        url = path + "/" + testCase.url;
    } else {
        url = path + testCase.url;
    }
    expected.innerHTML = 'url: <a href="' + url + '">' + testCase.url + '</a><br> method:' + testCase.method + '<br> status:' + testCase.status;
    testDiv.appendChild(head);
    testDiv.appendChild(expected);
    let result = document.createElement('p');
    sendTest(testCase, result);
    testDiv.appendChild(result);
    cont.appendChild(testDiv);
}

function sendTest(testCase, tag) {
    let path = document.getElementById('api').value;
    let url = '';
    let returnText = '';
    if (path.substring(-1) !== "/") {
        url = path + "/" + testCase.url;
    } else {
        url = path + testCase.url;
    }
    fetch(url, {
        method: testCase.method,
        body: testCase.data
    }).then(response => {
        if (response.status !== testCase.status) {
            tag.className = 'error';
            returnText += "status:" + response.status + " <= INVALID expected " + testCase.status + "<br>";
        } else {
            tag.className = 'ok';
            returnText += "status:" + response.status + "<br>";
        }
        tag.innerHTML = returnText;
        return response.json();
    }).then(data => {
        for (field in testCase.result) {
            if (Array.isArray(testCase.result[field])) {
                if (Array.isArray(data[field])) {
                    let err = '';
                    returnText += field + ": [";
                    for (p in data[field]) {
                        returnText += '{';
                        for (eP in testCase.result[field][0]) {
                            if (data[field][p][eP] != undefined) {
                                returnText += eP + ":" + data[field][p][eP] + ', ';
                            } else {
                                tag.className = 'error';
                                err += eP + "<= SAKNAS! <br>";
                            }
                        }
                        returnText += '}<br>';
                    }

                    returnText += "]<br>";
                    returnText += err;
                } else {
                    returnText += field + ":" + data[field] + +" <= INVALID expected Array <br>";
                    tag.className = 'error';
                }
            } else {
                if (testCase.result[field] === '?' && data[field] != undefined) {
                    returnText += field + ":" + data[field] + "<br>";
                } else {
                    if (testCase.result[field] !== data[field] || data[field] == undefined) {
                        returnText += field + ":" + data[field] + " <= INVALID expected " + testCase.result[field] + "<br>";
                        tag.className = 'error';
                    } else {
                        returnText += field + ":" + data[field] + "<br>";
                    }
                }
            }
        }
        tag.className === 'error' ? testFail++ : testOK++;
        document.getElementById('testResultat').innerHTML = '<p>' + (testOK + testFail) + ' tester genomförda.</p><p class="ok">' + testOK + ' tester lyckades.</p><p class="error">' + testFail + ' tester misslyckades.</p>';
        tag.innerHTML = returnText;
    });
}


function addTestToCombo(test) {
    let tmpUrl = test.url;
    let url = tmpUrl.substr(0, tmpUrl.indexOf('.php') + 4);

    let sel = document.getElementById('url');
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].text === url) {
            return;
        }
    }
    opt = new Option(url);
    sel.options.add(opt);
}
