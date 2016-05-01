var xmlHttpR_COsupport = false;
xmlHttpR_support = false;
var req;

function setProgress(mess)
{
    document.getElementById("postep").innerHTML = mess;
}

function validate() {
    var idField = document.getElementById("tytul");
    if (idField.value == "" || id=="0")
    {
        document.getElementById('szukane').style.visibility= 'hidden';
        document.getElementById('napis_wyniki').style.visibility = 'hidden';
        document.getElementById('szukane').innerHTML = "";
        document.getElementById('napis_wyniki').innerHTML = "";
        return false;
    }
    else
    {
        document.getElementById('szukane').style.visibility = 'visible';
        document.getElementById('napis_wyniki').style.visibility = 'visible';
    }
        var url = "/szukanie.php?fraza=" + idField.value + "&pom=" + id;
        if (XMLHttpRequest)
        {
            req = new XMLHttpRequest();
            xmlHttpR_support = true;
        }
        else
        {
            if (window.XMLHttpRequest)
            {
                req = new XMLHttpRequest();
            }
            else
                if (window.ActiveXObject)
                {
                    req = new ActiveXObject("Microsoft.XMLHTTP");
                }
        }
        if (xmlHttpR_support)
        { //jest XMLHttpRequest Level 2
            req.onprogress = function (e)
            {
                var total = e.total;  //całość do przesłania
                var loaded = e.loaded;  // ilość przesłana
                if (e.lengthComputable)
                { // czy całkowita ilość znana?
                    // do something with the progress information
                    var ratio = e.loaded / e.total;
                    
                    setProgress(ratio + "% downloaded");
                }
            }
            req.upload.onprogress = function (e)
            {
                var total = e.total;
                var loaded = e.loaded;
                if (e.lengthComputable)
                {
                    // do something with the progress information
                    var ratio = e.loaded / e.total;
                    setProgress(ratio + "% downloaded");
                }
            }
            req.onload = function (e) { setProgress("finished"); }
            req.onerror = function (e) { setProgress("error"); }
        }
        req.onreadystatechange = callback;
        req.open("GET", url, true);
        req.send();
}

function callback()
{
    if (req.readyState == 4 && req.status == 200)
    {
        var message = JSON.parse(req.responseText);
        setMessage(message);
    }
}

function setMessage(message)
{
    document.getElementById('napis_wyniki').innerHTML = "Wyniki wyszukiwania(" + message.ilosc + ")";
    var szukane = "";
    for (var i = 0; i < message.ilosc; i++)
        szukane+= "<div style=\"vertical-align: middle; display: inline-block; position: relative;\">\
        " + message.tytul[i] + "<br>\
        <a href=\"./images/wodny_" + message.plik[i] + "\" target=\"_blank\">\
        <img width=\"200\" height=\"100\" alt=\"" + message.tytul[i] + "\" src=\"./images/min_" + message.plik[i] + "\">\
        </a>\
        <input name=\"szukane_id_zdj" + i + "\" type=\"hidden\" value=\"" + message.plik[i] + "\">\
        <input name=\"szukana"+i+"\" style=\"right: 0px; bottom: 0px; position: absolute;\" type=\"checkbox\" value=\"on\">\
        </div>";
    document.getElementById("szukane").innerHTML = szukane;
}