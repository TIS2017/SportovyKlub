<?php
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
session_start();

if (isset($_GET['odhlas'])){
  $_SESSION['admin']=0;
}
?>

<!DOCTYPE HTML>

<html>
<?php
if (isset($_SESSION['admin'])&&$_SESSION['admin']){
  hlavicka("Admin");
}else{
  hlavicka("Tréningy ŠK Sandberg");
}
?>

<section id="uprav_preteky">
<?php
$zobraz_form = false;

if (isset($_POST['novy'])){
  $zobraz_form = true;

}
if (isset($_POST['end'])){
  $zobraz_form = false;
}


if ( (isset ($_POST['posli'])) &&

    isset ($_POST['nazov']) &&
    isset ($_POST['datum']) &&
    isset ($_POST['deadline']) )  {



  $p = new PRETEKY();
  $p->pridaj_pretek($_POST['nazov'],$_POST['datum'],$_POST['deadline']);
  echo '<META HTTP-EQUIV="refresh" CONTENT="0">';
  unset($p);


  ?>

  <?php
  echo '<p class="chyba">Upravene!</p>';
}

if ($zobraz_form) {
?>
  <form method="post" enctype="multipart/form-data">
    <input type="submit" name="kopir" value="Nakopírovať tréning">
   <table>
    <?php if(isset($_POST['nazov']) && !over($_POST['nazov'])){echo'<tr><td><font color="red">Nevyplnili ste názov!</font></td></tr>';} ?>
    <tr>

      <td><label for="nazov">Názov pretekov</label></td>
      <td><input type="text" name="nazov" id="nazov" size="30" > </td>
    </tr>
    <?php if(isset($_POST['datum']) && !over($_POST['datum'])){echo'<tr><td><font color="red">Nevyplnili ste dátum!</font></td></tr>';} ?>
    <tr>

      <td><label for="datetimepicker">Dátum konania</label>  </td>
      <td><input type="text" name="datum" id="datetimepicker" size="30" ></td>
    </tr>
    <?php if(isset($_POST['deadline']) && !over($_POST['deadline'])){echo'<tr><td><font color="red">Nevyplnili ste deadline!</font></td></tr>';} ?>
    <tr>

      <td><label for="datetimepicker1">Deadline prihlásenia</label>    </td>
      <td><input type="text" name="deadline" id="datetimepicker1" size="30"> </td>
    </tr>
    <tr>
       <td> <label for="poznamka">Poznámka</label> </td>
       <td> <textarea cols="80" rows="15" name="poznamka" id="poznamka"></textarea></td>

    </tr>
   </table>
   <table><?php
   if(isset($_POST['posli'])&&!isset($_POST['incharge'])){echo'<tr><td><font color="red">Musíte zadať aspoň jednu kategóriu!</font></td></tr>';}
   PRETEKY::vypis_zoznam_ostatne_table(); ?></table>

   <p id="buttons">
   <input type="submit" name="posli" value="Pridaj">
   <input type="submit" name="end" value="Koniec">
   <br><br>
   </p>
  </form>
  <?php
  }
  ?>
</section>

<div id="zoz_pretekov_uzivatel">
    <h2>Archív tréningov</h2>

    <table border="1" style="width:100%;">
      <tr>
        <td class="prvy">Typ tréningu</td>
        <td class="prvy">Dátum konania</td>

<?php if(isset($_SESSION['admin'])&&$_SESSION['admin']){?>
        <td class="prvy"></td>
      </tr>
      <?php PRETEKY::vypis_zoznam_admin();?>
    </table>

</div>

<script src="js/jquery.js"></script>
  <script src="js/jquery.datetimepicker.js"></script>
  <script>
  $('#datetimepicker1').datetimepicker({
  dayOfWeekStart : 1,
  format:'d-m-Y H:i',
  lang:'sk'
  });


  </script>
  <script>
  $('#datetimepicker2').datetimepicker({
  dayOfWeekStart : 1,
  format:'d-m-Y H:i',
  lang:'sk',
  showAnim: "show"
  });


  </script>

<?php }else{?>
      </tr>
      <?php PRETEKY::vypis_zoznam();?>
    </table>
</div>
<br><br>
<?php
}
paticka();
?>



</html>
