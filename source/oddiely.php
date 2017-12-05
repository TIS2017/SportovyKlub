<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');


?>
<!DOCTYPE HTML>

<html>

<?php
if (!isset($_SESSION['admin']) || !$_SESSION['admin']){
  echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
}else{
hlavicka("Oddiely");
?> 
  
<section>
 
<div id="tab_platby">  
    <form method="post">
      <h2>Oddiely</h2>
      <table border="1" style="width:100%">
          <tr>
            <td class="prvy"></td>
            <td class="prvy">ID oddielu</td>
            <td class="prvy">Názov</td>
            
          </tr>
          
             <?php
      $pl = new PRETEKY();
      PRETEKY::vypis_zoznam_oddiely();
      //unset($pl);
      ?>
          
      </table>
      <p>

        <input name="novy" type="submit" id="novy" value="Nový oddiel">
        <!--<input name="edit" type="submit" id="edit" value="Upravit platbu"> -->
        <input name="del" type="submit" id="del" onclick="return confirm('Naozaj chcete vymazať oddiel?');" value="Vymazať oddiel"> 
      </p>
      
    </form>
    <br><br> <br> <br>  
</div>


    <br>
<?php 

$zobraz_form = false;



if ((isset($_POST['del']) && (isset($_POST['incharge'])) ))
{
    // PHP throws a fit if we try to loop a non-array
    if(is_array($_POST['incharge']))
    {
         foreach($_POST['incharge'] as $val)     // id platby
         {
            PRETEKY::vymaz_oddiel($val);
            //echo $val . '<br />';
            //echo $val."<br>";
            echo '<META HTTP-EQUIV="refresh" CONTENT="0">';
            
        }
    }
}

if (isset($_POST['novy']) || isset($_POST['posli'])) 
{
    
    
         
  $zobraz_form = true;
            
  //echo '<META HTTP-EQUIV="refresh" CONTENT="0">';
            
      
    
}
/*$pom = 0;
if ((isset($_POST['edit']) && (isset($_POST['incharge'])) ))
{
    // PHP throws a fit if we try to loop a non-array
    if(is_array($_POST['incharge']))
    {
         foreach($_POST['incharge'] as $val)     // id platby
         {
            //PLATBY::vymaz_platbu($val);
            
            $pom = $val;
            $zobraz_form = true;
            //echo $pom . '<br />';
            
            //echo $val."<br>";
            //echo '<META HTTP-EQUIV="refresh" CONTENT="0">';
            
        }
    }
}*/
?> 

<?php

if ((isset($_POST['posli'])) &&
    (over ($_POST['nazov']))) {
    
     PRETEKY::pridaj_oddiel($_POST['nazov']);
     echo '<META HTTP-EQUIV="refresh" CONTENT="0">';
    }
    
if (isset($_POST['cancel'])) {
  $zobraz_form = false;
}
    
    
if ($zobraz_form) {
  
?>
<div id="novy_pouzivatel">
	<form method="post" enctype="multipart/form-data">
  <h2>Pridať oddiel</h2>
	<table>
    <?php if(isset($_POST['nazov']) && !over($_POST['nazov'])){echo'<tr><td><font color="red">Nevyplnili ste názov!</font></td></tr>';} ?>
    <tr>
      
		  <td><label for="nazov">Názov:</label></td>
		  <td><input type="text" name="nazov" id="nazov" size="30" value="<?php if(isset($_POST['nazov'])){echo $_POST['nazov'];} ?>"></td>
		</tr>
    
  </table>
		           
	  	<p id="buttons">
        <input type="submit" name="posli" value="Pridaj">
        <input type="submit" name="cancel" value="Koniec">
        
		  </p>
        
  </form>
</div>
<?php } ?>
  </section>
  
    
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
  
<?php
paticka();
}
?>
</html>