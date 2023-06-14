<?php
require_once "includes/db.php";
$db = DB::get_instance();

if (isset($_POST['eliminar_assoliments'])) {
    $db->eliminar_assoliments();
}

if (isset($_POST['eliminar_participants'])) {
    $db->eliminar_participants();
}

if (isset($_POST['borrar_vies_sectors'])) {
    $db->borrar_vies_sectors();
}

if (isset($_POST['introduir_sectors_vies'])) {
    $db->introduir_sectors_vies();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Página de depuración</title>
</head>
<body>
    <h1>Página de depuración</h1>

    <form method="post" action="debug.php">
        <h2>Eliminar datos</h2>
        <button type="submit" name="eliminar_assoliments">Eliminar todos los assoliments</button>
        <button type="submit" name="eliminar_participants">Eliminar todos los participants</button>
        <button type="submit" name="borrar_vies_sectors">Borrar todas las vies y sectores</button>
    </form>

    <form method="post" action="debug.php">
        <h2>Introducir datos automáticamente</h2>
        <button type="submit" name="introduir_sectors_vies">Introducir sectores y vies automáticamente</button>
    </form>
</body>
</html>
