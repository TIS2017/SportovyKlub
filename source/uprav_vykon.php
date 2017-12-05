<?php
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
session_start();

?>

<!DOCTYPE HTML>

<html>
<?php
hlavicka("Spravovanie výkonu");
$vykon = vrat_vykon($_GET["id"]);
if($vykon){




if(isset($_POST["uloz"])){
    echo "<h4 align='center'>".uprav_vykon()."</h4>";
    echo '<meta http-equiv="refresh" content="3; URL=tabulka_vykonov.php?id='.$vykon["ID_LOG"].'">';
}
else{
?>
<form method=POST>
    <div class="spravaVykonu">
        <h2>Spravovanie výkonu:</h2>
        <table style="width:100%;">
            <tr>
                <td class="table-left"><label>Meno súťažiaceho:</label></td>
                <td class="table-right"><?php echo $vykon["MENO"]." ".$vykon["PRIEZVISKO"];?></td>
            </tr>
            <tr>
                <td class="table-left"><label>Preteky:</label></td>
                <td class="table-right"><?php echo $vykon["NAZOV"];?></td>
            </tr>
            <tr>
                <td class="table-left"><label>Dátum:</label></td>
                <td class="table-right"><?php echo $vykon["DATUM"];?></td>
            </tr>
            <tr>
                <td class="table-left"><label>Miesto:</label></td>
                <td class="table-right"><input type="text" name="MIESTO" id="MIESTO" value="<?php if(isset($_POST["MIESTO"])){echo $_POST["MIESTO"];} else{echo $vykon["MIESTO"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Víťaz:</label></td>
                <td class="table-right"><input type="text" name="VITAZ" id="VITAZ" value="<?php if(isset($_POST["VITAZ"])){echo $_POST["VITAZ"];} else{echo $vykon["VITAZ"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Čas víťaza:</label></td>
                <td class="table-right"><input type="text" name="VITAZ_CAS" id="VITAZ_CAS" value="<?php if(isset($_POST["VITAZ_CAS"])){echo $_POST["VITAZ_CAS"];} else{echo $vykon["VITAZ_CAS"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"<label>Môj čas:</label></td>
                <td class="table-right"><input type="text" name="MOJ_CAS" id="MOJ_CAS" value="<?php if(isset($_POST["MOJ_CAS"])){echo $_POST["MOJ_CAS"];} else{echo $vykon["MOJ_CAS"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Nabehaná vzdialenosť v km:</label></td>
                <td class="table-right"><input type="text" name="VZDIALENOST" id="VZDIALENOST" value="<?php if(isset($_POST["VZDIALENOST"])){echo $_POST["VZDIALENOST"];} else{echo $vykon["VZDIALENOST"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Ideálna vzdialenosť v km:</label></td>
                <td class="table-right"><input type="text" name="IDEAL_VZDIALENOST" id="IDEAL_VZDIALENOST" value="<?php if(isset($_POST["IDEAL_VZDIALENOST"])){echo $_POST["IDEAL_VZDIALENOST"];} else{echo $vykon["IDEAL_VZDIALENOST"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Rýchlosť min/km:</label></td>
                <td class="table-right"><input type="text" name="RYCHLOST" id="RYCHLOST" value="<?php if(isset($_POST["RYCHLOST"])){echo $_POST["RYCHLOST"];} else{echo $vykon["RYCHLOST"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Prevýšenie m/km:</label></td>
                <td class="table-right"><input type="text" name="PREVYSENIE" id="PREVYSENIE" value="<?php if(isset($_POST["PREVYSENIE"])){echo $_POST["PREVYSENIE"];} else{echo $vykon["PREVYSENIE"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Odchýlka nabehané/ideálne mínus 1(%):</label></td>
                <td class="table-right"><input type="text" name="ODCHYLKA" id="ODCHYLKA" value="<?php if(isset($_POST["ODCHYLKA"])){echo $_POST["ODCHYLKA"];} else{echo $vykon["ODCHYLKA"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Prirážka % v závislosti od kopcov a rýchlosti:</label></td>
                <td class="table-right"><input type="text" name="PRIRAZKA" id="PRIRAZKA" value="<?php if(isset($_POST["PRIRAZKA"])){echo $_POST["PRIRAZKA"];} else{echo $vykon["PRIRAZKA"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"><label>Hodnotiace kritérium %:</label></td>
                <td class="table-right"><input type="text" name="HODNOTENIE" id="HODNOTENIE" value="<?php if(isset($_POST["HODNOTENIE"])){echo $_POST["HODNOTENIE"];} else{echo $vykon["HODNOTENIE"];}?>"></td>
            </tr>
            <tr>
                <td class="table-left"></td>
                <td class="table-right"><input type=submit name="uloz" id="uloz" value="Ulož" /></td>
            </tr>
        </table>
    </div>
</form>
<br><br><br>

<?php
}
}
else{
    echo "<h4 align='center'>Výkon neexistuje</h4>";
}
paticka();
?>



