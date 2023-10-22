<?php
include("config.php");
header('Content-Type: text/html; charset=utf-8');
include("menu.php");

$talk=get_random_talk($db);
//echo '<pre>'.print_r($talk,true).'</pre>';

$src_teilnemer=get_teilnehmer($db,$talk["src"]);
$dst_teilnemer=get_teilnehmer($db,$talk["dst"]);

$sql="UPDATE teilnehmer SET src-".$talk["type"]." = ".($src_teilnemer["src-".$talk["type"]]+1)." WHERE nr=".$talk["src"];
$db->executeQuery($sql);
$sql="UPDATE teilnehmer SET dst-".$talk["type"]." = ".($dst_teilnemer["dst-".$talk["type"]]+1)." WHERE nr=".$talk["dst"];
$db->executeQuery($sql);


$befehl_statistics=get_befehl_statistics($db);

do {
  $r_befehl=array_rand($moegliche_befehle);
  $anz=get_befehl_count($db,$r_befehl);
} while($anz>$befehl_statistics["min-anzahl"]);
$befehl=$moegliche_befehle[$r_befehl];
$befehl=str_replace("SRC",$src_teilnemer["name"],$befehl);
$befehl=str_replace("DST",$dst_teilnemer["name"],$befehl);
$befehl=str_replace("TYPE",$talk["type"]."en",$befehl);

$sprechgruppenwechsel=get_status($db,"Sprechgruppenwechsel");
$sprechgruppendauer=time()-$sprechgruppenwechsel;

echo '<table border="0" cellpadding="15" width="100%">';
if($sprechgruppendauer>120 AND rand(1,1+floor(1000/$sprechgruppendauer))==1) {
  echo '<tr>';
  cell_kanalwechsel($db);
  cell_stats($db,$talk,2,$sprechgruppendauer);
  echo '</tr><tr>';
  cell_befehl($befehl);
  echo '</tr>';
}
else {
  echo '<tr>';
  cell_befehl($befehl);
  cell_stats($db,$talk,1,$sprechgruppendauer);
  echo '</tr>';  
}
echo '</table>';

$sql="UPDATE befehle SET anzahl=".($anz+1)." WHERE nr=".$r_befehl;
$db->executeQuery($sql);
show_befehl_stats($db,$r_befehl);


// ==================== functions ====================

function cell_kanalwechsel($db) {
  global $sprechgruppen;
  $aktueller_kanal=get_status($db,"Sprechgruppe");
  $aktueller_ordner=strtok($aktueller_kanal,"/");
  $aktuelle_gruppe=strtok("/");
  do {
    $r=array_rand($sprechgruppen);
    $neuer_kanal=$sprechgruppen[$r];
  } while($aktueller_kanal==$neuer_kanal);
  
  $neuer_ordner=strtok($neuer_kanal,"/");
  $neuer_gruppe=strtok("/");

  if($aktueller_ordner==$neuer_ordner)
    $befehl="im aktuellen Ordner von <b>".str_replace(" ","&nbsp;",$aktuelle_gruppe)."</b> auf <b>".str_replace(" ","&nbsp;",$neuer_gruppe)."</b>";
  else
    $befehl="von <b>".str_replace(" ","&nbsp;",$aktueller_kanal)."</b> auf <b>".str_replace(" ","&nbsp;",$neuer_kanal)."</b>";
    
  $sql="UPDATE status SET value='".$neuer_kanal."' WHERE key='Sprechgruppe'";
  $resultSet=$db->executeQuery($sql);

  $sql="UPDATE status SET value='".time()."' WHERE key='Sprechgruppenwechsel'";
  $resultSet=$db->executeQuery($sql);
  
  echo '<td bgcolor=LightCoral valign="top">';
  echo "<font size=+5 face=Arial,Helvetica><u>Sprechgruppenwechsel</u>: ".$befehl."</font>";
  echo '</td>';
}
  
function cell_befehl($befehl) {
  echo '<td bgcolor=PaleGreen valign="top">';
  echo "<font size=+5 face=Arial,Helvetica>".$befehl."<font>";
  echo '</td>';
}

function cell_stats($db,$talk,$rowspan=1,$sprechgruppendauer=0) {
  echo '<td width="1" valign="top" rowspan="'.$rowspan.'">';
  show_stats($db,$talk);
  //echo "Dauer in aktueller Sprechgruppe: ".$sprechgruppendauer." sec.<br>";
  //echo "rand 1,".(1+floor(1000/$sprechgruppendauer));
  echo '</td>';
}

function get_status($db,$key) {
  $sql="SELECT value FROM status WHERE key='".$key."'";
  $resultSet=$db->executeQuery($sql);
  if($resultSet->getRowCount()<1)
    return false;
  while($resultSet->next()) {
    $value=$resultSet->getCurrentValueByName("value");
  }
  return $value;  
}

function get_teilnehmer($db,$nr) {
  $sql="SELECT * FROM teilnehmer WHERE nr=".$nr;
  $resultSet=$db->executeQuery($sql);
  if($resultSet->getRowCount()<1)
    return false;
  while($resultSet->next()) {
    $arr=$resultSet->getCurrentValuesAsHash();
  }
  return $arr;
}

function get_befehl_count($db,$nr) {
  $sql="SELECT * FROM befehle WHERE nr=".$nr;
  $resultSet=$db->executeQuery($sql);
  if($resultSet->getRowCount()<1)
    return false;
  while($resultSet->next()) {
    $anz=$resultSet->getCurrentValueByName("anzahl");
  }
  return $anz;
}

