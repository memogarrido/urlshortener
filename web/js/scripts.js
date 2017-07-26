function isUrlValid(userInput) {
    var res = userInput.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    if(res == null)
        return false;
    else
        return true;
}

function shortenURL() {
    var imgAnim = document.getElementById('imgAnim');
    var longURL = document.getElementById('longURL');
    var shortURL = document.getElementById('shortURL');
    imgAnim.className += " animado";
    var data = new FormData();
    data.append('url', longURL.value);
    if (isUrlValid(longURL.value)) {
        doJSONRequest("http://localhost:3000/links/", data, "POST", function (data) {
            if (data.status == 0) {
                shortURL.value = "http://localhost:3000/" + data.link.hash;
            } else
                alert("Ocurrio un error al obtener url " + data.message);
            imgAnim.className = "transfer-icon";
        });
    }
    else{
        console.log("Not valid URL");
    }

}
function getLatestLinks(offset) {
    var data = {
        offset: offset + 1
    };
    doJSONRequest("http://localhost:3000/links/", data, "GET", function (data) {
        var tableDom = document.getElementById('tblBodyLinks');
        console.log(data);
        data.links.forEach(function (link) {
            tableDom.innerHTML = tableDom.innerHTML + "<tr><td>" + link.hash + "</td><td>" + link.urlOrig + "</td></tr>";
        });
        var tableFooterDom = document.getElementById('tblLinksFooterTd');
        tableFooterDom.innerHTML = "<button onclick='getLatestLinks(" + (offset + 100) + ")'>Load more data</button>";
    });
}
function doJSONRequest(url, data, method, callback) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {
            if (xmlhttp.status == 200) {
                callback(JSON.parse(xmlhttp.responseText));
            } else if (xmlhttp.status == 400) {
                alert('There was an error 400');
            } else {
                alert('something else other than 200 was returned');
            }
        }
    };
    if (method === "GET") {
        console.log(url + formatParams(data));
        xmlhttp.open(method, url + formatParams(data), true);
        xmlhttp.send();
    } else if (method === "POST") {
        xmlhttp.open(method, url, true);
        xmlhttp.send(data);
    }
}

function formatParams(params) {
    return "?" + Object
            .keys(params)
            .map(function (key) {
                return key + "=" + encodeURIComponent(params[key]);
            })
            .join("&");
}