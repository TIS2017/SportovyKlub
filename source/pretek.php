<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
$navodik = false;
if(isset($_POST['export'])){
  vypis_db();?>
  <meta http-equiv="refresh" content="0;URL=zoznam.txt" />
  <?php

}

if (isset($_POST['prihlas'])&&isset($_POST['incharge']))
{

    // PHP throws a fit if we try to loop a non-array
    if(is_array($_POST['incharge']))
    {
         $cookies="";
         foreach($_POST['incharge'] as $val)
         {

            if($val!="-"){
              if ($cookies!=""){$cookies.="#";}

              $pieces = explode(":", $val);
              $i=$pieces[1];
              $j=$pieces[0];
              $cookies.=$i;
              if (isset($_POST['poznamka'.$i])){
                $poznamka=$_POST['poznamka'.$i];
              }else{
                $poznamka="";
              }
              PRETEKY::prihlas_na_pretek($_GET["id"], $i, $j,$poznamka);
            }
        }
        setcookie("posledni_prihlaseni", $cookies, time() + (86400 * 366),"/");
    }
}


if (isset($_POST['odhlas']))
{
    // PHP throws a fit if we try to loop a non-array
    if(is_array($_POST['incharge']))
    {
         foreach($_POST['incharge'] as $val)
         {
            //echo $val . '<br />';
            PRETEKY::odhlas_z_preteku($_GET["id"], $val);

        }
    }
}

if (isset($_POST['del']))
{
    // PHP throws a fit if we try to loop a non-array
    if(is_array($_POST['incharge2']))
    {
         foreach($_POST['incharge2'] as $val)
         {
            //echo $val . '<br />';
            $po = new POUZIVATELIA();
            $po->vymaz_pouzivatela($val);
            unset($po);
        }
    }
}

$po = new POUZIVATELIA();

if (isset ($_POST['posli'])&&over($_POST['meno'])&&over($_POST['priezvisko'])){

   $id_novy=$po->pridaj_pouzivatela ($_POST['meno'], $_POST['priezvisko'],"", $_POST['oscislo'], $_POST['cip'], $_POST['poznamka'],"");
   if ($id_novy>-1 && isset($_POST['kategoria']) && $_POST['kategoria']!='-'){
    PRETEKY::prihlas_na_pretek($_GET["id"], $id_novy,$_POST['kategoria'],$_POST['poznamka']);
    if (isset($_COOKIE['posledni_prihlaseni'])){setcookie("posledni_prihlaseni", $_COOKIE['posledni_prihlaseni']."#".$id_novy, time() + (86400 * 366),"/");}
    else{setcookie("posledni_prihlaseni", $id_novy, time() + (86400 * 366),"/");}
  }
  unset($po);
}

if(isset($_POST['navodik'])){
  $navodik = true;


}
if(isset($_POST['skry'])){
  $navodik = false;


}

?>
<!DOCTYPE HTML>
<html>
<script type="text/javascript" src="sorter/jquery-latest.js"></script>
<script type="text/javascript" src="sorter/jquery.tablesorter.js"></script>
<script type="text/javascript">
  $(document).ready(function()
  {
    $("#myTable").tablesorter({sortList: [[2,0]]});
  }
);
  </script>
<script type="text/javascript">
  $(document).ready(function()
  {
    $("#myTable2").tablesorter();
  }
);
  </script>
<?php
  $pr = new PRETEKY();
  $pr=PRETEKY::vrat_pretek($_GET["id"]);
  hlavicka($pr->NAZOV);
  unset($pr);
?>
<section id=pretekSection>


    <?php
    // vypis detailu preteku
    $pr = new PRETEKY();
    $pr=PRETEKY::vrat_pretek($_GET["id"]); ?>

    <form method="post">
    <?php if(!$navodik){ ?>
   <div id="navod"> <input name="navodik" type="submit" id="navodik" value="Návod"> </div>
   <?php }else { ?>
   <div id="skry"> <input name="skry" type="submit" id="skry" value="Skryť"> </div><br>
   <?php } ?>
