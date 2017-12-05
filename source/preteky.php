<?php

/*
* Trieda PRETEK ktoru pouzijeme na udrzovanie udajov o pretekoch.
*/
class PRETEKY
{
  public $ID;
  public $NAZOV;
  public $DATUM;
  public $DEADLINE;
  public $AKTIV;
  public $POZNAMKA;
  /**
  *Prida udaje objektu preteky do databazy
  */

    public function nacitaj($ID,$NAZOV,$DATUM,$DEADLINE,$AKTIV,$POZNAMKA){
        $this->ID = $ID;
        $this->NAZOV = $NAZOV;
        $this->DATUM = $DATUM;
        $this->DEADLINE = $DEADLINE;
        $this->AKTIV = $AKTIV;
        $this->POZNAMKA = iconv('cp1252', 'UTF-8', html_entity_decode($POZNAMKA, ENT_QUOTES, 'cp1252'));
    }

  public function pridaj_pretek($NAZOV, $DATUM, $DEADLINE, $POZNAMKA){
   $db = napoj_db();
   $NAZOV2 = htmlentities($NAZOV, ENT_QUOTES, 'UTF-8');
   $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
   $text = $POZNAMKA;
   if(preg_match($reg_exUrl, $text, $url) && !strpos($text, "</a>") && !strpos($text, "</A>") && !strpos($text, "HREF") && !strpos($text, "href")) {

       // make the urls hyper links
      $text = preg_replace($reg_exUrl, "<a href=".$url[0].">{$url[0]}</a> ", $text);

}

   $POZNAMKA2 = htmlentities($text, ENT_QUOTES, 'UTF-8');

   $sql =<<<EOF
      INSERT INTO PRETEKY (
         NAZOV,DATUM,DEADLINE,AKTIV,POZNAMKA)
      VALUES ("$NAZOV2", "$DATUM", "$DEADLINE","1","$POZNAMKA2");
EOF;

   $ret = $db->exec($sql);

$sql0 = "SELECT max(id) as bubulak FROM PRETEKY";
    $ret0=$db->query($sql0);
    $row = $ret0->fetchArray(SQLITE3_ASSOC);
    $cislo = $row['bubulak'];

$sql1 =<<<EOF
      CREATE TABLE KATEGORIE_PRE_$cislo
      (ID INTEGER PRIMARY KEY   AUTOINCREMENT,
       NAZOV    TEXT
       );
EOF;
$ret = $db->exec($sql1);

   $db->close();
  }

/**
*upravy pretek v databaze podla aktualneho id objektu preteky
*/
  function uprav_pretek ($NAZOV, $DATUM, $DEADLINE,$POZNAMKA){
  if(!$this->ID){
    return false;
  }
  $NAZOV2 = htmlentities($NAZOV, ENT_QUOTES, 'UTF-8');
  $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
   $text = $POZNAMKA;

   if(preg_match($reg_exUrl, $text, $url) && !strpos($text, "</a>") && !strpos($text, "</A>") && !strpos($text, "HREF") && !strpos($text, "href")) {

       // make the urls hyper links
      $text = preg_replace($reg_exUrl, "<a href=".$url[0].">{$url[0]}</a> ", $text);

}

   $POZNAMKA2 = htmlentities($text, ENT_QUOTES, 'UTF-8');
    $db = napoj_db();
    $sql =<<<EOF
       UPDATE PRETEKY set NAZOV = "$NAZOV2" where ID="$this->ID";
       UPDATE PRETEKY set DATUM = "$DATUM" where ID="$this->ID";
       UPDATE PRETEKY set DEADLINE = "$DEADLINE" where ID="$this->ID";
       UPDATE PRETEKY set POZNAMKA = "$POZNAMKA2" where ID="$this->ID";
       DELETE FROM KATEGORIE_PRE_$this->ID;
EOF;
    $ret = $db->exec($sql);
    if(!$ret){
       echo $db->lastErrorMsg();
    } else {

    }
   $db->close();
  }

/**
*prihlasy pouzivatela na pretek
*/
static function odhlas_z_preteku($ID,$ID_pouz){
  $db = napoj_db();

   $sql =<<<EOF
      DELETE FROM PRIHLASENY WHERE ID_POUZ = "$ID_pouz" AND ID_PRET="$ID";
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {

   }
   $db->close();
}

/**
*odhlasy pouzivatela na pretek
*/
static function prihlas_na_pretek($ID,$ID_pouz,$kat,$poz){
  $db = napoj_db();
    $sql =<<<EOF
      INSERT INTO PRIHLASENY (
         ID_POUZ,ID_PRET,KAT,POZNAMKA)
      VALUES ("$ID_pouz","$ID","$kat","$poz");
EOF;
  setcookie('kat_pretekar'.$ID_pouz, $kat, time() + (86400 * 366),"/");
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
    }
    $db->close();
}


