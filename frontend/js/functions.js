function getCookie(namn, standard = null) {
    let cookie = document.cookie.match('(^|;) ?' + namn + '=([^;]*)(;|$)');
    return cookie ? cookie[2] : standard;
}

function setCookie(namn, varde, days = - 1) {
    var d = new Date;
    if (days !== -1) {
        d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * days);
    } else {
        d.setFullYear(2030, 11, 31);
    }
    document.cookie = namn + "=" + varde + ";path=/;expires=" + d.toGMTString();
}

function clearTableBody(id) {
    table = document.getElementById(id);
    if (table === null) {
        return;
    }

    while (table.rows.length > 1) {
        table.deleteRow(1);
    }
}

function chopText(text, length) {
    if(text.length<length) {
        return text;
    }
    
    return (text.substring(0,length) + "…");
}