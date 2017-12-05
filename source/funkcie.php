<?php
$heslo="olympiada";
date_default_timezone_set('UTC');
class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('database.db');
      }
   }

function napoj_db(){

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
      return false;
   } else {
      //echo "Opened database successfully<br>"; ///////////////////////////////////
      return $db;
   }
}

function vypis_db(){
    $db = napoj_db();
    $sql =<<<EOF
           CREATE TABLE temp
      (ID INTEGER NOT NULL,
      MENO              VARCHAR    NOT NULL,
      PRIEZVISKO        VARCHAR    NOT NULL,
      OS_I_C            VARCHAR,
      CHIP              INT,
      POZNAMKA          VARCHAR,
      USPECH            VARCHAR,
      oddiel            INTEGER
      );
EOF;
$id=$_GET['id'];
$db->exec($sql);
$sql =<<<EOF
          INSERT INTO temp(ID, MENO, PRIEZVISKO, OS_I_C, CHIP, POZNAMKA, USPECH,oddiel) SELECT POUZIVATELIA.* FROM POUZIVATELIA INNER JOIN PRIHLASENY ON POUZIVATELIA.ID = PRIHLASENY.ID_POUZ  WHERE (PRIHLASENY.ID_PRET = $id);
EOF;
$db->exec($sql);
$sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.CHIP in (SELECT temp.CHIP from temp GROUP BY temp.CHIP HAVING COUNT (temp.CHIP) > 1);
EOF;
$ret = $db->query($sql);


    $myfile = fopen("zoznam.txt", "w") or die("Unable to open file!");
//    fputcsv($myfile, array("MENO","PRIEZVISKO","OSOBNE CISLO","CIP","POZNAMKA"), ";");
//    Changed by RB @ Sep 19 2017: new format for IS SZOS:
    fputcsv($myfile, array("OSOBNE CISLO","Kategoria?","CIP","PRIEZVISKO","MENO","POZNAMKA"), ";");
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      fputcsv($myfile,array($row['OS_I_C'],$row['CHIP'],$row['CHIP'],$row['PRIEZVISKO'],$row['MENO'],$row['POZNAMKA']),";");
    }
      $sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.CHIP in (SELECT temp.CHIP from temp GROUP BY temp.CHIP HAVING COUNT (temp.CHIP) = 1);
EOF;

$ret = $db->query($sql);
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
        fputcsv($myfile,array($row['MENO'],$row['PRIEZVISKO'],$row['OS_I_C'],$row['CHIP'],$row['POZNAMKA']),";");
    }
       // echo "Operation done successfully"."<br>";      ////////////////////////////
      $sql =<<<EOF
         DROP TABLE TEMP;
EOF;

       $db->exec($sql);
       fclose($myfile);
       $db->close();
  }





function Vytvor_tab(){

   $db = napoj_db();

   $sql =<<<EOF
      CREATE TABLE PRETEKY
      (ID INTEGER PRIMARY KEY   AUTOINCREMENT,
      NAZOV           VARCHAR    NOT NULL,
      DATUM            DATETIME     NOT NULL,
      DEADLINE        DATETIME);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Table created successfully<br>";
   }
   $db->close();
}


function Vytvor_tab_pouz(){

   $db = napoj_db();

   $sql =<<<EOF
      CREATE TABLE POUZIVATELIA
      (ID INTEGER PRIMARY KEY   AUTOINCREMENT,
      MENO              VARCHAR    NOT NULL,
      PRIEZVISKO        VARCHAR    NOT NULL,
      OS_I_C            VARCHAR,
      CHIP              INT,
      POZNAMKA          VARCHAR);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Table created successfully<br>";
   }
   $db->close();
}

function Vytvor_tab_uspech(){

   $db = napoj_db();

   $sql =<<<EOF
      CREATE TABLE USPECHY
      (ID INTEGER PRIMARY KEY   AUTOINCREMENT,
      ID_POUZ  INTEGER,
      POPIS        VARCHAR    NOT NULL,
      FOREIGN KEY(ID_POUZ) REFERENCES POUZIVATELIA(ID));
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Table created successfully<br>";
   }
   $db->close();
}


function  Pridaj_do_tab(){

   $db = napoj_db();

   $sql =<<<EOF
      INSERT INTO PRETEKY (
         NAZOV,DATUM,DEADLINE)
      VALUES ('BEH KOKOTOV PRE LEZBIZKE UCELI', 06/06/1999, '06/26/1982 00:00:00');
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Records created successfully"."<br>";
   }
   $db->close();
}

