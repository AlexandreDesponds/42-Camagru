<?php
    require_once('database.php');
    $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $sql = file_get_contents('datafixture.sql');
    $qr = $db->exec($sql);
    echo "Done !<br><br>";
    echo "<a href='/'>GG ! Enjoy !</a>";