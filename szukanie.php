<?php
function charset_utf_fix($string)
{
    $utf = array(
      "%u0104" => "�",
     "%u0106" => "�",
     "%u0118" => "�",
     "%u0141" => "�",
     "%u0143" => "�",
     "%D3" => "�",
     "%u015A" => "�",
     "%u0179" => "�",
     "%u017B" => "�",
     "%u0105" => "�",
     "%u0107" => "�",
     "%u0119" => "�",
     "%u0142" => "�",
     "%u0144" => "�",
     "%F3" => "�",
     "%u015B" => "�",
     "%u017A" => "�",
     "%u017C" => "�"
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