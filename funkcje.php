<?php
function miniaturka($org,$s_org,$w_org,$plik,$fol,$prfx,$s_min,$w_min)
{
    $min = imagecreatetruecolor($s_min, $w_min);
    imagecopyresized($min, $org, 0, 0, 0, 0, $s_min, $w_min, $s_org, $w_org);
    imagejpeg($min, $fol."/".$prfx.$plik, 50);
    imagedestroy($min);
}
function wodny($org,$s_org,$w_org,$plik,$fol,$prfx2)
{
    $im = imagecreate($s_org, 15);
    $bg = imagecolorallocate($im, 1, 1, 1);
    imagecolortransparent($im, $bg);
    $textcolor = imagecolorallocate($im, 255, 255, 255);
    if ($_POST['wodny']!="")
        $tex=$_POST['wodny'];
    else
        $tex=$plik;
    imagestring($im, 5, ($s_org-9*strlen($tex))/2, 0, $tex, $textcolor);
    //imagecolortransparent($im, $bg);
    imagepng($im);
    $s_im = imagesx($im);
    $w_im = imagesy($im);
    imagecopyresampled($org, $im, 0, (($w_org - $w_im)), 0, 0, $s_im, $w_im, $s_im, $w_im);
    imagejpeg($org, $fol."/".$prfx2.$plik, 100);
    imagedestroy($im);
}

