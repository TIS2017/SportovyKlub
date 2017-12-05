<?php
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
session_start();

?>

<!DOCTYPE HTML>

<html>
<?php
hlavicka("Tabuľka výkonov");
if(isset($_POST["vymaz"])){
    if(vymaz_vykon($_POST["ID_VYKON"])){
        echo "<h4 align='center'>Výkon vymazaný</h4>";
    }
}
vypis_vykony();

paticka();
?>



</html>

<?php
// === PHP Functions ===
function vypis_vykony(){

        ?>
        <div>
        <h1 style="text-align:center;">Kmeňoví členovia</h1>
        <table style="width:100%;" border=1 class="tabulkaVykonou">

            <tr>
                <th class="prvy">Meno</th>
                <th class="prvy">Priezisko</th>
                <th class="prvy">Pohlavie</th>
                <th class="prvy">Dátum narodenia</th>
                <th class="prvy">Krajina narodenia</th>
                <th class="prvy">Štátna príslušnosť</th>
                <th class="prvy">Krajina trvalého bydliska</th>
                <th class="prvy">Ulica</th>
                <th class="prvy">Číslo domu</th>
                <th class="prvy">PSČ</th>
                <th class="prvy">Mesto</th>
                <th class="prvy">Telefón</th>
                <th class="prvy">Mail</th>
                <th class="prvy">Číslo čipu</th>
                <th class="prvy">Registračné číslo</th>
                <td></td>
            </tr>
            <?php
       for($i=0; $i<20; $i++){
            echo "<tr>";
            echo "<td>Janko</td>";
            echo "<td>Brokolica</td>";
            echo "<td>Muž</td>";
            echo "<td>26.11.1996</td>";
            echo "<td>Slovensko</td>";
            echo "<td>slovenská</td>";
            echo "<td>Slovenská republika</td>";
            echo "<td>Priečna</td>";
            echo "<td>570</td>";
            echo "<td>966 93</td>";
            echo "<td>Mandarínkovo</td>";
            echo "<td>0912345678</td>";
            echo "<td>janko.brokolica@post.sk</td>";
            echo "<td>98712345</td>";
            echo "<td>SKS9601</td>";
            echo "<td><form method='post'><input type='hidden' name='ID_VYKON' ><input type='submit' name='vymaz' value='Vymaž'></form></td>";

            echo "</tr>";
        }
            ?>
        </table>
        </div><?php

        return;

}

function vymaz_vykon($id_vykon){
    $db = napoj_db();

    if ($db) {
        $sql = <<<EOF
          DELETE FROM VYKON WHERE VYKON.ID_VYKON = "$id_vykon";
EOF;

        $ret = $db->exec($sql);
        return $ret;
    }
    return 0;
}
?>
