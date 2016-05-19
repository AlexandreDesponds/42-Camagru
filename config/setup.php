<?php
    if (isset($_GET['k'])) {
        require_once('database.php');
        $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $sql = file_get_contents('struct.sql');
        $qr = $db->exec($sql);
        echo "Done !<br><br>";
        echo "<a href='datafixture.php'>execute datafixture ?</a>";
    } else {
        echo "Are you sure ? <a href='?k=k'>Remove all data and recreate struct</a>";
    }