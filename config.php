<?php

error_reporting(-1);
include("php-txt-db-api-0.3.1-Beta-01/txt-db-api.php");
$db=new Database("funkuebung");

$sprechgruppen=array("TULLN/TU Ausweich 1","TULLN/TU Ausweich 2","KREMS/KR Ausweich 3","KORNEUBURG/KO Ausweich 4","ZWETTL/ZW Ausweich 5");
$funkmodis=array("TMO","DMO");

$einheiten=array("Anton","Berta","Cäsar","Kommando","Feuerwehr","Rüstlösch");
$ortsnamen=array("Elsdorf","Plankenbach","Rappoltenberg","Dietersbach","Röhrenkirchen");

$moegliche_teilnehmer=array();
for($i=0;$i<30;$i++) {
  $funkname=$einheiten[$i%6]." ".$ortsnamen[$i%5];
  if(!in_array($funkname,$moegliche_teilnehmer))
    $moegliche_teilnehmer[]=$funkname;
}

$moegliche_befehle=array();

/*
masculinum (m./masc.)   männlich (m./männl.)
femininum (f./fem.)   weiblich (w./weibl.)
neutrum (n./neutr.)   sächlich (s./sächl.) 
*/
$pois=array(
  "alten Feuerwehrhaus"=>"neutrum",
  "neuen Feuerwehrhaus"=>"neutrum",
  "Kirche"=>"femininum",
  "Kaufhaus"=>"neutrum",
  "Gemeindemt"=>"neutrum",
);
$zur=array("masculinum"=>"zum","femininum"=>"zur","neutrum"=>"zum");

foreach($pois as $poi=>$geschlecht) {  
  $moegliche_befehle[]="<b>SRC:</b><br>Befehlen Sie <u>DST</u> <i>im TYPE Funkgespräch</i>, ".$zur[$geschlecht]." ".$poi." zu fahren.";
}

$texte=array();
$texte[]="Fragen Sie %other nach seinem Standort.";
$texte[]="Fragen Sie %other nach der geschätzten Ankunftszeit beim Einsatzort.";
$texte[]="Fragen Sie %other nach dem Fortschritt der Löscharbeiten.";
$texte[]="Fragen Sie %other nach dem Fortschritt der Menschenrettung.";
$texte[]="Fragen Sie %other ob der Brand schon unter Kontrolle ist.";
$texte[]="Fragen Sie %other ob das verunfallte Fahrzeug schon entfernt wurde.";
$texte[]="Fragen Sie %other ob der Notartzt schon eingetroffen ist.";
$texte[]="Fragen Sie %other nach der geschätzten restlichen Einsatzdauer.";
$texte[]="Fragen Sie bei %other nach, ob der Atemschutzkompressor benötigt wird.";
$texte[]="Fragen Sie bei %other nach, ob noch weitere Atemschutzgeräteträger benötigt werden.";
$texte[]="Fragen sie bei %other nach, ob die Arbeiten schon abgeschlossen sind.";
$texte[]="Fragen sie bei %other nach, ob noch Unterstützung benötigt wird.";

$texte[]="Teilen Sie %other mit, dass Sie zum Brandeinsatz in der Pressbaumerstraße ausrücken.";
$texte[]="Teilen Sie %other mit, dass Sie gerade am Bauhof eingetroffen sind.";
$texte[]="Teilen Sie %other mit, dass der aktuelle Brand nur ein Kleinbrand in der Scheune ist. Trotzdem liegt starke Rauchentwicklung vor.";
$texte[]="Teilen Sie %other mit, dass der erste Atemschutztrupp bereits im Einsatz ist und ein weiterer Trupp benötigt wird.";
$texte[]="Teilen Sie %other mit, dass Sie zu ihnen unterwes sind und Fragen Sie nach dem Anfahrtsweg.";
$texte[]="Teilen Sie %other mit, dass zum Brandobjekt nur der Anfahrtsweg von Seite Firma Kern her möglich ist.";

