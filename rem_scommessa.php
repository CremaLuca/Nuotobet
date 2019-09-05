<?php
    if(!isset($_GET['id_scom'])){
        refresh(0,'/');
        exit("Manca l'id scom");
    }
    $id_scom = $_GET['id_scom'];
    if(!isset($_SESSION['id_utente'])){
        refresh(0,'/');
        exit("Non hai neanche fatto l'accesso");
    }
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    $query_info_scomm = mysqli_query($conn,"SELECT s.id_utente FROM scommessa sc INNER JOIN schedina s ON s.id = sc.id_schedina WHERE sc.id = '$id_scom' LIMIT 1");
    $scom = mysqli_fetch_assoc($query_info_scomm);
    if($_SESSION['id_utente'] != $scom['id_utente']){
       // refresh(0,'/');
        exit("Ma che non è neanche tua, cosa vuoi eliminarla a fare ".mysqli_error($conn));
    }else{
    mysqli_query($conn, "DELETE FROM scommessa WHERE id = '$id_scom'");
    refresh(0,'/');
    }
?>