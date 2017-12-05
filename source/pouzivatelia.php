<?php



class POUZIVATELIA
{  
  public $id;
  public $meno;
  public $prezvisko;
  public $oddiel;
  public $os_i_c;
  public $chip;
  public $poznamka;
  public $uspech;

  function __construct(){


  }

  function nacitaj($id, $meno,$prezvisko,$oddiel,$os_i_c,$chip,$poznamka,$uspech){
    $this->id = $id;
    $this->meno = $meno;   ///////////////////
    $this->priezvisko = $prezvisko;
    $this->oddiel = $oddiel;
    $this->os_i_c = $os_i_c;
    $this->chip = $chip;
    $this->poznamka = $poznamka;
    $this->uspech = stripslashes($uspech);
  }

  function pridaj_pouzivatela($meno,$prezvisko,$oddiel,$os_i_c,$chip,$poznamka,$uspech){
    $meno2 = $meno;
    $prezvisko2 = $prezvisko;
    $os_i_c2 = $os_i_c;
    $chip2 = strtoupper($chip);
    $poznamka2 = $poznamka;
    $uspech2 = htmlentities($uspech, ENT_QUOTES, "UTF-8");

    $db = napoj_db();
    
   $sql =<<<EOF
      INSERT INTO POUZIVATELIA (
         MENO,PRIEZVISKO,ID_ODDIEL,OS_I_C,CHIP,POZNAMKA,USPECH)
      VALUES ("$meno2", "$prezvisko2","$oddiel", "$os_i_c2", "$chip2", "$poznamka2","$uspech2");
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
      $ret=-1;
   } else {
      $ret=$db->lastInsertRowID();
   }
   $db->close();
   return $ret;

  }


  static function vrat_pouzivatela($ID){
    $db = napoj_db();
    $sql =<<<EOF
       SELECT * FROM POUZIVATELIA WHERE ID = $ID;
EOF;
$sql1 =<<<EOF
         SELECT * from POUZIVATELIA WHERE ID=$ID;
EOF;
$count = 0;
if(is_numeric($ID)){
      $ret = $db->query($sql);
      $ret2 = $db->query($sql1);
      $count = $ret2->fetchArray(PDO::FETCH_NUM);
    }
    if($count>0){
      //echo "Records got successfully\n";
      
      while($row = $ret->fetchArray(SQLITE3_ASSOC)){
        $p = new self();
        $p->nacitaj($row['ID'], $row['MENO'], $row['PRIEZVISKO'],$row['ID_ODDIEL'], $row['OS_I_C'], $row['CHIP'], $row['POZNAMKA'], $row['USPECH']);
        return $p;
      }
      $db->close();
   }else{

    echo'Zvoleny pouzivatel neexistuje';
   }
   
    
  }
  
  




static function vypis_zoznam(){
  $db = napoj_db();

   $sql =<<<EOF
      SELECT * from POUZIVATELIA LEFT JOIN oddiely ON POUZIVATELIA.id_oddiel=oddiely.id ORDER BY oddiely.id IS NULL, oddiely.nazov;;
EOF;
   $akt_oddiel=".";
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      if ($akt_oddiel!=$row['nazov']){
        $akt_oddiel=$row['nazov'];
        if($akt_oddiel==""){
          echo "<h2 class=ZC_nadpis>Bez oddielu</h2>";
        } else {
          echo "<h2 class=ZC_nadpis>".$akt_oddiel."</h2>";
        }
      }
      POUZIVATELIA::vypis_profil($row);        
   }
   //echo "Operation done successfully"."<br>";
   $db->close();

  }
  