function dodaj_wszystko($zdjecia,$fol,$min,$wodny,$s_min,$w_min)
{
    $dane = simplexml_load_file($zdjecia);
    $uchwyt = opendir($fol);
    while(false !== ($plik = readdir($uchwyt)))
        if(!$dane->xpath("/zdjecia/zdj/plik[.=\"$plik\"]") &&
        !is_file($fol."/".$min.$plik) && !is_file($fol."/".$wodny.$plik) &&
        substr($plik, 0, strlen($min)) != $min &&
        substr($plik, 0, strlen($wodny)) != $wodny &&
        preg_match("/^.+\.(jpe?|pn)g$/", $plik) )
        {
            if (preg_match("/^.+\.png$/", $plik))
                $org = imagecreatefrompng($fol."/".$plik);
            else
                $org = imagecreatefromjpeg($fol."/".$plik);
            $s_org = imagesx($org);
            $w_org = imagesy($org);
            miniaturka($org,$s_org,$w_org,$plik,$fol,$min,$s_min,$w_min);
            wodny($org,$s_org,$w_org,$plik,$fol,$wodny);
            imagedestroy($org);
            $zdj = $dane->addChild('zdj');
            $zdj->addAttribute('publiczny', 'Tak');
            $zdj->addChild('id_zdj', nowe_id($dane));
            $zdj->addChild('tytul', $plik);
            $zdj->addChild('plik', $plik);
        }
    closedir($uchwyt);
    $plik = fopen($zdjecia, "w");
    fwrite($plik,$dane->asXML());
    fclose($plik);
}
function rejestracja()
{
    echo "<p>Rejestracja</p>
    <form action=\"index.php\" method=\"post\">
    <input type=\"text\" name=\"login\" placeholder=\"Login\"/><br>
    <input type=\"password\" name=\"haslo1\" placeholder=\"Hasło\"/><br>
    <input type=\"password\" name=\"haslo2\" placeholder=\"Potwiedź hasło\" /><br>
    <input value=\"Zarejestruj\" type=\"submit\" name=\"dodaj_uzytkownika\">
    </form>";
}
function logowanie()
{
    echo "<p>Logowanie</p>
    <form action=\"index.php\" method=\"post\">
    <input type=\"text\" name=\"login\" placeholder=\"Login\"/><br>
    <input type=\"password\" name=\"haslo\" placeholder=\"Hasło\"/><br>
    <input value=\"Zaloguj się\" type=\"submit\" onclick=\"szyfruj();\" name=\"logowanko\">
    </form>";
}
function nowe_id($dane)
{
    while (1)
    {
        $nr=mt_rand()+1;
        if(!$dane->xpath("/zdjecia/zdj/id_zdj[.=\"$nr\"]") && !$dane->xpath("/logowanie/uzytkownik/identyfikator[.=\"$nr\"]"))
            return $nr;
    }
}
function dodaj_uzytkownika($uzytkownicy)
{
    $dane = simplexml_load_file($uzytkownicy);
    $blad=false;
    if ($_POST['haslo1']!=$_POST['haslo2'])
    {
        $blad=true;
        echo "<div style=\"position:absolute; color:red;\"><h2>hasła nie są takie same</h2></div><br>";
    }
    if (!preg_match("/^([a-z\d]{1})([a-z\d_-]{2,20})$/D",$_POST['login']))
    {
        $blad=true;
        echo "<div style=\"position:absolute; color:red;\">login jest niepoprawny - musi się rozpoczynać od cyfry lub litery,
        potem dozwolone są litery, cyfry, '_', '-', max 20 zn, min 3</div><br>";
    }
    if (!preg_match("/^(?=[a-zA-Z0-9_#@%\*-]*?[a-z|A-Z])(?=[a-zA-Z0-9_#@%\*-]*?[0-9])([a-zA-Z0-9_#@%\*-]{5,20})$/D",$_POST['haslo1']))
    {
        echo "<div style=\"position:absolute; color:red;\">hasło musi zawierać co najmniej jedną cyfrę i co najmniej jedną literę,
        dozwolone są jeszcze '_#@%\\*-, max 20 zn, min 5</div>";
        $blad=true;
    }
    $pom=$_POST['login'];
    if ($dane->xpath("/logowanie/uzytkownik/nazwa[.=\"$pom\"]"))
    {
        echo "<div style=\"position:absolute; color:red;\"><h2>uzytkownik istnieje</h2></div>";
        $blad=true;
    }
    if ($blad==false)
    {
        $nowy = $dane->addChild('uzytkownik');
        $nowy->addChild('identyfikator', nowe_id($dane));
        $nowy->addChild('nazwa', $_POST['login']);
        $nowy->addChild('haslo', md5($_POST['haslo1']));
        $plik = fopen($uzytkownicy, "w");
        fwrite($plik,$dane->asXML());
        fclose($plik);
        echo "<div style=\"position:absolute; color:green;\"><h2>Możesz się zalogować</h2></div>";
        
    }
}
function logowanko($uzytkownicy)
{
    $dane = simplexml_load_file($uzytkownicy);
    $pom=$_POST['login'];
    $pom2=md5($_POST['haslo']);
    if (!$dane->xpath("/logowanie/uzytkownik/nazwa[.=\"$pom\"]") ||
        !$dane->xpath("/logowanie/uzytkownik/haslo[.=\"$pom2\"]"))
        echo "<div style=\"position:absolute; color:red;\"><h2>Niepoprawne dane logowania</h2></div>";
    else
    {
        $pom3 = array();
        $pom3=$dane->xpath("/logowanie/uzytkownik[nazwa=\"$pom\"]/identyfikator");
        $_SESSION['id']=(string)$pom3[0];
        $_SESSION['login']=$pom;
    }
}
function wyloguj()
{
    unset($_SESSION['id']);
    unset($_SESSION['login']);
}
function dodaj_nowe_zdjecie($plik,$fol,$min,$wodny,$s_min,$w_min,$zdjecia)
{
    $dane = simplexml_load_file($zdjecia);
    if (preg_match("/^.+\.png$/", $plik))
        $org = imagecreatefrompng($fol."/".$plik);
    else
        $org = imagecreatefromjpeg($fol."/".$plik);
    $s_org = imagesx($org);
    $w_org = imagesy($org);
    miniaturka($org,$s_org,$w_org,$plik,$fol,$min,$s_min,$w_min);
    wodny($org,$s_org,$w_org,$plik,$fol,$wodny);
    imagedestroy($org);
    $zdj = $dane->addChild('zdj');
    if (isset($_POST['publiczne']))
        $zdj->addAttribute('publiczny', 'Nie');
    else
        $zdj->addAttribute('publiczny', 'Tak');
    $zdj->addChild('id_zdj', nowe_id($dane));
    if (isset($_SESSION['id']))
        $zdj->addChild('uzytkownik',$_SESSION['id']);
    if ($_POST['tytul'])
        $zdj->addChild('tytul', $_POST['tytul']);
    else
        $zdj->addChild('tytul',$plik);
    $zdj->addChild('plik', $plik);
    $plik = fopen($zdjecia, "w");
    fwrite($plik,$dane->asXML());
    fclose($plik);
}

