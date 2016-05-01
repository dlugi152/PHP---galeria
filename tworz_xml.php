<?php
$zdjecia = "zdjecia.xml";
$uzytkownicy = "uzytkownicy.xml";
if (!file_exists($uzytkownicy))
{
    $plik = fopen($uzytkownicy, "w");
    fwrite($plik,"<?xml version=\"1.0\"?>\n<logowanie>\n</logowanie>");
    fclose($plik);
}
if (!file_exists($zdjecia))
{
    $plik = fopen($zdjecia, "w");
    fwrite($plik,"<?xml version=\"1.0\"?>\n<zdjecia>\n</zdjecia>");
    fclose($plik);
    return true;
}
return false;
?>