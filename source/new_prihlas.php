<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="CSS/mycss.css">
<title>Untitled Document</title>
</head>

<body>

<?php

$sel0 = 2;
if(isset($_GET['sel0']))
{
 $sel0 = $_GET['sel0'];
}
if(isset($_POST['sel0']))
{
 $sel0 = $_POST['sel0'];
}

$asc0;
if(isset($_GET['asc0']))
{
 $asc0 = $_GET['asc0'];
	 if ($asc0 == "ASC")
		$asc0 = "DESC";
	else
		$asc0 = "ASC";
}
elseif(isset($_POST['asc0']))
{
 $asc0 = $_POST['asc0'];
 if ($asc0 == "ASC")
	$asc0 = "DESC";
else
	$asc0 = "ASC";
}
else
	$asc0 = "ASC";
	
$asc1;
if(isset($_GET['asc1']))
{
 $asc1 = $_GET['asc1'];
	 if ($asc1 == "ASC")
		$asc1 = "DESC";
	else
		$asc1 = "ASC";
}
elseif(isset($_POST['asc1']))
{
 $asc1 = $_POST['asc0'];
 if ($asc1 == "ASC")
	$asc1 = "DESC";
else
	$asc1 = "ASC";
}
else
	$asc1 = "ASC";



$b01 = "";
$b02 = "";
$b03 = "";
$b04 = "";
$b05 = "";

if($sel0 == 1)
{
 $met0 = "firstname";
 $b01 = "<b>";
}
else if($sel0 == 2)
{
 $met0 = "surname";
 $b02 = "<b>";
}
else if($sel0 == 3)
{
 $met0 = "reg_num";
 $b03 = "<b>";
}
else if($sel0 == 4)
{
 $met0 = "chip_num";
 $b04 = "<b>";
}
else if($sel0 == 5)
{
 $met0 = "category";
 $b05 = "<b>";
}

$sel1 = 3;
if(isset($_GET['sel1']))
{
 $sel1 = $_GET['sel1'];
}
if(isset($_POST['sel1']))
{
 $sel1 = $_POST['sel1'];
}

$b1 = "";
$b2 = "";
$b3 = "";
$b4 = "";
$b5 = "";

if($sel1 == 1)
{
 $met1 = "category";
 $b1 = "<b>";
}
else if($sel1 == 2)
{
 $met1 = "firstname";
 $b2 = "<b>";
}
else if($sel1 == 3)
{
 $met1 = "surname";
 $b3 = "<b>";
}
else if($sel1 == 4)
{
 $met1 = "reg_num";
 $b4 = "<b>";
}
else if($sel1 == 5)
{
 $met1 = "chip_num";
 $b5 = "<b>";
}