function Select_z_tab(){

   $db = napoj_db();

   $sql =<<<EOF
      SELECT * from Preteky;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      echo "ID = ". $row['ID'] ."<br>";
      echo "NAZOV = ". $row['NAZOV'] ."<br>";
      echo "DATUM = ". $row['DATUM'] ."<br>";
      echo "DEADLINE =  ".$row['DEADLINE'] ."<br><br>";
   }
   // echo "Operation done successfully"."<br>";  ////////////////////////////
   $db->close();
}

function Vytvor_tab_pretekov(){

   $db = napoj_db();

   $sql =<<<EOF
      CREATE TABLE PRETEKY
      (ID INTEGER PRIMARY KEY   AUTOINCREMENT,
      NAZOV           VARCHAR    NOT NULL,
      DATUM            DATETIME     NOT NULL,
      DEADLINE        DATETIME);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Table created successfully<br>";
   }
   $db->close();
}
//skontorluj dlzky strinku backslase

function Vytvor_tab_pri(){

   $db = napoj_db();

   $sql =<<<EOF
      CREATE TABLE PRIHLASENY
      (ID INTEGER PRIMARY KEY   AUTOINCREMENT,
      ID_POUZ  INT  NOT NULL,
      ID_PRET  INT  NOT NULL,
      FOREIGN KEY(ID_POUZ) REFERENCES POUZIVATELIA(ID),
      FOREIGN KEY(ID_PRET) REFERENCES PRETEKY(ID));
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Table created successfully<br>";
   }
   $db->close();
}


function vymaz_obrazok($id){
if(file_exists('pictures/'.$id.'.gif')){
        $subor = 'pictures/' . $id .'.gif';
         unlink($subor);
        }
    if(file_exists('pictures/'.$id.'.png')){
        $subor = 'pictures/' . $id .'.png';
         unlink($subor);
        }
    if(file_exists('pictures/'.$id.'.jpg')){
        $subor = 'pictures/' . $id .'.jpg';
         unlink($subor);
        }
    if(file_exists('pictures/'.$id.'.jpeg')){
        $subor = 'pictures/' . $id .'.jpeg';
         unlink($subor);
        }


}


function pridaj_obrazok($id){
if(($_FILES['obrazok']['type'] != 'image/png') && ($_FILES['obrazok']['type'] != 'image/jpg') && ($_FILES['obrazok']['type'] != 'image/jpeg')
&& ($_FILES['obrazok']['type'] != 'image/gif') && ($_FILES['obrazok']['type'] != 'image/bmp')) { }else{

  $type = $_FILES['obrazok']['type'];
  $pripona = explode("/",$type);
if(file_exists('pictures/'.$id.'.png')){
 $subor = 'pictures/' . $id .'.png';
  unlink($subor);
    pridaj_obrazok($id);
}
else if(file_exists('pictures/'.$id.'.jpg')){
 $subor = 'pictures/' . $id .'.jpg';
  unlink($subor);
    pridaj_obrazok($id);
}
else if(file_exists('pictures/'.$id.'.jpeg')){
 $subor = 'pictures/' . $id .'.jpeg';
  unlink($subor);
    pridaj_obrazok($id);
}
else if(file_exists('pictures/'.$id.'.gif')){
 $subor = 'pictures/' . $id .'.gif';
  unlink($subor);
    pridaj_obrazok($id);
}

else{
 if (isset($_FILES['obrazok'])) {
  $novy_nazov = '';
      if ($_FILES['obrazok']['error'] == UPLOAD_ERR_OK) {
        if (is_uploaded_file($_FILES['obrazok']['tmp_name'])) {
          $novy_nazov = 'pictures/' . $id .'.'.$pripona[1].'';
          $podarilosa = move_uploaded_file($_FILES['obrazok']['tmp_name'], $novy_nazov);
          if ($podarilosa) {



          }
            $novy_nazov = '';


  }

}
}
}}
}