</html>

<?php
// === PHP Functions ===
function vrat_vykon($id_vykonu){
    $db = napoj_db();

    if($db){
        $sql =<<<EOF
      SELECT
        * FROM VYKON
        JOIN POUZIVATELIA ON VYKON.ID_LOG = POUZIVATELIA.ID
        JOIN PRETEKY ON VYKON.ID_PRET = PRETEKY.ID
        WHERE ID_VYKON = "$id_vykonu";
EOF;

        $ret = $db->query($sql);
        $ret = $ret->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return $ret;
    } else {
        $db->close();
        return 0;
    }
}

function uprav_vykon(){
    $db = napoj_db();


    if($db){
        $ID_VYKON = $_GET['id'];

        $MIESTO = $_POST["MIESTO"];
        $VITAZ = $_POST["VITAZ"];
        $VITAZ_CAS = $_POST["VITAZ_CAS"];
        $MOJ_CAS = $_POST["MOJ_CAS"];
        $VZDIALENOST = $_POST["VZDIALENOST"];
        $IDEAL_VZDIALENOST = $_POST["IDEAL_VZDIALENOST"];
        $RYCHLOST = $_POST["RYCHLOST"];
        $PREVYSENIE = $_POST["PREVYSENIE"];
        $ODCHYLKA = $_POST["ODCHYLKA"];
        $PRIRAZKA = $_POST["PRIRAZKA"];
        $HODNOTENIE = $_POST["HODNOTENIE"];
        //echo $db->lastErrorMsg();
        $sql =<<<EOF
      UPDATE `VYKON` SET
      `MIESTO`="$MIESTO",`VITAZ`="$VITAZ",`VITAZ_CAS`="$VITAZ_CAS",`MOJ_CAS`="$MOJ_CAS",`VZDIALENOST`="$VZDIALENOST",
      `IDEAL_VZDIALENOST`="$IDEAL_VZDIALENOST",`RYCHLOST`="$RYCHLOST",`PREVYSENIE`="$PREVYSENIE",`ODCHYLKA`="$ODCHYLKA",
      `PRIRAZKA`="$PRIRAZKA",`HODNOTENIE`="$HODNOTENIE"
      WHERE VYKON.ID_VYKON = "$ID_VYKON";

EOF;



        $ret = $db->exec($sql);
        $db->close();
        if($ret){
            return "Výkon úspešne zmenený";
        }
        return "Nastala chyba";
    } else {
        $db->close();
        return "Nastal problém s databázou";
    }
    return "Nastal problém";
}

function nazov_a_datum_pretekov($id_preteku, $typ){
    $db = napoj_db();


    if($db){
        //echo $db->lastErrorMsg();
        $sql =<<<EOF
      SELECT
        NAZOV, DATUM FROM PRETEKY
        WHERE ID = "$id_preteku";
EOF;

        $ret = $db->query($sql);
        $row = $ret->fetchArray(SQLITE3_ASSOC);
        if($typ == "nazov"){
            echo '<input type="hidden" name="'.$typ.'" id="'.$typ.'" value="'.$id_preteku.'">'.$row["NAZOV"];
        }
        elseif($typ == "datum"){
            echo '<input type="hidden" name="'.$typ.'" id="'.$typ.'" value="'.$id_preteku.'">'.$row["DATUM"];
        }

        $db->close();
    } else {
        $db->close();
        return;
    }
    return;
}
?>