if(isset($_GET["pretek_id"]) or isset($_POST["pretek_id"]))
{
	$db = new SQLite3('Temp/test.sqlite');  //DB CONNECT
	$game;
	$under_deadline;
	if(isset($_GET["pretek_id"]))
	{
		$game = $_GET["pretek_id"];
		$under_deadline = $_GET["under_deadline"];
	}
	else
	{
		$game = $_POST["pretek_id"];
		$under_deadline = $_POST["under_deadline"];
	}
	
	//*******************************ALL*******************************
	//********                   UPDATE ALL                    ********
	//*****************************************************************
	if (isset($_POST['update_poznamka']) and isset($_POST['update_cip']) and isset($_POST['update_cat']) and isset($_POST['user_id'])) 
	{
		$update_poznamka = $_POST['update_poznamka'];
		$update_cip = $_POST['update_cip'];
		$update_user = $_POST['user_id'];
		$update_cat = $_POST['update_cat'];
		// $service is an array of selected values
		$service_string = "";
		//$db->exec("UPDATE dp_users SET poznamka ='',chip_num =''");
		for($i=0;$i<count($update_user);$i++)
		{	
			$db->exec("UPDATE dp_users SET poznamka ='".$update_poznamka[$i]."', chip_num ='".$update_cip[$i]."', category ='".$update_cat[$i]."' WHERE user_id ='".$update_user[$i]."'");
		}
	}
	//*******************************ALL*******************************
	
	//*****************************JAZDEC******************************
	//********                   ADD JAZDEC                    ********
	//*****************************************************************
	if (isset($_POST['cat_add']) and isset($_POST['meno_add'])and isset($_POST['priezvisko_add'])) 
	{
		$cat_add = $_POST['cat_add'];
		$meno_add = $_POST['meno_add'];
		$priezvisko_add = $_POST['priezvisko_add'];
		$regc_add = $_POST['regc_add'];
		$sicip_add = $_POST['sicip_add'];
		$poznamka_add = $_POST['poznamka_add'];
		// $service is an array of selected values
		if (strlen($cat_add) != 0 and strlen($meno_add) != 0 and strlen($priezvisko_add) != 0)
		{
			$db->exec("INSERT INTO dp_users (firstname,surname,reg_num,chip_num,address,birth_date,phone,email,reg_since,reg_to,fcn,mailing,category,poznamka) 
						values (
								'".$meno_add."'
								,'".$priezvisko_add."'
								,'".$regc_add."'
								,'".$sicip_add."'
								,'0'
								,'0'
								,0
								,'0'
								,'0'
								,'0'
								,'0'
								,0
								,'".$cat_add."'
								,'".$poznamka_add."')");
			if(isset($_POST['upadate_ucast_now']))
			{
				$now_inert = $db->query("select user_id from dp_users where firstname ='".$meno_add."' and surname ='".$priezvisko_add."' and reg_num ='".$regc_add."' and chip_num ='".$sicip_add."'");
				$usr_id = $now_inert->fetchArray();
				$db->exec("INSERT INTO dp_ucast (pretek_id,user_id) values ('".$game."','".$usr_id['user_id']."')");
			}
			
		}
	}
	//*****************************JAZDEC******************************
	
	//******************************UCAST******************************
	//********                  DELETE UCAST                   ********
	//*****************************************************************
	if (isset($_POST['delete_from_ucast'])) 
	{
		$delete_from_ucast = $_POST['delete_from_ucast'];
		// $service is an array of selected values
		$service_string = "";
		//$db->exec("UPDATE dp_users SET poznamka ='',chip_num =''");
		for($i=0;$i<count($delete_from_ucast);$i++)
		{	
			$db->exec("DELETE FROM dp_ucast WHERE user_id = '".$delete_from_ucast[$i]."' and pretek_id = '".$game."'");
		}
	}
	//******************************UCAST******************************
	
	
	//
	
	if (isset($_POST['upadate_ucast'])) 
	{
		$upadate_ucast = $_POST['upadate_ucast'];
		// $service is an array of selected values
		$service_string = "";
		//$db->exec("UPDATE dp_users SET poznamka ='',chip_num =''");
		for($i=0;$i<count($upadate_ucast);$i++)
		{
			$db->exec("INSERT INTO dp_ucast (pretek_id,user_id) values ('".$game."','".$upadate_ucast[$i]."')");
		}
	}	
	
	
	$result = $db->query("select * from dp_users where fcn != 'Ex' ");
?>

<div class="pretek_admin">
<div class="container" style="width:350px; margin-left:auto; margin-right:auto;">

	<!-- begin navigation -->
	<!-- end navigation -->
	
</div>
    
<?php	

$seda = "#999"; // -seda
$seda_txt = "#333"; // -seda
$zelena = "#0C3"; //-zelena
$zelena_txt = "#030";
$cervena = "#F00"; //-cervena
$cervena_txt = "#300"; //-cervena


$result = $db->query("SELECT * FROM dp_pretek WHERE pretek_id = ".$game." ");

while ($row = $result->fetchArray()) 
{
	$color; 
	if ($under_deadline == 0)
		$color = $seda;
	elseif ($under_deadline == 1)
		$color = $zelena;
	elseif ($under_deadline == 2)
		$color = $cervena;
	
	
	echo "<div class=\"table_prihlas_left\">";
		echo "<h4>Prihlasenie na preteky:</h4><h1 class=\"h1_new\" style=\"background:$color;\">".$row['name']."</h1>";
		echo "<h4>Deadline:</h4>";
		echo "<h1 class=\"h1_new\" style=\"background:$color;\">";
		echo date("d.m.Y", strtotime($row['deadline']));
		echo "</h1>";
		echo "<br />";
		if ($under_deadline == 0)
		{
			echo "<h1 class=\"h1_new\" style=\"background:$color;\">Prihlásenie je možné už len mailom</h1>";
		}
		if ($under_deadline == 2)
		{
			echo "<h1 class=\"h1_new\" style=\"background:$color;\">Posledný deň na prihlásenie</h1>";
		}
	echo "</div>";
}


	
	echo "<br />";

	echo "<div>";
	echo "<table class=\"new_prihlas\"><td class=\"In\"><h4> Zoznam prihlásených:<BR></H4><BR>";

		$result = $db->query("select * from dp_users where fcn != 'Ex' order by ".$met0." $asc1");
        //                 SECOND TABLE START
        echo "<form method=\"post\" action=\"new_prihlas.php\" name=\"formular2\" >";
        echo "<table class=\"kategoria\" border=\"1\" cellspacing=\"0\">
                <tr>
                    <td align=\"center\"><img src=\"Images/nok.png\" alt=\"delete\"></td>
                    <td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc1=$asc1&amp;under_deadline=$under_deadline&amp;sel0=5&amp;sel1=".$sel1."'\">".$b05." Kategória  </b></td>
					<td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc1=$asc1&amp;under_deadline=$under_deadline&amp;sel0=1&amp;sel1=".$sel1."'\">".$b01." Meno       </b></td>
					<td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc1=$asc1&amp;under_deadline=$under_deadline&amp;sel0=2&amp;sel1=".$sel1."'\">".$b02." Priezvisko </b></td>
					<td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc1=$asc1&amp;under_deadline=$under_deadline&amp;sel0=3&amp;sel1=".$sel1."'\">".$b03." Reg. č.    </b></td>
					<td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc1=$asc1&amp;under_deadline=$under_deadline&amp;sel0=4&amp;sel1=".$sel1."'\">".$b04." SI čip     </b></td>
                    <td> Poznamka     </b></td>
                </tr><br/>";
    
        while ($row = $result->fetchArray()) 
        {
            //zistene ucast na sutazi
            $result2 = $db->query("select * from dp_ucast where pretek_id ='".$game."' and user_id = '".$row['user_id']."'");
            if ($row2 = $result2->fetchArray())
            {
                $result3 = $db->query("select * from dp_category where category_id ='".$row['category']."'");
                
                $row3 = $result3->fetchArray();
                
                echo "<tr><td align=\"center\"><input type=\"checkbox\" value=\"".$row['user_id']."\" name=\"delete_from_ucast[]\" /></td><td>".$row3['name']."</td><td>".$row['firstname']."</td><td>".$row['surname']."</td><td>".$row['reg_num']."</td><td>".$row['chip_num']."</td><td>".$row['poznamka']."</td></tr>";
            }
            
        
        }
        echo "</table>";
        
		echo "<input type=\"hidden\" name=\"pretek_id\" value=\"".$game."\" />";
		echo "<input type=\"hidden\" name=\"sel1\" value=\"".$sel1."\" />";
		echo "<input type=\"hidden\" name=\"sel0\" value=\"".$sel0."\" />";
        echo "<input type=\"hidden\" name=\"under_deadline\" value=\"".$under_deadline."\" />";
        echo "<input type=\"submit\" class=\"kat_sub\" name=\"sub1\" value=\"Odhlas\"/><input type=\"submit\" class=\"kat_sub\" name=\"export\" id=\"button\" value=\"Export\"/></form>";
		echo "<br>";
        //                 SECOND TABLE END

	$Exp = false;
	//export zucastnenych pretekarov
	if (isset($_POST['export'])) {
		//otvorenie suboru
		$game = $_POST['pretek_id'];
		$myFile = "Temp/pretek".$game.".txt";
		mb_internal_encoding('iso-8859-2');
		$fh = fopen($myFile, 'w') or die("Can't open file");
		$result = $db->query("select * from dp_users order by surname");
		while ($row = $result->fetchArray()) {
			//zistene ucast na sutazi
			$result2 = $db->query("select * from dp_ucast where pretek_id ='".$game."' and user_id = '".$row['user_id']."'");
			if ($row2 = $result2->fetchArray())
			{
				$result3 = $db->query("select * from dp_category where category_id = '".$row['category']."'");
				$row3 = $result3->fetchArray();
				$stringData = $row3['name'].",".$row['firstname'].",".$row['surname'].",".$row['reg_num'].",".$row['chip_num']."\n";
				//$stringData = "skuska \n";
				$Exp = true;
				fwrite($fh, $stringData);

			}
	
		}
		fclose($fh);
		//redir("new_prihlas.php?export=".$game."");
		
	}
	if ($Exp) {
		$game = $_POST['pretek_id'];
		$myFile = "Temp/pretek".$game.".txt";
		echo "<h3>Data boli vyexportovane do suboru: <a href='$myFile'  charset=\"iso-8859-2\" target=\"_blank\">".$myFile."</a>.</h3>";
	}

if ($under_deadline != 0)
{
	echo "</td><td class=\"In\"> <h4>Nájdite sa v zozname a prípadne upravte údaje</H4>";

		//                 FIRST TABLE START
	$result = $db->query("select * from dp_users  WHERE reg_num > 'SKS' order by ".$met1." $asc0");
	echo "<form method=\"post\" action=\"new_prihlas.php\" name=\"formular1\" >";
	echo "<table border=\"1\" class=\"kategoria\" cellspacing=\"0\">
				<tr>
					<td><img src=\"Images/ok.png\" alt=\"delete\"></td>
					<td>Kategória</td>
					<td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc0=$asc0&amp;under_deadline=$under_deadline&amp;sel1=2&amp;sel0=".$sel0."'\">".$b2."Meno</td>
					<td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc0=$asc0&amp;under_deadline=$under_deadline&amp;sel1=3&amp;sel0=".$sel0."'\">".$b3."Priezvisko</td>
					<td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc0=$asc0&amp;under_deadline=$under_deadline&amp;sel1=4&amp;sel0=".$sel0."'\">".$b4."Reg. číslo</td>
					<td onclick=\"location.href='new_prihlas.php?pretek_id=".$game."&amp;asc0=$asc0&amp;under_deadline=$under_deadline&amp;sel1=5&amp;sel0=".$sel0."'\">".$b5."SI čip</td>
					<td>Poznamka</td>
				</tr><br/>";  
				
	 
			
		while ($row = $result->fetchArray()) {
			//zistene ucast na sutazi
			$result2 = $db->query("select * from dp_ucast where pretek_id ='".$game."' and user_id = '".$row['user_id']."'");
			if (!$row2 = $result2->fetchArray())
			{
				$result3 = $db->query("select * from dp_category where category_id ='".$row['category']."'");
				$row3 = $result3->fetchArray();
				
				echo "<tr><td align=\"center\"><input type=\"checkbox\" value=\"".$row['user_id']."\" name=\"upadate_ucast[]\" /></td><td>";
				echo "<select class=\"meno\" name=\"update_cat[]\" style=\"width: 100px\">";
					$result3 = $db->query("select * from dp_category");
					while ($row3 = $result3->fetchArray()) {
						if ($row3['active'] == 'Y')
						{
							if ($row3['category_id'] == $row['category']){
								echo "2a";
								echo "<option value=\"".$row3['category_id']."\" selected>".$row3['name']."</option>";
							}else{
								echo "2b";
								echo "<option value=\"".$row3['category_id']."\">".$row3['name']."</option>";
							}
						}
					}
				echo"</select>";
				echo "</td><td>".$row['firstname']."</td><td>".$row['surname']."</td><td>".$row['reg_num']."</td><td><input type=\"text\" name=\"update_cip[]\" value=\"".$row['chip_num']."\" size=\"5\" /></td><td><input type=\"text\" name=\"update_poznamka[]\"  value=\"".$row['poznamka']."\" size=\"8\" /></td></tr>";
				echo "<input type=\"hidden\" name=\"user_id[]\" value=\"".$row['user_id']."\" />";
			}

		}
		echo "<tr>
					<td align=\"center\"><input type=\"checkbox\" value=\"Y\" name=\"upadate_ucast_now\" /></td>
					<td>";
				echo "<select id=\"meno\" name=\"cat_add\" style=\"width: 100px\">";
					$result3 = $db->query("select * from dp_category");
					while ($row3 = $result3->fetchArray()) {
						if ($row3['active'] == 'Y')
						{
							if ($row3['category_id'] == $row['category']){
								echo "2a";
								echo "<option value=\"".$row3['category_id']."\" selected>".$row3['name']."</option>";
							}else{
								echo "2b";
								echo "<option value=\"".$row3['category_id']."\">".$row3['name']."</option>";
							}
						}
					}
				echo"</select>";
				echo "</td>
					<td><input class\"add_driver\" type=\"text\" name=\"meno_add\" size=\"5\" /></td>
					<td><input class\"add_driver\" type=\"text\" name=\"priezvisko_add\" size=\"8\" /></td>
					<td><input class\"add_driver\" type=\"text\" name=\"regc_add\" size=\"6\" /></td>
					<td><input class\"add_driver\" type=\"text\" name=\"sicip_add\" size=\"5\" /></td>
					<td><input class\"add_driver\" type=\"text\" name=\"poznamka_add\" size=\"8\" /></td>
				</tr><br/>";
				
		echo "</table>";
		
	echo "<input type=\"hidden\" name=\"pretek_id\" value=\"".$game."\" />";
	echo "<input type=\"hidden\" name=\"sel1\" value=\"".$sel1."\" />";
	echo "<input type=\"hidden\" name=\"sel0\" value=\"".$sel0."\" />";
	echo "<input type=\"hidden\" name=\"under_deadline\" value=\"".$under_deadline."\" />";
	echo "<input class=\"kat_sub\" type=\"submit\" name=\"sub1\" value=\"Prihlas / Uprav\"/></form>";
	echo "<br>";
	//                 FIRST TABLE END
}
    }
	
	echo "</td></table>";
    ?>
    </div>
</div>
</body>
</html>