static function vypis_profil($pouz){
  ?>
  <div class="profil_ram" border=1>
    <p><strong><?php echo $pouz['MENO']." ".$pouz['PRIEZVISKO']?></strong></p>
    <div class="foto_ram">
    <?php
      if(file_exists('pictures/'.$pouz['ID'].'.gif')){
        $subor = 'pictures/' . $pouz['ID'] .'.gif';
      }else if(file_exists('pictures/'.$pouz['ID'].'.png')){
        $subor = 'pictures/' . $pouz['ID'] .'.png';
      }else if(file_exists('pictures/'.$pouz['ID'].'.jpg')){
        $subor = 'pictures/' . $pouz['ID'] .'.jpg';
      }else if(file_exists('pictures/'.$pouz['ID'].'.jpeg')){
        $subor = 'pictures/' . $pouz['ID'] .'.jpeg';
      }else {
        $subor = 'pictures/no_photo.jpg';
      } 
    ?>
      <a href="<?php echo $subor;?>"><img src="<?php echo $subor;?>"></a>
    </div>
    
    <?php
    if ($pouz['nazov']!=""){
      echo "<p>Oddiel: ".$pouz['nazov']."</p>";
    }?>
    <p>Osobné identifikačné číslo: <?php echo $pouz['OS_I_C']?></p>
    <p>Čip: <?php echo $pouz['OS_I_C']?></p>
    <p>Poznámka: <?php echo $pouz['POZNAMKA']?></p>
    <p>Úspechy: <?php echo $pouz['USPECH']?></p>
    <p><a href="tabulka_vykonov.php?id=<?php echo $pouz['ID']?>">Osobné výkony</a></p>
    <p><a href="uprav.php?id=<?php echo $pouz['ID']?>">Uprav</a></p>
    <?php
    if (isset($_SESSION['admin'])&&$_SESSION['admin']){
    ?>
      <form method=post>
        <input type=hidden name='id_user' value='<?php echo $pouz['ID']?>'>
        <input type=submit id="del" name='del' value="Vymaž člena" onclick="return confirm('Naozaj chcete vymazať používateľa?');">
      </form>
    <?php
    }
    ?>
  </div>
  <?php  
}

static function vymaz_pouzivatela($ID){
  $db = napoj_db();
    $sql =<<<EOF
       DELETE FROM POUZIVATELIA WHERE ID = $ID;
       DELETE FROM PRIHLASENY WHERE ID_POUZ = $ID;
       DELETE FROM PLATBY WHERE ID_POUZ = $ID;
EOF;
    $ret = $db->exec($sql);
    if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      vymaz_obrazok($ID);
      //echo "Records removed successfully\n";
   }
   $db->close();
  

}

function uprav_pouzivatela ($MENO, $PRIEZVISKO, $oddiel, $OS_I_C, $CHIP, $POZNAMKA, $uspech){
    $db = napoj_db();
    $MENO2 = $MENO;
    $PRIEZVISKO2 = $PRIEZVISKO;
    $OS_I_C2 = $OS_I_C;
    $CHIP2 = strtoupper($CHIP);
    $POZNAMKA2 = $POZNAMKA;
    $uspech2 = htmlentities($uspech, ENT_QUOTES, "UTF-8");
    $sql =<<<EOF
       UPDATE POUZIVATELIA set MENO = "$MENO2" where ID="$this->id";
       UPDATE POUZIVATELIA set PRIEZVISKO = "$PRIEZVISKO2" where ID="$this->id";
       UPDATE POUZIVATELIA set ID_ODDIEL = "$oddiel" where ID="$this->id";
       UPDATE POUZIVATELIA set OS_I_C = "$OS_I_C2" where ID="$this->id";
       UPDATE POUZIVATELIA set CHIP = "$CHIP2" where ID="$this->id";
       UPDATE POUZIVATELIA set POZNAMKA = "$POZNAMKA2" where ID="$this->id";
       UPDATE POUZIVATELIA set USPECH = "$uspech2" where ID="$this->id";
EOF;
    $ret = $db->exec($sql);
    if(!$ret){
       echo $db->lastErrorMsg();
    } else {
       //echo $db->changes(), " Record updated successfully\n";
    }
   $db->close();
  }








}
 ?>