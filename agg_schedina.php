<?php
    //Pagina per concludere le schedine
    if(!isset($_GET['id_sche'])){
        refresh(0,'/');
        exit("Manca l'id schedina");
    }
    $id_schedina = $_GET['id_sche'];
    if(!isset($_SESSION['id_utente'])){
        refresh(0,'/');
        exit("Non hai neanche fatto l'accesso");
    }
    $id_utente = $_SESSION['id_utente'];
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    $query_info_schedina = mysqli_query($conn,"SELECT crediti, id_utente FROM schedina WHERE id='$id_schedina' LIMIT 1");
    $schedina = mysqli_fetch_assoc($query_info_schedina);

    if($schedina['id_utente'] != $id_utente){
        refresh(0,'/');
        exit("Non è neanche tua $id_utente , ".$schedina['id_utente']);
    }else{
        $crediti_schedina = $schedina['crediti'];
        mysqli_query($conn,"UPDATE Schedina SET aperta = '0' WHERE id='$id_schedina'");
        mysqli_query($conn,"UPDATE Utente SET crediti = crediti - '$crediti_schedina' WHERE id = '$id_utente' ");
        $query_crediti = mysqli_query($conn,"SELECT crediti FROM Utente WHERE id = '$id_utente'");
        $crediti_aggiornati = mysqli_fetch_assoc($query_crediti)['crediti'];
        $_SESSION['crediti'] = $crediti_aggiornati;
        unset($_SESSION['id_schedina']);
        refresh(0,'/');
    }
?>