function nowe_zdjecie($fol,$min,$wodny,$s_min,$w_min,$zdjecia)
{
    $nowy_plik=$fol."/".basename($_FILES["upload"]["name"]);
    $typ = pathinfo($nowy_plik,PATHINFO_EXTENSION);
    if($typ!="jpg" && $typ!="png" && $typ!="jpeg")
    {
        echo "<div style=\"position:absolute; color:red;\"><h2>Wyślij plik jpg/jpeg lub png</h2></div>";
        return false;
    }
    if (file_exists($nowy_plik))
    {
        echo "<div style=\"position:absolute; color:red;\"><h2>Taki plik już wysłano</h2></div>";
        return false;
    }
    if ($_FILES["upload"]["tmp_name"]==NULL)
    {
        echo "<div style=\"position:absolute; color:red;\"><h2>Plik ma być nie większy niż 1mb</h2></div>";
        return false;
    }
    if (move_uploaded_file($_FILES["upload"]["tmp_name"], $nowy_plik))
    {
        echo "<div style=\"position:absolute; color:green;\"><h2>Przesłano plik ".basename($_FILES["upload"]["name"])."</h2></div><!--";
        dodaj_nowe_zdjecie(basename($_FILES["upload"]["name"]),$fol,$min,$wodny,$s_min,$w_min,$zdjecia);
        echo "-->";
    }
    else
        echo "<div style=\"position:absolute; color:red;\"><h2>Plik nie mógł być przesłany</h2></div>";
}
function pojedyncze_zdjecie($link,$miniaturka,$tytul,$plik,$n,$nazwa_form)
{
    echo "<div style=\"position: relative;vertical-align:middle;display:inline-block;\">
    $tytul<br>
    <a href=\"$link$plik\" target=\"_blank\">
    <img src=\"$miniaturka$plik\" width=\"200\" height=\"100\" alt=\"$tytul\">
    </a>
    <input type=\"hidden\" value=\"$plik\" name=\"id_zdj$n\"/>
    <input style=\"position: absolute; bottom: 0px; right: 0px;\" type=\"checkbox\" value=\"on\" name=\"$nazwa_form$n\"/>
    </div>\n";
}
function prywatne($fol,$wodny,$min,$zdjecia)
{
    echo "\n<h2>Twoje prywatne zdjęcia</h2>\n";
    $n=1;
    $dane=simplexml_load_file("./$zdjecia");
    foreach($dane->zdj as $i)
        if ($i->uzytkownik==$_SESSION['id'])
        {
            pojedyncze_zdjecie("$fol/$wodny","$fol/$min","$i->tytul","$i->plik",$n,"zapisz");
            $n++;
        }
    return $n;
}
function publiczne($fol,$wodny,$min,$zdjecia,$n)
{
    echo "\n<h2>Wszystkie publiczne zdjęcia</h2>\n";
    $dane=simplexml_load_file("./$zdjecia");
    foreach($dane->zdj as $i)
        if($i->attributes()=='Tak')
        {
            pojedyncze_zdjecie("$fol/$wodny","$fol/$min","$i->tytul","$i->plik",$n,"zapisz");
            $n++;
        }
    return $n;
}
function zapamietaj()
{
    if(!isset($_SESSION['ilosc_zapamietanych']))
    {
        $_SESSION['ilosc_zapamietanych']=0;
        $_SESSION['zapamietane']=array();
    }
    for ($i=0;$i<$_POST['ilosc'];$i++)
    {
        if (isset($_POST["szukana$i"]))
            if (!in_array($_POST["szukane_id_zdj$i"],$_SESSION['zapamietane']))
            {
                $_SESSION['zapamietane'][$_SESSION['ilosc_zapamietanych']]=$_POST["szukane_id_zdj$i"];
                $_SESSION['ilosc_zapamietanych']++;
            }
        if (isset($_POST["zapisz$i"]))
            if (!in_array($_POST["id_zdj$i"],$_SESSION['zapamietane']))
            {
                $_SESSION['zapamietane'][$_SESSION['ilosc_zapamietanych']]=$_POST["id_zdj$i"];
                $_SESSION['ilosc_zapamietanych']++;
            }
    }
}
function wyswietl_zapamietane($fol,$wodny,$min,$zdjecia)
{
    echo "\n<h2>Twoje zapamietane zdjecia</h2>\n";
    $n=1;
    $dane=simplexml_load_file("./$zdjecia");
    foreach($_SESSION['zapamietane'] as $i)
    {
        $tytul=$dane->xpath("/zdjecia/zdj[plik=\"$i\"]/tytul");
        pojedyncze_zdjecie("$fol/$wodny","$fol/$min","$tytul[0]","$i",$n,"wypisz");
        $n++;
    }
    return $n;
}
function nie_pamietaj()
{
    for ($i=1;$i<$_POST['ilosc'];$i++)
        if (isset($_POST["wypisz$i"]))
        {
            unset($_SESSION['zapamietane'][$i-1]);
            $_SESSION['ilosc_zapamietanych']--;
        }
    $_SESSION['zapamietane'] = array_values($_SESSION['zapamietane']);
}
//$conn = mysql_connect("localhost", "root","debmini") or die(mysql_error());
?>