/**
*vrati zoznam pouzivatelov pruhlasenych na pretek s duplicitnym chipom
*/

public function vypis_prihlasenych_d_chip(){
    $db = napoj_db();
    $sql =<<<EOF
           CREATE TABLE temp
      (ID INTEGER NOT NULL,
      MENO              TEXT    NOT NULL,
      PRIEZVISKO        TEXT    NOT NULL,
      OS_I_C            TEXT,
      CHIP              TEXT,
      POZNAMKA          TEXT,
      USPECH            TEXT,
      KAT               TEXT,
      ID_ODDIEL         INTEGER,
      NIC TEXT
      );
EOF;
$db->exec($sql);
$sql =<<<EOF
          INSERT INTO temp(ID, meno, priezvisko, OS_I_C, CHIP, NIC, USPECH, ID_ODDIEL, KAT, POZNAMKA) SELECT POUZIVATELIA.*, PRIHLASENY.KAT, PRIHLASENY.POZNAMKA FROM POUZIVATELIA INNER JOIN PRIHLASENY ON POUZIVATELIA.ID = PRIHLASENY.ID_POUZ  WHERE (PRIHLASENY.ID_PRET = $this->ID);
EOF;
$db->exec($sql);
$sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.CHIP in (SELECT temp.CHIP from temp GROUP BY temp.CHIP HAVING COUNT (temp.CHIP) > 1) GROUP BY temp.ID;
EOF;
$ret = $db->query($sql);
$sql =<<<EOF
         DROP TABLE TEMP;
EOF;






      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
        //echo "<b>".$row['ID'],$row['MENO'],$row['PRIEZVISKO'],$row['OS_I_C'],$row['CHIP'],$row['POZNAMKA']."</b><br>";

        echo "<tr>";
        //echo '<td><input type="checkbox" name="incharge[]" value="'.$row['ID'].'"/></td>';

        echo "<td class='fnt'><strong class=upozornenie>".$row['MENO']."</strong></td>";      //***********************
        echo "<td class='fnt'><strong class=upozornenie>".$row['PRIEZVISKO']."</strong></td>";
        echo "<td class='fnt'>".$row['KAT']."</td>";
        echo "<td class='fnt'>".$row['OS_I_C']."</td>";
        echo "<td class='fnt'><strong class=upozornenie>".$row['CHIP']."</strong></td>";
        echo "<td class='fnt'>".$row['POZNAMKA']."</td>";
        echo "</tr> ";

      }
      // echo "Operation done successfully"."<br>";   ///////////////////
       $db->exec($sql);
       $db->close();
  }
