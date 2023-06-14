<?php
require_once "includes/db.php";
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <title>Escrutini</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="container">

    <div class="form-container left-container">
        <form action="addassoliments.php" method="post">
            
        <span>Escull participant</span>
        <label for="participant">
                <input id="participant_in" list="participant" name="participant" type="text" required>
            </label>
            <datalist id="participant">
                <?php
                $db = DB::get_instance();
                $participants = $db->get_participants();
                foreach ($participants as $p){
                    $nom = $p["nom"];
                    $cog = $p["cognom"];
                    $ema = $p["email"];

                    echo "<option value='$ema' data-nom='$nom' data-cognom='$cog'> $nom - $cog </option> \n";
                }
                ?>
            </datalist>
            <span>Escull via</span>
        <label for="via">
                <input id="via_in" list="via" name="via" type="text" required>
            </label>
            <datalist id="via">
                <?php
                $db = DB::get_instance();
                $via = $db->get_via();
                foreach ($via as $v){
                    $nom = $v["nom"];
                    $sec = $v["sector"];
                    $gra = $v["grau"];

                    echo "<option value='$nom' data-sector='$sec' data-grau='$gra'> $nom </option> \n";
                }
                ?>
            </datalist>
            <span>Intent</span>
            <input id="nom_in" name="intent" type="number" required>
            <span>Data</span>
            <input id="nom_in" name="data" type="date" required>
            <span>Encadenat</span>
            <input id="nom_in" name="encadenat" type="checkbox">
            <span>Primer</span>
            <input id="nom_in" name="primer" type="checkbox">
            <span>Escull assegurador</span>
            <label for="assegurador">
                <input id="assegurador_in" list="assegurador" name="assegurador" type="text" required>
            </label>
            <datalist id="assegurador">
                <?php
                $db = DB::get_instance();
                $participants = $db->get_participants();
                foreach ($participants as $p){
                    $nom = $p["nom"];
                    $cog = $p["cognom"];
                    $ema = $p["email"];

                    echo "<option value='$ema' data-nom='$nom' data-cognom='$cog'> $nom - $cog </option> \n";
                }
                ?>
            </datalist>
            <button>Inicia</button>
        </form>
    </div>
</div>
</div>