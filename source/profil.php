<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');

$po = new POUZIVATELIA();
$po = POUZIVATELIA::vrat_pouzivatela($_GET["id"]);


if(isset($_POST['posli3'])){
  vymaz_obrazok($_GET['id']);
  pridaj_obrazok($_GET['id']);

}
if(isset($_POST['vymaz'])){

  vymaz_obrazok($_GET['id']);

}

?>
<!DOCTYPE HTML>

<html>

<?php hlavicka("Upraviť údaje používateľa -  ".$po->meno." ".$po->priezvisko);?>
<script src="thumbnailviewer.js" type="text/javascript"></script>  
<section id="uprav"><div id="profil">
<div id="foto">
  <form method="post" enctype="multipart/form-data">
<?php zobraz_obrazok($_GET['id']);

 
 ?>
</form>
</div>
<div id="obsah">
<?php 
$po = new POUZIVATELIA();
  $po = POUZIVATELIA::vrat_pouzivatela($_GET["id"]);
echo $po->meno; 
echo" ";
echo $po->priezvisko;
echo"<br>";
echo $po->uspech;

unset($po);

?>
</div>
</div>
  
</section>
<br><br> 
<?php 

unset($po);
paticka();
?>

</html>