/**
*vrati zoznam pouzivatelov pruhlasenych na pretek s unikatnym chipom
*/
public function vypis_prihlasenych_u_chip(){
    $db = napoj_db();
    $sql =<<<EOF
           CREATE TABLE temp
      (ID INTEGER NOT NULL,
      MENO              TEXT    NOT NULL,
      PRIEZVISKO        TEXT    NOT NULL,
      OS_I_C            TEXT,
      CHIP              TEXT,
      POZNAMKA          TEXT,
      USPECH            TEXT,
      KAT               TEXT,
      ID_ODDIEL         INTEGER,
      NIC TEXT
      );
EOF;
$db->exec($sql);
$sql =<<<EOF
          INSERT INTO temp(ID, meno, priezvisko, OS_I_C, CHIP, NIC, USPECH, ID_ODDIEL, KAT, POZNAMKA ) SELECT POUZIVATELIA.*, PRIHLASENY.KAT, PRIHLASENY.POZNAMKA FROM POUZIVATELIA INNER
           JOIN PRIHLASENY ON POUZIVATELIA.ID = PRIHLASENY.ID_POUZ  WHERE (PRIHLASENY.ID_PRET = $this->ID);
EOF;
$db->exec($sql);
$sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.CHIP in (SELECT temp.CHIP from temp GROUP BY temp.CHIP HAVING COUNT (temp.CHIP) = 1) GROUP BY temp.ID;
EOF;
$ret = $db->query($sql);
$sql =<<<EOF
         DROP TABLE TEMP;
EOF;


      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
        //echo $row['ID'],$row['MENO'],$row['PRIEZVISKO'],$row['OS_I_C'],$row['CHIP'],$row['POZNAMKA']."<br>";
        echo "<tr>";
        //echo '<td><input type="checkbox" name="incharge[]" value="'.$row['ID'].'"/></td>';

        echo "<td>".$row['MENO']."</td>";
        echo "<td>".$row['PRIEZVISKO']."</td>";
        echo "<td>".$row['KAT']."</td>";
        echo "<td>".$row['OS_I_C']."</td>";
        echo "<td>".$row['CHIP']."</td>";
        echo "<td>".$row['POZNAMKA']."</td>";
        echo "</tr> ";

      }
       // echo "Operation done successfully"."<br>";      ////////////////////////////
       $db->exec($sql);
       $db->close();
  }

/**
*vrati zoznam neprihlasenych
*/

public function vypis_neprihlasenych(){
    $db = napoj_db();
    $sql =<<<EOF
           CREATE TABLE temp
      (ID INTEGER NOT NULL,
      MENO              TEXT    NOT NULL,
      PRIEZVISKO        TEXT    NOT NULL,
      OS_I_C            TEXT,
      CHIP              TEXT,
      POZNAMKA          TEXT,
      USPECH            TEXT,
      ID_ODDIEL         INTEGER
      );
EOF;
$db->exec($sql);
    $sql =<<<EOF
        INSERT INTO temp(ID, meno, priezvisko, OS_I_C, CHIP, POZNAMKA, USPECH, ID_ODDIEL) SELECT POUZIVATELIA.* FROM POUZIVATELIA LEFT OUTER JOIN PRIHLASENY ON PRIHLASENY.ID_POUZ = POUZIVATELIA.ID
        WHERE PRIHLASENY.ID is null
        OR (PRIHLASENY.ID_PRET <> $this->ID AND PRIHLASENY.ID_POUZ NOT IN
          (SELECT PRIHLASENY.ID_POUZ FROM PRIHLASENY WHERE ID_PRET = $this->ID));


EOF;
$db->exec($sql);
$sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.ID GROUP BY temp.ID;
EOF;

$ret = $db->query($sql);
$sql =<<<EOF
         SELECT * FROM KATEGORIE_PRE_$this->ID;
EOF;
$result = $db->query($sql);

$sql =<<<EOF
         DROP TABLE TEMP;
EOF;

      if(isset($_COOKIE['posledni_prihlaseni'])){
        $cookiesArray=explode("#",$_COOKIE['posledni_prihlaseni']);
      }
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
        if ((isset($_GET['cookies'])&&!$_GET['cookies'])||(!isset($_COOKIE['posledni_prihlaseni']) || in_array($row['ID'],$cookiesArray))){
        echo "<tr>";
        //echo '<td><input type="checkbox" name="incharge2[]" value="'.$row['ID'].'"/></td>';
        echo '<td><select name="incharge[]">';
        echo '<option value="-">-</option>';
        while($row1 = $result->fetchArray(SQLITE3_ASSOC) ){
          echo '<option value="'.$row1['NAZOV'].':'.$row['ID'].'" ';
          if (isset($_COOKIE['kat_pretekar'.$row['ID']]) && $_COOKIE['kat_pretekar'.$row['ID']]==$row1['NAZOV']){
            echo "selected";
          }
          echo '>'.$row1['NAZOV'].'</option>';

        }
        echo "</select></td>";
        echo "<td><a class='fntb' href='profil.php?id=".$row['ID']."&amp;pr=".$_GET["id"]."'>".$row['MENO']."</a></td>";
        echo "<td><a class='fntb' href='profil.php?id=".$row['ID']."&amp;pr=".$_GET["id"]."'>".$row['PRIEZVISKO']."</a></td>";

        echo "<td>".$row['OS_I_C']."</td>";
        echo "<td>".$row['CHIP']."</td>";
        echo "<td><input type='text' name=poznamka".$row['ID']." size=10 value='";
        if (isset($_POST['poznamka'.$row['ID']])){
          echo $_POST['poznamka'.$row['ID']];
        }else{
          echo $row['POZNAMKA'];
        }
        echo "'></td>";
        echo "<td>
        <a class='fntb' href='uprav.php?id=".$row['ID']."&amp;pr=".$_GET["id"]."'>Uprav</a></td></tr>";

        }
      }
      ?>
        <tr>
          <td><input type="checkbox" name="posli"></td>
          <?php
          echo '<td><select name="kategoria">';
          echo '<option value="-">-</option>';
          while($row1 = $result->fetchArray(SQLITE3_ASSOC) ){
            echo '<option value="'.$row1['NAZOV'].'">'.$row1['NAZOV'].'</option>';
          }
          echo "</select></td>";
          ?>
          <td><input type="text" name="meno" id="meno" size="10" value=""></td>
          <td><input type="text" name="priezvisko" id="priezvisko" size="10" value=""></td>

          <td><input type="text" name="oscislo" id="oscislo" size="10" value=""></td>
          <td><input type="text" name="cip" id="cip" size="10" value=""></td>
          <td><input type="text" name="poznamka" id="poznamka" size="10" value=""></td>
        </tr>
      <?php
      // echo "Operation done successfully"."<br>";       ////////////////////////
      $db->exec($sql);
      $db->close();
}



