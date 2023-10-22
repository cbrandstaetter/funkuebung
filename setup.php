<?php
include("config.php");
header('Content-Type: text/html; charset=utf-8');
include("menu.php");

if(empty($_GET["anzahl"])) {
  echo '<form>'."\n";
  echo '<table border="1">';
  echo '<tr><th>Anzahl der Teilnehmer</th><th>Start Sprechgruppe</th><th>Start Modus</th></tr>';

  echo '<td>';
  echo '<select name="anzahl">'."\n";
  foreach($moegliche_teilnehmer as $key=>$value) {
    if($key>0) {
      $anzahl=$key+1;
      echo '<option value="'.$anzahl.'">'.$anzahl.' Teilnehmer (Letzter: '.$value.')</option>'."\n";
    }
  }
  echo '</select>'."\n";
  echo '</td>';
  
  echo '<td>';
  echo '<select name="start_kanal">'."\n";
  foreach($sprechgruppen as $sprechgruppe) {
    echo '<option value="'.$sprechgruppe.'">'.$sprechgruppe.'</option>'."\n";
  }
  echo '</select>'."\n";
  echo '</td>';

  echo '<td>';
  echo '<select name="start_modi">'."\n";
  foreach($funkmodis as $funkmodus) {
    echo '<option value="'.$funkmodus.'">'.$funkmodus.'</option>'."\n";
  }
  echo '</select>'."\n";
  echo '</td>';
  
  echo '<td>';
  echo '<input type="submit" value="Übung anlegen">'."\n";
  echo '</td>';

  echo '</tr></table>';
  echo '</form>'."\n";  
}

else {
  $db->executeQuery("DROP TABLE teilnehmer");
  $db->executeQuery("CREATE TABLE teilnehmer (nr inc, name str, src-einfach int, dst-einfach int, src-eingespielt int, dst-eingespielt int)");
  for($i=1;$i<=$_GET["anzahl"];$i++) {
    $sql="INSERT INTO teilnehmer (nr,name) VALUES ('".$i."','".$moegliche_teilnehmer[$i-1]."')";
    $db->executeQuery($sql);
  }

  $db->executeQuery("DROP TABLE befehle");
  $db->executeQuery("CREATE TABLE befehle (nr inc, anzahl int)");
  foreach($moegliche_befehle as $key=>$befehl) {
    $sql="INSERT INTO befehle (nr,anzahl) VALUES ('".$key."',0)";
    $db->executeQuery($sql);
  }

  $db->executeQuery("DROP TABLE status");
  $db->executeQuery("CREATE TABLE status (key str, value str)");

  $sql="INSERT INTO status (key,value) VALUES ('TMO-DMO','".$_GET["start_modi"]."')";
  $db->executeQuery($sql);
  $sql="INSERT INTO status (key,value) VALUES ('Sprechgruppe','".$_GET["start_kanal"]."')";
  $db->executeQuery($sql);

  $sql="INSERT INTO status (key,value) VALUES ('Sprechgruppenwechsel','".time()."')";
  $db->executeQuery($sql);
  
  echo "<br>Übung für ".$_GET["anzahl"]." Teilnehmer erfolgreich angelegt!<br><br>";
  echo '<a href="start.php">mit erstem Befehl starten.</a>';
}
  
?>
