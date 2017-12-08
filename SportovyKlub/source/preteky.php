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
      INSERT INTO Preteky (
         nazov,datum,deadline,aktiv,poznamka)
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
       UPDATE Preteky set nazov = "$nazov2" where id="$this->ID";
       UPDATE Preteky set datum = "$DATUM" where id="$this->ID";
       UPDATE Preteky set deadline = "$DEADLINE" where id="$this->ID";
       UPDATE Preteky set poznamka = "$POZNAMKA2" where id="$this->ID";
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
      DELETE FROM Prihlaseni WHERE id_pouz = "$ID_pouz" AND id_pret="$ID";
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
      INSERT INTO Prihlaseni (
         id_pouz,id_pret,KAT,POZNAMKA)
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

        echo "<td class='fnt'><strong class=upozornenie>".$row['meno']."</strong></td>";      //***********************
        echo "<td class='fnt'><strong class=upozornenie>".$row['priezvisko']."</strong></td>";
        echo "<td class='fnt'>".$row['id_kat']."</td>";
        echo "<td class='fnt'>".$row['os_i_c']."</td>";
        echo "<td class='fnt'><strong class=upozornenie>".$row['cip']."</strong></td>";
        echo "<td class='fnt'>".$row['poznamka']."</td>";
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

        echo "<td>".$row['meno']."</td>";
        echo "<td>".$row['priezvisko']."</td>";
        echo "<td>".$row['id_kat']."</td>";
        echo "<td>".$row['os_i_c']."</td>";
        echo "<td>".$row['cip']."</td>";
        echo "<td>".$row['poznamka']."</td>";
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
        if ((isset($_GET['cookies'])&&!$_GET['cookies'])||(!isset($_COOKIE['posledni_prihlaseni']) || in_array($row['id'],$cookiesArray))){
        echo "<tr>";
        //echo '<td><input type="checkbox" name="incharge2[]" value="'.$row['ID'].'"/></td>';
        echo '<td><select name="incharge[]">';
        echo '<option value="-">-</option>';
        while($row1 = $result->fetchArray(SQLITE3_ASSOC) ){
          echo '<option value="'.$row1['nazov'].':'.$row['id'].'" ';
          if (isset($_COOKIE['kat_pretekar'.$row['id']]) && $_COOKIE['kat_pretekar'.$row['id']]==$row1['nazov']){
            echo "selected";
          }
          echo '>'.$row1['nazov'].'</option>';

        }
        echo "</select></td>";
        echo "<td><a class='fntb' href='profil.php?id=".$row['id']."&amp;pr=".$_GET["id"]."'>".$row['meno']."</a></td>";
        echo "<td><a class='fntb' href='profil.php?id=".$row['ID']."&amp;pr=".$_GET["id"]."'>".$row['priezvisko']."</a></td>";

        echo "<td>".$row['os_i_c']."</td>";
        echo "<td>".$row['cip']."</td>";
        echo "<td><input type='text' name=poznamka".$row['id']." size=10 value='";
        if (isset($_POST['poznamka'.$row['id']])){
          echo $_POST['poznamka'.$row['id']];
        }else{
          echo $row['poznamka'];
        }
        echo "'></td>";
        echo "<td>
        <a class='fntb' href='uprav.php?id=".$row['id']."&amp;pr=".$_GET["id"]."'>Uprav</a></td></tr>";

        }
      }
      ?>
        <tr>
          <td><input type="checkbox" name="posli"></td>
          <?php
          echo '<td><select name="kategoria">';
          echo '<option value="-">-</option>';
          while($row1 = $result->fetchArray(SQLITE3_ASSOC) ){
            echo '<option value="'.$row1['nazov'].'">'.$row1['nazov'].'</option>';
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
         SELECT * from Preteky WHERE id=$ID;
EOF;
$sql1 =<<<EOF
         SELECT * from Preteky WHERE id=$ID;
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
        $pom->nacitaj($ID,$row['nazov'],$row['datum'],$row['deadline'], $row['aktiv'], $row['poznamka']);
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
       DELETE FROM Preteky WHERE id = $ID;
       DELETE FROM Prihlaseni WHERE id_pret = $ID;
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
      SELECT * from Preteky WHERE aktiv = 1 ORDER BY nazov ASC;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    $d1 = $row['deadline'];
    $d2 = $row['datum'];
    $d3 = new DateTime(date("Y-m-d H:i:s"));

    if(strtotime($d1) < strtotime('1 days') && strtotime($d1) > strtotime('0 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['id']."' class='red'>".$row['nazov']."</a></td>";
  }
    if(strtotime($d1) < strtotime('0 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['id']."' class='grey'>".$row['nazov']."</a></td>";
  }
  if(strtotime($d1) > strtotime('1 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['id']."' class='green'>".$row['nazov']."</a></td>";
  }
    echo "<td>".$row['datum']."</td>";
    echo "<td>".$row['deadline']."</td>";
    //echo "<td><a href='uprav_preteky.php?id=".$row['ID']."'>Uprav</a></td>";
    if(new DateTime($d2) < $d3){
      ?>
        <td><a href='vykon.php?id=<?php echo $row['id'];?>'>Osobný výkon</a></td>
        <td><a href='zhodnotenie.php?id=<?php echo $row['id']?>'>Celkové hodnotenie</a></td>
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
      SELECT * from Preteky ORDER BY deadline DESC;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    $d1 = $row['deadline'];
    $d2 = $row['datum'];
    $d3 = new DateTime(date("Y-m-d H:i:s"));
    if(strtotime($d1) < strtotime('1 days') && strtotime($d1) > strtotime('0 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['id']."&amp;ad=1' class = 'red'>".$row['nazov']."</a></td>";
  }
  if(strtotime($d1) < strtotime('0 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['id']."&amp;ad=1' class = 'grey'>".$row['nazov']."</a></td>";
  }
  if(strtotime($d1) > strtotime('1 days')){
    echo "<tr><td><a href='pretek.php?id=".$row['id']."&amp;ad=1' class = 'green'>".$row['nazov']."</a></td>";
  }
    echo "<td>".$row['datum']."</td>";


    if(new DateTime($d2) < $d3){
      ?>
        <td><a href='vykon.php?id=<?php echo $row['id'];?>'>Osobný výkon</a></td>
        <td><a href='zhodnotenie.php?id=<?php echo $row['id']?>'>Celkové hodnotenie</a></td>

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
      UPDATE Preteky set aktiv = "1" where id="$ID";
EOF;

   $ret = $db->exec($sql);


   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}

static function deaktivuj($ID){

   $db = napoj_db();

   $sql =<<<EOF
      UPDATE Preteky set aktiv = "0" where id="$ID";
EOF;

   $ret = $db->exec($sql);


   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}

static function vypis_zoznam_oddiely(){

   $db = napoj_db();

   $sql =<<<EOF
      SELECT * from Oddiely;
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
      SELECT * from Kategorie;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){


       echo '<tr><td><input type="radio" name="incharge[]" value="'.$row['id'].'"/></td>';
       echo '<td>'.$row['id'].'</td><td>'.$row['nazov'] ."</td></tr>";

   }
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vymaz_kategoriu($ID){

   $db = napoj_db();

   $sql =<<<EOF
      DELETE from Kategorie WHERE id = $ID;
EOF;

   $ret = $db->exec($sql);

   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vymaz_oddiel($ID){

   $db = napoj_db();

   $sql =<<<EOF
      DELETE from Oddiely WHERE id = $ID;
EOF;

   $ret = $db->exec($sql);

   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function pridaj_kategoriu($nazov){
   $db = napoj_db();

   $sql =<<<EOF
      INSERT INTO Kategorie (nazov)
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
      INSERT INTO Oddiely (nazov)
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
      SELECT * from Kategorie;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){


       //echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['nazov'].'"/></td>';
       echo '<td>'.$row['nazov'] ."</td></tr>";

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


       //echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['nazov'].'" checked/></td>';
       echo '<td>'.$row['nazov'] ."</td></tr>";

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


       //echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['nazov'].'"/></td>';
       echo '<td>'.$row['nazov'] ."</td></tr>";

   }
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function pridaj_kat_preteku($nazov){
  $db = napoj_db();

  $sql0 = "SELECT max(id) as bubulak FROM PRETEKY";
    $ret0=$db->query($sql0);
    $row = $ret0->fetchArray(SQLITE3_ASSOC);
    $cislo = $row['bubulak'];
    $sql =<<<EOF
      INSERT INTO KATEGORIE_PRE_$cislo (
         nazov)
      VALUES ("$nazov");
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
    }
    else{

    }

    $db->close();
}

static function uprav_kat_preteku($nazov){
  $db = napoj_db();
  $cislo = $_GET['id'];


    $sql =<<<EOF
      INSERT INTO KATEGORIE_PRE_$cislo (
         nazov)
      VALUES ("$nazov");
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
      INSERT INTO Zhodnotenie (id_pret,id_pouz,cas) VALUES ("$ID_PRET","$ID_POUZ","$cas");
EOF;

  $db->exec($sql);
  $db->close();
}

static function uprav_cas($ID_PRET,$ID,$cas){
    $db = napoj_db();
    $sql =<<<EOF
      UPDATE Zhodnotenie set cas = "$cas" WHERE id = $ID;
EOF;

  $db->exec($sql);
  $db->close();
}

static function exportuj_zhodnotenie($id){
  $db = napoj_db();
    $sql =<<<EOF
      SELECT * FROM Zhodnotenie JOIN Pouzivatelia ON Zhodnotenie.id_pouz = Pouzivatelia.id JOIN Prihlaseni ON Prihlaseni.id_pouz=Pouzivatelia.id WHERE Zhodnotenie.id_pret = $id ORDER BY Prihlaseni.id_kat,Zhodnotenie.cas ASC;
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
      SELECT * FROM Zhodnotenie JOIN Prihlaseni ON Prihlaseni.id_pouz=Zhodnotenie.id_pouz AND Prihlaseni.id_pret=Zhodnotenie.id_pret JOIN Pouzivatelia ON Zhodnotenie.id_pouz = Pouzivatelia.id WHERE Zhodnotenie.id_pret = $ID_PRET  ORDER BY Prihlaseni.id_kat,Zhodnotenie.cas ASC;
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
      SELECT * FROM Zhodnotenie JOIN Prihlaseni ON Prihlaseni.id_pouz=Zhodnotenie.id_pouz AND Prihlaseni.id_pret=Zhodnotenie.id_pret JOIN Pouzivatelia ON Zhodnotenie.id_pouz = Pouzivatelia.id WHERE Zhodnotenie.id_pret = $ID_PRET  ORDER BY Prihlaseni.id_kat,Zhodnotenie.cas ASC;
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
      (id INTEGER PRIMARY KEY   AUTOINCREMENT,
      id_pouz             INTEGER    NOT NULL,
      id_pret        INTEGER    NOT NULL,
      id_kat        INTEGER    NOT NULL,
      poznamka TEXT
      );
EOF;
$db->exec($sql);
$sql =<<<EOF
          INSERT INTO duplicity(id_pouz, id_pret, id_kat,poznamka) SELECT Prihlaseni.id_pouz, Prihlaseni.id_pret, Prihlaseni.id_kat, Prihlaseni.poznamka FROM Prihlaseni GROUP BY id_pret, id_pouz;
EOF;
$db->exec($sql);
$sql =<<<EOF
         DROP TABLE Prihlaseni;
EOF;
$db->exec($sql);
$sql =<<<EOF
         ALTER TABLE duplicity RENAME to Prihlaseni;
EOF;
$db->exec($sql);


       // echo "Operation done successfully"."<br>";      ////////////////////////////

       $db->close();
  }

}
 ?>