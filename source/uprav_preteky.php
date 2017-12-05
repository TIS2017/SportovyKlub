<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');

if (!isset($_SESSION['admin']) || !$_SESSION['admin']){
  echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
}else{

$zobraz_form = true;
if(isset($_POST['aktivuj'])){
  PRETEKY::aktivuj($_GET['id']);

}
if(isset($_POST['deaktivuj'])){
  PRETEKY::deaktivuj($_GET['id']);

}

if ((isset ($_POST['posli'])) &&

    over($_POST['nazov']) &&
    over($_POST['datum']) &&
    over($_POST['deadline'])&&
    is_array($_POST['incharge'])
    )  {

  //$po->uprav_pretek ($_POST['meno'], $_POST['priezvisko'], $_POST['oscislo'], $_POST['cip'], $_POST['poznamka']);
  $po = new PRETEKY();
  $po = PRETEKY::vrat_pretek($_GET["id"]);
  $po->uprav_pretek($_POST['nazov'], $_POST['datum'], $_POST['deadline'], $_POST['poznamka']);
  if(is_array($_POST['incharge']))
    {
         foreach($_POST['incharge'] as $val)
         {
            //echo $val . '<br />';
            PRETEKY::uprav_kat_preteku($val);

        }

    }
  unset($po);
  echo '<meta http-equiv="refresh" content="0; URL=index.php">';

}
if (isset($_POST['zmaz']) ){
  $zobraz_form = false;
  $po = new PRETEKY();
  PRETEKY::vymaz_pretek($_GET['id']);
  unset($po);
  echo '<p class="chyba">Vymazane!</p>';
  echo '<meta http-equiv="refresh" content="0; URL=index.php">';

}

?>
<!DOCTYPE HTML>

<html>
<?php
    $po = new PRETEKY();
  $po = PRETEKY::vrat_pretek($_GET["id"]);
  hlavicka("Upraviť preteky ".$po->ID." - ".$po->NAZOV);
  unset($po);
    ?>


<section id="uprav_preteky">
<?php



if ($zobraz_form) {
  $po = new PRETEKY();
  $po = PRETEKY::vrat_pretek($_GET["id"]);
?>
	<form method="post" enctype="multipart/form-data">
	 <table>
    <?php if(isset($_POST['nazov']) && !over($_POST['nazov'])){echo'<tr><td><font color="red">Nevyplnili ste názov!</font></td></tr>';} ?>
		<tr>

      <td><label for="nazov">Názov pretekov</label></td>
		  <td><input type="text" name="nazov" id="nazov" size="30" value="<?php if(isset($_POST['nazov'])){echo $_POST['nazov'];}else {echo $po->NAZOV;} ?>"> </td>
		</tr>
    <?php if(isset($_POST['datum']) && !over($_POST['datum'])){echo'<tr><td><font color="red">Nevyplnili ste dátum!</font></td></tr>';} ?>
    <tr>

		  <td><label for="datetimepicker">Dátum konania</label>  </td>
		  <td><input type="text" name="datum" id="datetimepicker" size="30" value="<?php if(isset($_POST['datum'])){echo $_POST['datum'];}else{echo $po->DATUM;} ?>"></td>
		</tr>
    <?php if(isset($_POST['deadline']) && !over($_POST['deadline'])){echo'<tr><td><font color="red">Nevyplnili ste deadline!</font></td></tr>';} ?>
    <tr>

      <td><label for="datetimepicker1">Deadline prihlásenia</label>    </td>
		  <td><input type="text" name="deadline" id="datetimepicker1" size="30" value="<?php if(isset($_POST['deadline'])){echo $_POST['deadline'];}else{echo $po->DEADLINE;} ?>"> </td>
		</tr>
    <tr>
       <td> <label for="poznamka">Poznámka</label> </td>
       <td> <textarea cols="80" rows="15" name="poznamka" id="poznamka"><?php if(isset($_POST['poznamka'])){echo $_POST['poznamka'];}else{echo $po->POZNAMKA;} ?></textarea></td>

    </tr>
	 </table>
   <table><?php
   if(isset($_POST['posli'])&&!isset($_POST['incharge'])){echo'<tr><td><font color="red">Musíte zadať aspoň jednu kategóriu!</font></td></tr>';}
   PRETEKY::vypis_zoznam_pretek_table(); PRETEKY::vypis_zoznam_ostatne_table(); ?></table>
   <p id="buttons">
   <input type="submit" name="posli" value="Uprav">
   <input type="submit" name="zmaz" value="Vymaž" onclick="return confirm('Naozaj chcete vymazať preteky?');"">

   <br>
   <?php
   if(isset($po->AKTIV) && $po->AKTIV == 1){
    echo'Pretek je aktivny<br>';
    echo'<input type="submit" name="deaktivuj" value="Deaktivuj">';
   }
   else
  if(!isset($po->AKTIV) || $po->AKTIV == 0){
    echo'Pretek nie je aktivny<br>';
    echo'<input type="submit" name="aktivuj" value="Aktivuj">';
   }

   ?>
   <br>
   <br>
   <br>
   </p>
  </form>

  <?php
  unset($po);
     } ?>

</section>

<br><br><br>
<footer>
  <div id="footer">TIS - projekt 2014, Registračný systém pre športový klub</div>
</footer>
<script src="js/jquery.js"></script>
  <script src="js/jquery.datetimepicker.js"></script>
  <script>
  $('#datetimepicker').datetimepicker({
  dayOfWeekStart : 1,
  format:'d-m-Y H:i',
  lang:'sk',
  showAnim: "show"
  });


  </script>
  <script>
  $('#datetimepicker1').datetimepicker({
  dayOfWeekStart : 1,
  format:'d-m-Y H:i',
  lang:'sk',
  showAnim: "show"
  });


  </script>

<?php
paticka();
}
?>


</html>
