<?php
require_once "includes/db.php";
if($_REQUEST['participant']==$_REQUEST['assegurador'])
{
    header("Location: assoliments.php");
}else{
    $encadenat = false;
    $primer = false;
    if(isset($_REQUEST['encadenat'])){
        $encadenat = true;
    }
    if(isset($_REQUEST['primer'])){
        $primer = true;
    }
    $db = DB::get_instance();
    if($db->set_assoliment($_REQUEST['participant'], $_REQUEST['via'], $_REQUEST['intent'], $_REQUEST['data'], $encadenat, $primer, $_REQUEST['assegurador'])){
            header("Location: assoliments.php");
    }else{
        header("Location: registre.html");
    }
}
?>