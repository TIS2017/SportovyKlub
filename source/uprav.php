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

$zobraz_form = true;

if (isset ($_POST['posli2']) )  { 
  $po->vymaz_pouzivatela($_GET['id']);
  echo '<meta http-equiv="refresh" content="0; URL=index.php">';
  //unset($po);
  
  ?>
  
  <?php
  echo '<p class="chyba">Vymazane!</p>';
  $zobraz_form = false;  
}

if ((isset ($_POST['posli'])) && 
    
    over ($_POST['meno'])  &&
    over ($_POST['priezvisko']) )  { 

  $po->uprav_pouzivatela ($_POST['meno'], $_POST['priezvisko'], $_POST['oddiel'], $_POST['oscislo'], $_POST['cip'], $_POST['poznamka'], $_POST['uspech']);

 
  unset($po);
  $po = new POUZIVATELIA();
  $po = POUZIVATELIA::vrat_pouzivatela($_GET["id"]);
  ?>
  
  <?php

    
}

?>
<!DOCTYPE HTML>

<html>

<?php hlavicka("Upraviť údaje používateľa -  ".$po->meno." ".$po->priezvisko);?>

  
<script src="thumbnailviewer.js" type="text/javascript"></script>  
<section id="uprav"><div id="profil">
<div id="foto">
  <form method="post" enctype="multipart/form-data">
<?php zobraz_obrazok($_GET['id']); ?>

</form>
</div>
</div>



  
<?php 
 
if ($zobraz_form) {
?>
<div id="f">
	<form method="post" enctype="multipart/form-data">
    <table>
      <?php if(isset($_POST['meno']) && !over($_POST['meno'])){echo'<tr><td><font color="red">Nevyplnili ste meno!</font></td></tr>';} ?>
    <tr>
		    <td><label for="meno">Meno</label></td>
		    <td><input type="text" name="meno" id="meno" size="30" value="<?php if(isset($_POST['meno'])){echo $_POST['meno'];}else{echo $po->meno;} ?>"></td>
		</tr>
    <?php if(isset($_POST['priezvisko']) && !over($_POST['priezvisko'])){echo'<tr><td><font color="red">Nevyplnili ste priezvisko!</font></td></tr>';} ?>
    <tr>
        <td><label for="priezvisko">Priezvisko</label></td>
		    <td><input type="text" name="priezvisko" id="priezvisko" size="30" value="<?php if(isset($_POST['priezvisko'])){echo $_POST['priezvisko'];}else{echo $po->priezvisko;} ?>"></td>
		</tr>
    <tr>
      <td><label for="oddiel">Oddiel</label></td>
      <td><select name="oddiel">
      <option value="">-</option>
      <?php
      $db = napoj_db();
      $sql =<<<EOF
         SELECT * FROM oddiely;
EOF;
      $result = $db->query($sql);
      while($row1 = $result->fetchArray(SQLITE3_ASSOC) ){
        echo '<option value="'.$row1['id'].'"';
        if (isset($_POST['oddiel'])){
          if ($_POST['oddiel']==$row1['id']){
            echo 'selected';
          }  
        }else if ($po->oddiel==$row1['id']){
          echo 'selected';
        }
        echo '>'.$row1['nazov'].'</option>';

      }
      ?>
      </select></td>
    </tr>
    <tr>
		    <td><label for="oscislo">Osobné číslo</label> </td>
		    <td><input type="text" name="oscislo" id="oscislo" size="30" value="<?php if(isset($_POST['oscislo'])){echo $_POST['oscislo'];}else{echo $po->os_i_c;} ?>"> </td>
		</tr>
    <tr>
        <td><label for="cip">Čip</label> </td>
		    <td><input type="text" name="cip" id="cip" size="30" value="<?php if(isset($_POST['cip'])){echo $_POST['cip'];}else{echo $po->chip;} ?>"></td>
		</tr>
    <tr>
        <td><label for="poznamka">Poznámka</label></td>
		    <td><input type="text" name="poznamka" id="poznamka" size="30" value="<?php if(isset($_POST['poznamka'])){echo $_POST['poznamka'];}else{echo $po->poznamka;} ?>"> </td>
		</tr>
    <tr>
       <td> <label for="uspech">Úspechy</label> </td>
       <td> <textarea cols="27" rows="5" name="uspech" id="uspech"><?php if(isset($_POST['uspech'])){echo $_POST['uspech'];}else{echo $po->uspech;} ?></textarea></td>
	     	
		</tr>	
  </table>
  
	  <p id="buttons">
      <input type="submit" name="posli" value="Upraviť"> 
      <input type="submit" name="posli2" value="Vymazať" onclick="return confirm('Naozaj chcete vymazať používateľa?');"> 
     
		</p>
      
  </form>
</div>
</section>
<br><br> 
<?php } 
unset($pt);
paticka();
?> 




</html>