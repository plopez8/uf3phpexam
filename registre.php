<?php
require_once "includes/db.php";
if($_REQUEST['nom']=='' || $_REQUEST['cognom']=='' || $_REQUEST['email']=='')
{
echo("please fill the empty field.");
}else{
    $db = DB::get_instance();
    if(!$db->connected()){
        $status = "f";
        echo json_encode(array('status'=>$status));
    }
    if($db->set_participant($_REQUEST['nom'], $_REQUEST['cognom'], $_REQUEST['email'])){
            header("Location: assoliments.php");
    }else{
        header("Location: registre.html");
    }
}
?>