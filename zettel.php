<?php
include("config.php");
header('Content-Type: text/html; charset=utf-8');
//include("menu.php");

$count=0;
echo '<p style="page-break-inside: avoid;">';
echo '<table border=0 cellpadding="15">';
echo '<tr>';
foreach($moegliche_teilnehmer as $nr=>$name) {
  if($count>1 AND $count%2==0) 
    echo '</tr></table></p><p style="page-break-inside: avoid;"><table border=0 cellpadding="15"><tr>';
  echo '<td>';
  echo '<table border="1" cellpadding="1" cellspacing="0"><tr><td align="center">';
  echo str_repeat("&nbsp;",90)."<br><br>\n";
  echo "<font size=+2>Du bist</font>";
  echo "<br><br><br>";
  echo "<font size=+3>".$name."</font><br><br><br>\n";
  echo "<p align=right>Karte Nr. ".($nr+1)."</p>";
  echo '</td></tr></table>';
  echo '</td>';
  $count++;
}
echo '</tr>';
echo '</table>';
echo '</p>';

  
?>