function get_statistics($db) {
  $sql="SELECT ";
  foreach(array("src","dst") as $direction) {
    foreach(array("einfach","eingespielt") as $type) {
      $sql.="min(".$direction."-".$type.") as min-".$direction."-".$type.", ";
      $sql.="max(".$direction."-".$type.") as max-".$direction."-".$type.", ";
      $sql.="sum(".$direction."-".$type.") as sum-".$direction."-".$type.", ";
    }
  }
  $sql.="FROM teilnehmer";
  //echo $sql;
  $resultSet=$db->executeQuery($sql);
  if($resultSet->getRowCount()<1)
    return false;
  while($resultSet->next()) {
    $arr=$resultSet->getCurrentValuesAsHash();
  }
  return $arr;
}

function get_befehl_statistics($db) {
  $sql="SELECT min(anzahl) as min-anzahl FROM befehle";
  //echo $sql;
  $resultSet=$db->executeQuery($sql);
  if($resultSet->getRowCount()<1)
    return false;
  while($resultSet->next()) {
    $arr=$resultSet->getCurrentValuesAsHash();
  }
  return $arr;
}

function get_random_talk($db) {
  $alle_teilnehmer=array();
  $sql="SELECT nr FROM teilnehmer ORDER BY nr";
  $resultSet=$db->executeQuery($sql);
  while($resultSet->next()) {
    $alle_teilnehmer[]=$resultSet->getCurrentValueByName("nr");
  }
  $statistics=get_statistics($db);
  
  // type suchen
  if($statistics["min-src-einfach"]<1 OR $statistics["min-dst-einfach"]<1)
    $type="einfach";
  elseif($statistics["sum-src-einfach"]+$statistics["sum-dst-einfach"]<$statistics["sum-src-eingespielt"]+$statistics["sum-dst-eingespielt"])
    $type="einfach";
  elseif($statistics["sum-src-einfach"]+$statistics["sum-dst-einfach"]>$statistics["sum-src-eingespielt"]+$statistics["sum-dst-eingespielt"])
    $type="eingespielt";
  else {
    $typen=array("einfach","eingespielt");
    $r=array_rand($typen);
    $type=$typen[$r]; 
  }

  // src suchen
  do {
    $r=array_rand($alle_teilnehmer);
    $src=$alle_teilnehmer[$r];
    $src_teilnemer=get_teilnehmer($db,$src);
  } while($src_teilnemer["src-".$type]>$statistics["min-src-".$type]);
  
  // dst suchen
  $count=0;
  do
   {
    $r=array_rand($alle_teilnehmer);
    $dst=$alle_teilnehmer[$r];
    $dst_teilnemer=get_teilnehmer($db,$dst);
    $count++;
  } while($dst==$src OR $dst_teilnemer["dst-".$type]>$statistics["min-dst-".$type]+$count/100);
  
  return array("src"=>$src,"dst"=>$dst,"type"=>$type);
}


function show_stats($db,$hilight=array()) {
  $sql="SELECT * FROM teilnehmer ORDER BY nr";
  $resultSet=$db->executeQuery($sql);
  echo '<table border=1 cellpadding="1" cellspacing="0">';
  echo '<tr><th rowspan="2">Nr.</th><th rowspan="2">Name</th><th colspan="2">einfach</th><th colspan="2">eingesp.</th></tr>';
  echo '<tr><th>Src</th><th>Dst</th><th>Src</th><th>Dst</th></tr>';
  $spalten=$resultSet->getColumnNames();
  while($resultSet->next()) {
    echo '<tr>';
    foreach($spalten as $spalte) {
      $bgcolor="white";
      if(!empty($hilight["src"]) AND !empty($hilight["type"]) AND $spalte=="src-".$hilight["type"] AND $hilight["src"]==$resultSet->getCurrentValueByName("nr"))
        $bgcolor="LimeGreen";
      if(!empty($hilight["dst"]) AND !empty($hilight["type"]) AND $spalte=="dst-".$hilight["type"] AND $hilight["dst"]==$resultSet->getCurrentValueByName("nr"))
        $bgcolor="LightCoral";
      $align="center";
      if($spalte=="nr")
        $align="right";
      if($spalte=="name")
        $align="left";
	  $text=$resultSet->getCurrentValueByName($spalte);
	  if($spalte=="name")
	    $text=str_replace(" ","<br>",$text);
      else
		$text=str_replace(" ","&nbsp;",$text);
      echo '<td bgcolor="'.$bgcolor.'" align="'.$align.'">'.$text."</td>\n";
    }
    echo '</tr>';
  }
  echo '</table>';
}

function show_befehl_stats($db,$hilight=-1) {
  $sql="SELECT * FROM befehle ORDER BY nr";
  $resultSet=$db->executeQuery($sql);
  echo '<table border=1 cellpadding="1" cellspacing="0">';
  echo '<tr><th>Befehle:</th>';
  while($resultSet->next()) {
    $bgcolor="white";
    if($resultSet->getCurrentValueByName("nr")==$hilight)
      $bgcolor="Orange";
    echo '<td bgcolor="'.$bgcolor.'" align="'.$align.'">'.str_replace(" ","&nbsp;",$resultSet->getCurrentValueByName("anzahl"))."</td>\n";
  }
  echo '</tr>';
  echo '</table>';
}

?>