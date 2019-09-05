<?
    if(!isset($_SESSION['id_utente'])){
        refresh(0,'/');
        echo "Non hai fatto l'accesso";
    }//Ora siamo sicuri che qualcuno ha fatto l'accesso
    include_once("includes/time.inc.php");
    include_once("rules.php");
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    if(!isset($_POST['scomm'])){
        refresh(0,'/');
        echo("Manca il tipo di scommessa");
    }
    $scomm = $_POST['scomm'];
    if(!isset($_POST['id_part'])){
        refresh(0,'/');
        echo("Manca id partecipazione");
    }
    $id_part = $_POST['id_part'];

    $query_part = mysqli_query($conn,"SELECT p.id_gara,p.tempo_iscrizione,a.nome,a.anno,sq.nome as squadra,g.metri,g.stile,m.data FROM partecipazione p INNER JOIN atleta a ON a.id = p.id_atleta INNER JOIN gara g ON p.id_gara = g.id INNER JOIN manifestazione m ON g.id_manifestazione = m.id INNER JOIN squadra sq ON sq.id = a.id_squadra WHERE p.id = '$id_part' LIMIT 1");
    echo mysqli_error($conn);
    $partecipazione = mysqli_fetch_assoc($query_part);
    $query_mediana = mysqli_query($conn, "SELECT tempo_iscrizione FROM partecipazione WHERE id_gara = '".$partecipazione['id_gara']."'");
    $array_part = [];
    while($row = mysqli_fetch_array($query_mediana))
    {
        $array_part[] = $row;
    }
    $mediana = $array_part[sizeof($array_part)/2]['tempo_iscrizione'];

    //Parto con i calcoli da qui

    $tempi = calcoloTempo($scomm,$partecipazione['tempo_iscrizione'],$partecipazione['metri']);
    $t_min = $tempi[0];
    $t_max = $tempi[1];
    $quota = calcoloQuota($scomm,$partecipazione['tempo_iscrizione'],$mediana);
    $scadenza = $partecipazione['data'];

    if(!isset($_SESSION['id_schedina'])){
        $query_nuova_schedina = mysqli_query($conn,"INSERT INTO Schedina (id_utente) VALUES ('".$_SESSION['id_utente']."')");
        $id_schedina = mysqli_insert_id($conn);
        if($id_schedina != 0)
        $_SESSION['id_schedina'] = $id_schedina;
        else
        echo "Errore ".mysqli_error($conn);
    }else{
        $id_schedina = $_SESSION['id_schedina']; //Sarà compito di qualcun'altro assicurarsi che sia ancora aperta
    }
    //Siamo sicuri di avere la schedina, ora aggiungiamo sta scommessa SE non c'era già, altrimenti la aggiorniamo
    $query_scommessa_presente = mysqli_query($conn,"SELECT id FROM scommessa WHERE id_schedina = '$id_schedina' AND id_partecipazione = '$id_part'");
    if(mysqli_num_rows($query_scommessa_presente) > 0){
        $id_scommessa = mysqli_fetch_assoc($query_scommessa_presente)['id'];
        $query_aggiorna_scommessa = mysqli_query($conn,"UPDATE Scommessa SET tempo_min = '$t_min', tempo_max = '$t_max', quota = '$quota' WHERE id = '$id_scommessa'");
        echo mysqli_error($conn)." Scommessa $id_scommessa aggiornata";
    }else{
        $query_aggiunta_scommessa = mysqli_query($conn,"INSERT INTO Scommessa (id_schedina, id_partecipazione, tempo_min, tempo_max, quota, scadenza) VALUES ('$id_schedina','$id_part','$t_min','$t_max','$quota','$scadenza')");
        $id_scommessa = mysqli_insert_id($conn);
        echo mysqli_error($conn)." Scommessa n $id_scommessa aggiunta a schedina $id_schedina";
    }

    refresh(0,"/?p=visual_startlist&id_gara=".$partecipazione['id_gara']);
?>