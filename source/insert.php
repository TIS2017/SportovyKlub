<?php
$filename = "database.db";
$db = sqlite_open($filename, 0666, $error_message) or die($error_message);

$query = "CREATE TABLE test (
          cislo INT NOT NULL PRIMARY KEY,
          meno VARCHAR(20),
          datum_narodenia DATE
          );

          INSERT INTO test VALUES (1, 'Rastislav Szab�', '1987-08-31');
          INSERT INTO test VALUES (2, 'Jozef Nov�k', '1979-11-02');
          INSERT INTO test VALUES (3, 'Juraj Hladk�', '1989-02-13');
          INSERT INTO test VALUES (4, 'Peter Nov�', '1985-11-03');
          INSERT INTO test VALUES (5, 'Andrej Lacn�', '1995-01-12');
          ";
sqlite_query($db, $query);
echo "Pridan�ch bolo ".sqlite_changes($db)." riadkov";

sqlite_close($db);
?>