$texte[]="Melden Sie an %other, dass Sie am Einsatzort eingetroffen sind. 
Die eingeklemmte Person konnte sich von selbst befreien. PKW sind ineinander verkeilt. Benzin läuft aus.";
$texte[]="Melden Sie an %other, dass der PKW nach dem Befreien der eingeklemmten Person in Brand geraten ist und 
fordern Sie Unterstützung bei der Brandbekämpfung an.";
$texte[]="Melden Sie an %other, dass sich immer mehr Schaulustige um die Einsatzstelle sammeln und 
fordern Sie Unterstützung durch die Polizei an.";
$texte[]="Melden Sie an %other, dass der Fahrzeugbrand gelöscht ist und Sie die Fahrzeugwracks zum 
Bauhof bringen sowie ausgelaufene Flüssigkeiten binden werden.";
$texte[]="Melden Sie an %other, dass im Feuerwehrhaus Verpflegung bereit steht.";
$texte[]="Melden Sie an %other, dass Tank Sieghartskirchen gegebenenfalls zur Unterstützung bereit steht."; 
$texte[]="Melden sie an %other, dass Exekutive beim Einsatzort benötigt wird und 
keine weiteren Feuerwehrkräfte erforderlich sind. Weiters wird eine Entsorgungsfirma für kontaminiertes Löschwasser benötigt.";
$texte[]="Melden Sie an %other, dass die Straße total gesperrt werden muss, 
weil der Notarzt – Hubschrauber in kürze landet und außerdem soll die Straßenmeisterei zum Unfallort kommen.";

$texte[]="Befehlen Sie %other, dass Sie zur Pressbaumerstraße ".rand(150,350)." fahren sollen. 
Sie werden dort bei einem Brandeinsatz zur Beleuchtung des Einsatzortes benötigt.";
$texte[]="Befehlen Sie %other, den Autoschlüssel des verunfallten PKW zum Polizeiposten Sieghartskirchen zu bringen.";
$texte[]="Befehlen Sie %other, bei Firma Herbert Gutscher 200 kg Ölbindemittel abzuholen und ins Feuerwehrhaus zu bringen.";
$texte[]="Befehlen Sie %other, sich zur Tankstelle Hold zu begeben.";

$texte[]="Fordern Sie bei %other eine weitere TS an.";
$texte[]="Fordern Sie bei %other eine Unterwasserpumpe an.";
$texte[]="Fordern Sie bei %other eine Dreheiter an.";
$texte[]="Fordern Sie bei %other einen weiteren Atemschutztrupp an.";
$texte[]="Fordern Sie bei %other Unterstützung bei der Brandbekämpfung von der Südseite her an.";
$texte[]="Fordern Sie bei %other eine weitere Motorkettensäge an.";
$texte[]="Fordern Sie bei %other Unterstützung durch den Notarzt an. Kamerad Huber hat sich beim Arbeiten mit der Motorkettensäge verletzt.";
$texte[]="Fordern Sie bei %other eine weitere Unterwasserpumpe an.";

$texte[]="Alarmieren Sie %other zu einem Technischer Einsatz: Es soll einen umgestürzten Baum von der B1 beim Gemeindeamt entfernt werden.";
$texte[]="Alarmieren Sie %other zu einem Technischer Einsatz: Hilfe wird beim Entfernen eines umgestürzten Baumes von der B1 beim Gemeindeamt benötigt.";

$texte[]="Informieren sie %other, dass das Wirtschaftsgebäude im Vollbrand steht und der Unterabschnitt 3 zur Verstärkung, 
sowie eine Rettungsorganisation für die ärztliche Versorgung dringend beim Einsatzort benötigt wird.";
$texte[]="Informieren sie %other, dass noch ein Trupp mit hydraulischen Rettungsgerät zur Unterstützung benoetigt wird und 
fragen sie nach ob die Polizei schon unterwegs ist.";

$texte[]="Geben sie eine Lagemeldung an %other: Wirtschaftsgebäude und Maschinenhalle abgebrannt, 
Wohnhaus gerettet, 23 Rinder gerettet, 8 Rinder verendet. Der Einsatz wird noch bis ca. 16:30 Uhr andauern.";
$texte[]="Geben Sie eine Lagemeldung an %other: ".rand(3,4)." eingeklemmte Personen in 2 PKW, 
2 bereits befreit an 3ter Person wird noch gearbeitet. 2 Notarztteams vor Ort. Straße bis auf weiteres gesperrt.";

foreach($texte as $text) {
  $moegliche_befehle[]="<b>SRC:</b><br>".str_replace('%other','<u>DST</u> <i>im TYPE Funkgespräch</i>',$text);
}


?>
