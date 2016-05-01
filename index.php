<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="skrypty.js"></script>
<title>149174 Projekt 2</title>
</head>
<body style="background-color:rgb(39, 43, 48); color:silver;">
<?php
    session_start();
    include 'funkcje.php';
    //unset($_SESSION['zapamietane']);
    //unset($_SESSION['ilosc_zapamietanych']);
    $fol = "./images"; // folder ze zdjęciami
    $min = "min_"; // prefix poprzedzający nazwy miniaturek
    $wodny = "wodny_";
    $zdjecia = "zdjecia.xml";
    $uzytkownicy = "uzytkownicy.xml";
    $s_min = 200;
    $w_min = 100;
    $dane=simplexml_load_file("./$zdjecia");
    if (require_once 'tworz_xml.php')
        dodaj_wszystko($zdjecia,$fol,$min,$wodny,$s_min,$w_min);
    if (isset($_POST['logowanko']))
    {
        logowanko($uzytkownicy);
    }
    if (isset($_POST['nie_wyswietl']))
    {
        unset($_SESSION['wlasna_lista']);
    }
    if (isset($_POST['nie_pamietaj']))
    {
        nie_pamietaj();
    }
    if (isset($_POST['wylogowanie']))
    {
        wyloguj();
    }
    if (isset($_POST['dodaj_uzytkownika']))
    {
        dodaj_uzytkownika($uzytkownicy);
    }
    if (isset($_POST['wyslij']))
    {
        nowe_zdjecie($fol,$min,$wodny,$s_min,$w_min,$zdjecia);
    }
    if (isset($_POST['zapamietaj']))
    {
        zapamietaj();
    }
    echo "<form action=\"index.php\" method=\"post\">\n<div id=\"galeria\" style=\"height:600px; overflow: scroll; text-align:center; background-color:rgba(114, 113, 203, 0.57);\">";
    if (isset($_SESSION['id']))
    {
        $id=$_SESSION['id'];
        echo "<script type='text/javascript'>var id=\"$id\"</script>\n";
    }
    else
        echo "<script type='text/javascript'>var id=\"0\"</script>\n";
    $n=0;
    echo "<h2 id=\"napis_wyniki\" style=\"visibility: hidden;\"></h2><div style=\"visibility: hidden;\" id=\"szukane\"></div>";
    if (isset($_POST['wyswietl']) || isset($_SESSION['wlasna_lista']))
    {
        $_SESSION['wlasna_lista']=true;
        $n=wyswietl_zapamietane($fol,$wodny,$min,$zdjecia);   
    }
    else
    {
        if (isset($_SESSION['login']))
            $n=prywatne($fol,$wodny,$min,$zdjecia);
        $n=publiczne($fol,$wodny,$min,$zdjecia,$n);
    }
    echo "</div><div style=\"float:right;\"><p>Twoja własna kolekcja</p>";
    echo "<input type=\"hidden\" value=\"$n\" name=\"ilosc\"/>";
    if (isset($_SESSION['wlasna_lista']))
    {
        echo "<input style=\"width:170px\" type=\"submit\" value=\"Nie pamiętaj wybranych\" name=\"nie_pamietaj\"/><br>";
        echo "<input style=\"width:170px\" type=\"submit\" value=\"Wyświetl pozostałe\" name=\"nie_wyswietl\"/>";
    }
    else
    {
        echo "<input style=\"width:170px;\" type=\"submit\" value=\"Zapamiętaj wybrane\" name=\"zapamietaj\"/><br>";
        echo "<input style=\"width:170px;\" type=\"submit\" value=\"Wyświetl zapamiętane\" name=\"wyswietl\"/>";
    }
    echo "</div></form><div style=\"float:left;\">\n";
    if (isset($_POST['logowanie']))
        logowanie();
    if(isset($_POST['rejestracja']))
        rejestracja();
    if (isset($_SESSION['login']))
    {
        echo "<p>Zalogowano <span style=\"color:white;\"><b>".$_SESSION['login']."</b></span></p>
        <form action=\"index.php\" method=\"post\">
        <input style=\"width:90px;\" value=\"Wyloguj\" type=\"submit\" name=\"wylogowanie\">
        </form>";
    }
    if (!isset($_POST['logowanie']) && !isset($_POST['rejestracja']) && !isset($_SESSION['login']))
        echo "<form action=\"index.php\" method=\"post\">
                <p>Zaloguj się by wysyłać zdjęcia prywatnie</p>
                <input style=\"width:90px;\" value=\"Rejestracja\" type=\"submit\" name=\"rejestracja\"><br>
                <input style=\"width:90px;\" value=\"Logowanie\" type=\"submit\" name=\"logowanie\">
                </form>";
    
    echo "</div><div style=\"width:200px;margin: 0 auto;\">";
    echo "<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">
    <p>Wzbogać tą galerię</p>
    <input type=\"file\" name=\"upload\" id=\"upload\">
    <input type=\"text\" name=\"tytul\" id=\"tytul\" onkeyup=\"validate();\" placeholder=\"Tytuł\"/><span id=\"postep\"></span>
    <input type=\"text\" name=\"wodny\" id=\"wodny\" placeholder=\"Znak wodny\"/><br>";
    if (isset($_SESSION['login']))
          echo "<input type=\"checkbox\" value=\"on\" name=\"publiczne\">wyślij jako prywatne";
    echo "<input type=\"submit\" value=\"Wyślij\" name=\"wyslij\">
        </form>";
    echo "</div>";
    
?>
</body>
</html>