/**
*nastavy parametre preteku podla zvoleneho id
*/
  static function vrat_pretek ($ID){
    $db = napoj_db();
      $sql =<<<EOF
         SELECT * from Preteky WHERE ID=$ID;
EOF;
$sql1 =<<<EOF
         SELECT * from Preteky WHERE ID=$ID;
EOF;
$count = 0;
if(is_numeric($ID)){
      $ret = $db->query($sql);
      $ret2 = $db->query($sql1);
      $count = $ret2->fetchArray(PDO::FETCH_NUM);
    }
      if($count>0){
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
        $pom = new PRETEKY();
        $pom->nacitaj($ID,$row['NAZOV'],$row['DATUM'],$row['DEADLINE'], $row['AKTIV'], $row['POZNAMKA']);
       }
       // echo "Operation done successfully"."<br>";    //////////////
       $db->close();
       return $pom;
     }
       else{echo'Zvoleny pretek neexistuje';}
  }

/**
*vymaze pretek z DB podla id objektu PRETEKY
*/
  static function vymaz_pretek($ID){
    $db = napoj_db();
    $sql =<<<EOF
       DELETE FROM PRETEKY WHERE ID = $ID;
       DELETE FROM PRIHLASENY WHERE ID_PRET = $ID;
EOF;
$sql1 =<<<EOF
       DROP TABLE KATEGORIE_PRE_$ID
EOF;
     $ret = $db->exec($sql);
     $ret1 = $db->exec($sql1);
    if(!$ret){
       echo $db->lastErrorMsg();
    }
   $db->close();

  }