function zobraz_obrazok($id){
  if(file_exists('pictures/'.$id.'.gif')){
        $subor = 'pictures/' . $id .'.gif';

echo'<a href="'.$subor.'" class="thumbnail" ><img class="img" src="'.$subor.'" alt="" /></a>';
?>
<label for="obrazok">Pridaj foto:</label>
        <br>
<input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
<input type="submit" name="vymaz" onclick="return confirm('Naozaj chcete vymazať fotku?');" value="Vymaž foto"><br>
<input type="submit" name="posli3" value="Zmeň foto"> <br>
<?php
        }
  else  if(file_exists('pictures/'.$id.'.png')){
        $subor = 'pictures/' . $id .'.png';
echo'<a href="'.$subor.'" class="thumbnail" ><img class="img" src="'.$subor.'" alt="" /></a>';
?>
<label for="obrazok">Pridaj foto:</label>
        <br>
<input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
<input type="submit" name="vymaz" onclick="return confirm('Naozaj chcete vymazať fotku?');" value="Vymaž foto"><br>
<input type="submit" name="posli3" value="Zmeň foto"> <br>
<?php
        }
  else  if(file_exists('pictures/'.$id.'.jpg')){
        $subor = 'pictures/' . $id .'.jpg';
echo'<a href="'.$subor.'" class="thumbnail" ><img class="img" src="'.$subor.'" alt="" /></a>';
?>
<label for="obrazok">Pridaj foto:</label>
        <br>
<input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
<input type="submit" name="vymaz" onclick="return confirm('Naozaj chcete vymazať fotku?');" value="Vymaž foto"><br>
<input type="submit" name="posli3" value="Zmeň foto"> <br>
<?php
        }
  else  if(file_exists('pictures/'.$id.'.jpeg')){
        $subor = 'pictures/' . $id .'.jpeg';
echo'<a href="'.$subor.'" class="thumbnail" ><img class="img" src="'.$subor.'" alt="" /></a>';
?>
<label for="obrazok">Pridaj foto:</label>
        <br>
<input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
<input type="submit" name="vymaz" onclick="return confirm('Naozaj chcete vymazať fotku?');" value="Vymaž foto"><br>
<input type="submit" name="posli3" value="Zmeň foto"> <br>
<?php
        }else{

         echo'<img src="fotky/no_photo.jpg" alt="" />';?>
         <label for="obrazok">Pridaj foto:</label>
        <br>
         <input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
         <input type="submit" name="posli3" value="Pridaj"><br> <?php
        }


  }

function Vytvor_tab_pla(){

   $db = napoj_db();

   $sql =<<<EOF
      CREATE TABLE PLATBY
      (ID INTEGER PRIMARY KEY   AUTOINCREMENT,
      ID_POUZ  INT  NOT NULL,
      DATUM  DATETIME  NOT NULL,
      SUMA INT NOT NULL,
      FOREIGN KEY(ID_POUZ) REFERENCES POUZIVATELIA(ID));
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Table created successfully<br>";
   }
   $db->close();
}

function  Pridaj_do_tab_pretekov(){

   $db = napoj_db();

   $sql =<<<EOF
      INSERT INTO PRETEKY (
         NAZOV,DATUM,DEADLINE)
      VALUES ('BEH KOKOTOV PRE LEZBIZKE UCELI', 06/06/1999, '06/26/1982 00:00:00');
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Records created successfully"."<br>";
   }
   $db->close();
}
function  Pridaj_do_tab_prihlas(){

   $db = napoj_db();

   $sql =<<<EOF
      INSERT INTO POUZIVATELIA (
         MENO,PRIEZVISKO,OS_I_C,CHIP,POZNAMKA)
      VALUES ('jana', 'omg','asdasd',99,'omfg');
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Records created successfully"."<br>";
   }
   $db->close();
}

function over($text){
  return strlen($text) >0;

}

function hlavicka($meno=""){
if (isset($_GET['odhlas'])){
  $_SESSION['admin']=0;
  echo '<meta http-equiv="refresh" content="0; URL=index.php">';
}
?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<title><?php echo $meno?></title>
  <link rel="stylesheet" href="styl/styly.css">
  <link rel="stylesheet" href="sorter/themes/blue/style.css">
  <link rel="stylesheet" href="thumbnailviewer.css">

</head>

<body>
  <header>
    <h1><?php echo $meno?></h1>
    <nav>
    <a href="index.php">Domov</a>
      <?php
    if (isset($_SESSION["admin"]) && $_SESSION["admin"]){
            ?>
            <a href="?odhlas=1">Archív</a>
            <a href="?odhlas=1">Kmeňoví členovia</a>
            <a href="?odhlas=1">Odhlásenie</a>
            <?php
        }
    else{
        ?>  <?php
    }
      ?>

    </nav>
  </header>



<?php
}

function paticka(){
    ?>

  <footer>
    <div id="footer"><A HREF="prihlasenie.php">TIS</A> - projekt 2015 pre ŠK Sandberg</div>
  </footer>
</body>

    <?php
}

?>
