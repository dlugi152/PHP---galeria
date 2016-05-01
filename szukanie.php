<?php
function charset_utf_fix($string)
{
    $utf = array(
      "%u0104" => "¥",
     "%u0106" => "Æ",
     "%u0118" => "Ê",
     "%u0141" => "£",
     "%u0143" => "Ñ",
     "%D3" => "Ó",
     "%u015A" => "Œ",
     "%u0179" => "",
     "%u017B" => "¯",
     "%u0105" => "¹",
     "%u0107" => "æ",
     "%u0119" => "ê",
     "%u0142" => "³",
     "%u0144" => "ñ",
     "%F3" => "ó",
     "%u015B" => "œ",
     "%u017A" => "Ÿ",
     "%u017C" => "¿"
    );
    return str_replace(array_keys($utf), array_values($utf), $string);
}
$zdjecia = "zdjecia.xml";
$dane=simplexml_load_file("./$zdjecia");
$fraza = $_GET['fraza'];
$fraza=charset_utf_fix($fraza);
$tytuly=array_values($dane->xpath("/zdjecia/zdj/tytul"));
$n=0;
$user=$_GET['pom'];
$znalezione->plik[$n]=(string)"null";
foreach($tytuly as $i)
    if (preg_match("/^(.+|.?)$fraza(.+|.?)$/",$i) && $fraza!=null)
    {
        if($dane->xpath("/zdjecia/zdj[tytul=\"$i\"][attribute::publiczny='Nie'][uzytkownik!=\"$user\"]"))
            continue;
        $pom=array();
        $pom=$dane->xpath("/zdjecia/zdj[tytul=\"$i\"]/plik");
        $znalezione->plik[$n]=(string)$pom[0];
        $znalezione->tytul[$n++]=(string)$i;
    }
$znalezione->ilosc=$n;
echo json_encode($znalezione);
?>