/**
*Vrati zoznam zoznam pretekov
*/
static function vypis_zoznam(){

   $db = napoj_db();

   $sql =<<<EOF
      SELECT * from Preteky WHERE AKTIV = 1 ORDER BY NAZOV ASC;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    $d1 = $row['DEADLINE'];
    $d2 = $row['DATUM'];
    $d3 = new DateTime(date("Y-m-d H:i:s"));

    if(strtotime($d1) < strtotime('1 days') && strtotime($d1) > strtotime('0 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['ID']."' class='red'>".$row['NAZOV']."</a></td>";
  }
    if(strtotime($d1) < strtotime('0 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['ID']."' class='grey'>".$row['NAZOV']."</a></td>";
  }
  if(strtotime($d1) > strtotime('1 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['ID']."' class='green'>".$row['NAZOV']."</a></td>";
  }
    echo "<td>".$row['DATUM']."</td>";
    echo "<td>".$row['DEADLINE']."</td>";
    //echo "<td><a href='uprav_preteky.php?id=".$row['ID']."'>Uprav</a></td>";
    if(new DateTime($d2) < $d3){
      ?>
        <td><a href='vykon.php?id=<?php echo $row['ID'];?>'>Osobný výkon</a></td>
        <td><a href='zhodnotenie.php?id=<?php echo $row['ID']?>'>Celkové hodnotenie</a></td>
      <?php
    }

    echo "</tr>";
   }
   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}


static function vypis_zoznam_admin(){

   $db = napoj_db();

   $sql =<<<EOF
      SELECT * from Preteky ORDER BY DEADLINE DESC;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    $d1 = $row['DEADLINE'];
    $d2 = $row['DATUM'];
    $d3 = new DateTime(date("Y-m-d H:i:s"));
    if(strtotime($d1) < strtotime('1 days') && strtotime($d1) > strtotime('0 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['ID']."&amp;ad=1' class = 'red'>".$row['NAZOV']."</a></td>";
  }
  if(strtotime($d1) < strtotime('0 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['ID']."&amp;ad=1' class = 'grey'>".$row['NAZOV']."</a></td>";
  }
  if(strtotime($d1) > strtotime('1 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['ID']."&amp;ad=1' class = 'green'>".$row['NAZOV']."</a></td>";
  }
    echo "<td>".$row['DATUM']."</td>";


    if(new DateTime($d2) < $d3){
      ?>
        <td><a href='vykon.php?id=<?php echo $row['ID'];?>'>Osobný výkon</a></td>
        <td><a href='zhodnotenie.php?id=<?php echo $row['ID']?>'>Celkové hodnotenie</a></td>

      <?php
    }
    echo "<td><input type='submit' value='A'></td>";
    echo "<td><input type='submit' value='Cc'></td>";
    echo "<td><input type='submit' value='X'>";
    echo "</tr>";

   }
   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}

static function aktivuj($ID){

   $db = napoj_db();

   $sql =<<<EOF
      UPDATE PRETEKY set AKTIV = "1" where ID="$ID";
EOF;

   $ret = $db->exec($sql);


   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}

static function deaktivuj($ID){

   $db = napoj_db();

   $sql =<<<EOF
      UPDATE PRETEKY set AKTIV = "0" where ID="$ID";
EOF;

   $ret = $db->exec($sql);


   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}

static function vypis_zoznam_oddiely(){

   $db = napoj_db();

   $sql =<<<EOF
      SELECT * from ODDIELY;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){


       echo '<tr><td><input type="radio" name="incharge[]" value="'.$row['id'].'"/></td>';
       echo '<td>'.$row['id'].'</td><td>'.$row['nazov'] ."</td></tr>";

   }
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vypis_zoznam_kategorii(){

   $db = napoj_db();

   $sql =<<<EOF
      SELECT * from KATEGORIE;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){


       echo '<tr><td><input type="radio" name="incharge[]" value="'.$row['ID'].'"/></td>';
       echo '<td>'.$row['ID'].'</td><td>'.$row['NAZOV'] ."</td></tr>";

   }
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vymaz_kategoriu($ID){

   $db = napoj_db();

   $sql =<<<EOF
      DELETE from KATEGORIE WHERE ID = $ID;
EOF;

   $ret = $db->exec($sql);

   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vymaz_oddiel($ID){

   $db = napoj_db();

   $sql =<<<EOF
      DELETE from Oddiely WHERE ID = $ID;
EOF;

   $ret = $db->exec($sql);

   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function pridaj_kategoriu($nazov){
   $db = napoj_db();

   $sql =<<<EOF
      INSERT INTO KATEGORIE (
         NAZOV)
      VALUES ("$nazov");
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   }
   $db->close();
  }

static function pridaj_oddiel($nazov){
   $db = napoj_db();

   $sql =<<<EOF
      INSERT INTO oddiely (
         nazov)
      VALUES ("$nazov");
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   }
   $db->close();
  }

static function vypis_zoznam_kategorii_table(){

   $db = napoj_db();

   $sql =<<<EOF
      SELECT * from KATEGORIE;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){


       //echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['NAZOV'].'"/></td>';
       echo '<td>'.$row['NAZOV'] ."</td></tr>";

   }
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vypis_zoznam_pretek_table(){

   $db = napoj_db();
   $cislo = $_GET['id'];
   $sql =<<<EOF
      SELECT * from KATEGORIE_PRE_$cislo;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){


       //echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['NAZOV'].'" checked/></td>';
       echo '<td>'.$row['NAZOV'] ."</td></tr>";

   }
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vypis_zoznam_ostatne_table(){

   $db = napoj_db();
   //$cislo = $_GET['id'];
   $sql =<<<EOF
      SELECT KATEGORIE.* from KATEGORIE;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){


       //echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['NAZOV'].'"/></td>';
       echo '<td>'.$row['NAZOV'] ."</td></tr>";

   }
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function pridaj_kat_preteku($NAZOV){
  $db = napoj_db();

  $sql0 = "SELECT max(id) as bubulak FROM PRETEKY";
    $ret0=$db->query($sql0);
    $row = $ret0->fetchArray(SQLITE3_ASSOC);
    $cislo = $row['bubulak'];
    $sql =<<<EOF
      INSERT INTO KATEGORIE_PRE_$cislo (
         NAZOV)
      VALUES ("$NAZOV");
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
    }
    else{

    }

    $db->close();
}

static function uprav_kat_preteku($NAZOV){
  $db = napoj_db();
  $cislo = $_GET['id'];


    $sql =<<<EOF
      INSERT INTO KATEGORIE_PRE_$cislo (
         NAZOV)
      VALUES ("$NAZOV");
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
    }
    else{

    }

    $db->close();
}

static function zapis_cas($ID_PRET,$ID_POUZ,$cas){
    $db = napoj_db();
    $sql =<<<EOF
      INSERT INTO ZHODNOTENIE (ID_PRET,ID_POUZ,CAS) VALUES ("$ID_PRET","$ID_POUZ","$cas");
EOF;

  $db->exec($sql);
  $db->close();
}

static function uprav_cas($ID_PRET,$ID,$cas){
    $db = napoj_db();
    $sql =<<<EOF
      UPDATE ZHODNOTENIE set CAS = "$cas" WHERE ID_ZHOD = $ID;
EOF;

  $db->exec($sql);
  $db->close();
}

static function exportuj_zhodnotenie($id){
  $db = napoj_db();
    $sql =<<<EOF
      SELECT * FROM ZHODNOTENIE JOIN POUZIVATELIA ON ZHODNOTENIE.ID_POUZ = POUZIVATELIA.ID JOIN PRIHLASENY ON PRIHLASENY.ID_POUZ=POUZIVATELIA.ID WHERE ZHODNOTENIE.ID_PRET = $id ORDER BY PRIHLASENY.KAT,ZHODNOTENIE.CAS ASC;
EOF;
  $ret = $db->query($sql);
  $myfile = fopen("zhodnotenie.csv", "w") or die("Unable to open file!");
    fputcsv($myfile, array("KATEGORIA","MENO","PRIEZVISKO","CAS"), ";");
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      fputcsv($myfile,array($row['KAT'],$row['MENO'],$row['PRIEZVISKO'],$row['CAS']),";");
    }
  echo '<meta http-equiv="refresh" content="0;URL=zhodnotenie.csv" />';
}

static function vypis_zhodnotenie($ID_PRET){
  $db = napoj_db();
    $sql =<<<EOF
      SELECT * FROM ZHODNOTENIE JOIN PRIHLASENY ON PRIHLASENY.ID_POUZ=ZHODNOTENIE.ID_POUZ AND PRIHLASENY.ID_PRET=ZHODNOTENIE.ID_PRET JOIN POUZIVATELIA ON ZHODNOTENIE.ID_POUZ = POUZIVATELIA.ID WHERE ZHODNOTENIE.ID_PRET = $ID_PRET  ORDER BY PRIHLASENY.KAT,ZHODNOTENIE.CAS ASC;
EOF;

  $ret = $db->query($sql);
  while ($row = $ret->fetchArray(SQLITE3_ASSOC)){
    ?>
    <tr><td><?php echo $row['KAT']?></td><td><?php echo $row["MENO"] ?></td><td><?php echo $row["PRIEZVISKO"] ?></td><td><?php echo $row["CAS"] ?></td></tr>
    <?php
  }
  if (isset($_SESSION["admin"]) && $_SESSION["admin"]){
    echo '<tr><td><form method="post"><input type="submit" name="export" value="Exportovať"></form></td><td></td><td><form method="post"><input type="submit" name="upravuj" value="Uprav"></form></td></tr>';
  }
  $db->close();
}

static function vypis_zhodnotenie_admin($ID_PRET){
  $db = napoj_db();
    $sql =<<<EOF
      SELECT * FROM ZHODNOTENIE JOIN PRIHLASENY ON PRIHLASENY.ID_POUZ=ZHODNOTENIE.ID_POUZ AND PRIHLASENY.ID_PRET=ZHODNOTENIE.ID_PRET JOIN POUZIVATELIA ON ZHODNOTENIE.ID_POUZ = POUZIVATELIA.ID WHERE ZHODNOTENIE.ID_PRET = $ID_PRET  ORDER BY PRIHLASENY.KAT,ZHODNOTENIE.CAS ASC;
EOF;

  $ret = $db->query($sql);
  $i = 0;
  while($row = $ret->fetchArray(SQLITE3_ASSOC)){
    echo "<tr>";

    echo "<td>".$row['KAT']."</td>";

    echo "<td>".$row['MENO']."</td>";

    echo "<td>".$row['PRIEZVISKO']."</td>";

    echo '<td><input type="text" name="cas'.$i.'" value = "';
    if (isset($_POST["cas".$i])){
      echo $_POST["cas".$i];
    } else{
      echo $row["CAS"];
    }
    echo '" required/><input type="hidden" name="id'.$i.'" value="'.$row["ID_ZHOD"].'"/></td>';

    echo "</tr>";

    $i++;
  }
  echo '<tr><td></td><td></td><td><input type="submit" name="uprav" value="Ulož zmeny"><input type="hidden" name="pocet" value="'.$i.'"></td></tr>';
  $db->close();
}

static function odstran_duplicity(){
    $db = napoj_db();
    $sql =<<<EOF
           CREATE TABLE duplicity
      (ID INTEGER PRIMARY KEY   AUTOINCREMENT,
      ID_POUZ              TEXT    NOT NULL,
      ID_PRET        TEXT    NOT NULL,
      KAT        TEXT    NOT NULL,
      POZNAMKA TEXT
      );
EOF;
$db->exec($sql);
$sql =<<<EOF
          INSERT INTO duplicity(ID_POUZ, ID_PRET, KAT,POZNAMKA) SELECT PRIHLASENY.ID_POUZ, PRIHLASENY.ID_PRET, PRIHLASENY.KAT, PRIHLASENY.POZNAMKA FROM PRIHLASENY GROUP BY ID_PRET, ID_POUZ;
EOF;
$db->exec($sql);
$sql =<<<EOF
         DROP TABLE PRIHLASENY;
EOF;
$db->exec($sql);
$sql =<<<EOF
         ALTER TABLE duplicity RENAME to PRIHLASENY;
EOF;
$db->exec($sql);


       // echo "Operation done successfully"."<br>";      ////////////////////////////

       $db->close();
  }

}
 ?>