<?php
if($navodik){
?>
      <div id="navod">
     <table class="n">
        <tr><td style="font-weight:bold;">Návod<BR></td></tr>
        <tr><td>
          V ľavom stĺpci je zoznam prihlásených, v pravom si vyberiete koho prihlasujete.
          <br>
          Ak si vás počítač zapamätal, tak tam máte tých, čo ste prihlasovali minule.
          <br>
          Ak sa nenájdete v pravom stĺpci, stlačte VIAC POUŽÍVATEĽOV, zoznam sa rozbalí.
          <br>
          Ak sa ani tak nenájdete (ste tu prvý raz), vyplňte položky v prázdnom
          riadku, zakliknite štvorček a stlačte PRIHLÁSIŤ NA TRÉNING.
          <br>
          Ak potrebujete poradiť, alebo sa chcete prihlásiť mailom, napíšte na adresu balogh@elf.stuba.sk
        </td></tr>
     </table>
  </div>
  <?php } if(over($pr->POZNAMKA)){ ?>
  <br>
  <div id="upozornenie">
     <table class="u">
        <tr><td style="font-weight:bold;">Pokyny</td></tr>
        <tr><td><?php echo $pr->POZNAMKA; ?></td></tr>
     </table>
  </div>
<?php } ?>

  <div id="prihlaseny">
     <h2>Zoznam prihlásených</h2>
     <p><input name="odhlas" type="submit" id="odhlas" value="Odhlásiť z tréningu"></p> <br>
    <table id="myTable" class="tablesorter" border="1" >

      <col class="col3" >
      <col class="col10" >
      <col class="col14" >
      <col class="col15" >
      <col class="col15" >
      <col class="col15" >
      <col class="col14" >

      <thead>
      <tr>


        <th class="prvy">Meno</th>
        <th class="prvy" id="priezvisko_button">Priezvisko</th>
        <th class="prvy">Kategória</th>
        <th class="prvy">Osobné číslo</th>
        <th class="prvy">Čip</th>
        <th class="prvy">Poznámka</th>

      </tr>
      </thead>
      <tbody>
        <?php
        PRETEKY::odstran_duplicity();
        $pr->vypis_prihlasenych_d_chip();
        $pr->vypis_prihlasenych_u_chip();
        ?>
        </tbody>
    </table>

