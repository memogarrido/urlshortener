function isUrlValid(userInput) {
    var res = userInput.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    if (res == null)
        return false;
    else
        return true;
}
function alphanumeric(str)
{
    var letters = /^[0-9a-zA-Z]+$/;
    if (str.match(letters))
        return true;
    else
        return false;
}
var SUCCESS = 0;
var ERROR_FALTARON_PARAMETROS = 1;
var ERROR_INESPERADO_CATCH = 2;
var ERROR_DE_CONEXION_BD = 3;
var ERROR_DATOS_ERRONEOS = 4;
var ERROR_DATO_NO_INSERTADO_ACTUALIZADO_BD = 5;
var WARN_LA_CONSULTA_NO_OBTUVO_RESULTADOS = 6;

function shortenURL() {
    var imgAnim = document.getElementById('imgAnim');
    var longURL = document.getElementById('longURL');
    var shortURL = document.getElementById('shortURL');
    var hash = document.getElementById('hash');
    imgAnim.className += " animado";
    var data = new FormData();
    data.append('url', longURL.value);
    if (isUrlValid(longURL.value) && (hash.value.length === 0 || alphanumeric(hash.value))) {
        if (hash.value.length > 0)
            data.append('hash', hash.value);
        doJSONRequest("http://localhost:3000/links/", data, "POST", function (data) {
            if (data.status == 0) {
                shortURL.value = "http://localhost:3000/" + data.link.hash;
            }
            sendMessage(data.message, data.status);
        });
    } else {
        sendMessage("Not a valid URL", ERROR_DATOS_ERRONEOS);
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
            } else {
                sendMessage("Error en la petición", ERROR_INESPERADO_CATCH);
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

function sendMessage(msg, status) {
    mensaje = document.getElementById('mensaje');

    imgAnim.className = "transfer-icon";
    switch (status) {
        case SUCCESS:
            mensaje.innerHTML = '<div class="isa_success">' +
                    '<i class="fa fa-check"></i>Operación realizada con éxto</div>';
            break;
        case ERROR_DATOS_ERRONEOS:
        case ERROR_DATO_NO_INSERTADO_ACTUALIZADO_BD:
        case ERROR_DE_CONEXION_BD:
        case ERROR_FALTARON_PARAMETROS:
        case ERROR_INESPERADO_CATCH:
            mensaje.innerHTML = '<div class="isa_error">' +
                    '<i class="fa fa-times-circle"></i>' +
                    msg + '</div>';
            break;
        case WARN_LA_CONSULTA_NO_OBTUVO_RESULTADOS:
            mensaje.innerHTML = '<div class="isa_warning">' +
                    '<i class="fa fa-times-warning"></i>' +
                    msg + '</div>';
            break;
    }
}