<br>
    <form>
      <h1>Forma exportu</h1>
      <table>
        <tr>
        <th>Zvoliť</th><th>Stĺpec</th><th>Poradie</th>
      </tr>
      <tr>
        <td><input type="checkbox" name="stlpec1" value="meno"></td><td>Meno</td>
        <td><select>
          <option value="1">1.</option>
          <option value="2">2.</option>
          <option value="3">3.</option>
          <option value="4">4.</option>
          <option value="5">5.</option>
          <option value="6">6.</option>
        </select></td> <br>
      </tr>
      <tr>
        <td><input type="checkbox" name="stlpec1" value="priezvisko"></td> <td>Priezvisko</td>
        <td><select>
          <option value="1">1.</option>
          <option value="2">2.</option>
          <option value="3">3.</option>
          <option value="4">4.</option>
          <option value="5">5.</option>
          <option value="6">6.</option>
        </select></td> <br>
      </tr>
      <tr>
        <td><input type="checkbox" name="stlpec1" value="meno"></td><td>Kategória</td>
       <td><select>
          <option value="1">1.</option>
          <option value="2">2.</option>
          <option value="3">-</option>
          <option value="4">4.</option>
          <option value="5">5.</option>
          <option value="6">6</option>
        </select></td> <br>
        </tr>
        <tr>
        <td><input type="checkbox" name="stlpec1" value="meno"></td><td>Osobné číslo</td>
        <td><select>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3.</option>
          <option value="4">-</option>
          <option value="5">5</option>
          <option value="6">6</option>
        </select><td><br>
      </tr>
      <tr>
        <td><input type="checkbox" name="stlpec1" value="meno"></td> <td>Čip</td>
        <td><select>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3.</option>
          <option value="4">4.</option>
          <option value="5">5.</option>
          <option value="6">6</option>
        </select></td> <br>
      </tr>
      <tr>
        <td><input type="checkbox" name="stlpec1" value="meno"></td> <td>Poznámka</td>
        <td><select>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4.</option>
          <option value="5">5.</option>
          <option value="6">6.</option>
        </select></td>
      </tr>
      </table>
    </form>

    <?php if(isset($_SESSION['admin'])&&$_SESSION['admin'] ==1 ){ ?>
    <p><input name="export" type="submit" id="export" value="Export do súboru"></p>
  <?php }?>
    <br> <br>   <br>
  </div>

    <!--</form>-->

    <!--<form method="post">-->

  <div id="odhlaseny">
  <?php
  $d1 = new DateTime($pr->DEADLINE);
    $d2 = new DateTime(date("Y-m-d H:i:s"));
   if((isset($_SESSION['admin'])&&$_SESSION['admin']==1) || $d1 > $d2){ ?>
  <h2>Neprihlásení používatelia</h2>

   <?php
    $d1 = new DateTime($pr->DEADLINE);
    $d2 = new DateTime(date("Y-m-d H:i:s"));
    if((isset($_SESSION['admin'])&&$_SESSION['admin']==1)||(isset($pr->AKTIV)&&$pr->AKTIV==1&&isset($pr->DEADLINE))&&$d1>$d2){ echo''; ?>
        <input name="prihlas" type="submit" id="prihlas" value="Prihlásiť na tréning">

        <?php } else if($d1<$d2){echo "Prihlasovanie na tento tréning bolo ukončené</p> <p>";}

    if (!isset($_GET['cookies'])){?>
      <?php $link="'pretek.php?id=".$_GET['id']."&cookies=0'"; ?>
      <input onclick="window.location.href =<?php echo $link;?>" type='button' value='Viac používateľov'>
    <?php }else{?>
      <?php $link="'pretek.php?id=".$_GET['id']."'"; ?>
      <input onclick="window.location.href =<?php echo $link;?>" type='button' value='Menej používateľov'>
    <?php
    }
    ?>

    <!--</form> -->

    <!--<form method="post">     -->
    <?php if (isset($_SESSION['admin'])&&$_SESSION['admin']){?>
    <input name="del" type="submit" id="del" onclick="return confirm('Naozaj chcete vymazať používateľov?');" value="Vymazať používateľa"><br> <!-- aj v admine kde su vsetci pouzivatelia-->
    <?php
    }
    ?>

  <?php } else{echo "<p>Prihlasovanie na tento trening bolo uzatvorene</p>";} ?>
<br>
    <table  id="myTable2" class="tablesorter" border="1">

      <col class="col10" >
      <col class="col15" >
      <col class="col14" >
      <col class="col15" >
      <col class="col15" >
      <col class="col15" >
      <col class="col9" >
      <col class="col3" >
      <thead>
      <tr>


        <th class="prvy"></th>
        <th class="prvy">Kategória</th>
        <th class="prvy">Meno</th>
        <th class="prvy">Priezvisko</th>

        <th class="prvy">Osobné číslo</th>
        <th class="prvy">Čip</th>
        <th class="prvy">Poznámka</th>
        <th class="prvy"></th>
        <th class="prvy"></th>

      </tr>
      </thead>
      <tbody>
        <?php

        //$pr->vypis_neprihlasenych();

        ?>
      </tbody>
    </table>

</div>
<br><br>


</form>

    <?php
    unset($pr);
    paticka();
    ?>


<?php



//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
?>


</section>
</html>
