<?php
    if(!isset($_GET['id_scom'])){
        refresh(0,'/');
        exit;
    }
    $id_scommessa = $_GET['id_scom'];
    if(!isset($_SESSION['id_utente'])){
        refresh(0,'/');
        exit;
    }
    require_once("includes/time.inc.php");
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    $query_scommessa = mysqli_query($conn, "SELECT sc.id as id_scom,sc.tempo_min,sc.tempo_max,sc.quota,sc.scadenza,p.tempo_iscrizione,p.id as id_part,a.nome,g.metri,g.stile,m.nome as nome_manif,m.id as id_manif,s.id_utente,p.id_atleta FROM scommessa sc INNER JOIN schedina s ON s.id = sc.id_schedina INNER JOIN partecipazione p ON sc.id_partecipazione = p.id INNER JOIN atleta a ON p.id_atleta = a.id INNER JOIN gara g ON p.id_gara = g.id  INNER JOIN manifestazione m ON g.id_manifestazione = m.id WHERE sc.id = '$id_scommessa' LIMIT 1");
    $scom = mysqli_fetch_assoc($query_scommessa);
    if($scom['id_utente'] != $_SESSION['id_utente']){
        refresh(0,'/');
        exit;
    }
?>
<title>Nuotobet - Scommessa</title>
<meta name="description" content="Scommessa" />
<table class="table">
<thead class="thead-dark">
<tr>
    <th colspan="4" class="text-center">
    <h1 class="h2"><? echo retrieveAtletheName($scom['nome']);?></h1>
    <p class='text-muted'><a href='/?p=visual_atleta&id_atleta=<? echo $scom['id_atleta'];?>'>Vai alla pagina dell'atleta</a></p>
    </th>
</tr>
</thead>
<tbody>
<tr class="text-center">
    <th colspan="4"> Informazioni gara </th>
</tr>
<tr>
    <th>Manifestazione</th>
    <td><? echo $scom['nome_manif']; ?></td>
    <th>Data</th>
    <td><? echo date("d-m-Y",strtotime($scom['scadenza'])); ?></td>
</tr>
<tr>
    <th>Gara</th>
    <td><? echo $scom['metri']." ".$scom['stile']; ?></td>
    <th>Tempo iscrizione</th>
    <td><? echo sede_sege($scom['tempo_iscrizione']); ?></td>
</tr>
<tr class="text-center">
    <th colspan="4"> Informazioni scommessa </th>
</tr>
<tr>
    <?php
        if($scom['tempo_min'] != "-1"){
    ?>
    <th>Tempo minimo</th>
    <td colspan="3"><? echo sede_sege($scom['tempo_min']); ?></td>
    </tr>
    <tr>
    <th>Tempo massimo</th>
    <td colspan="3"><? echo sede_sege($scom['tempo_max']); ?></td>
    <?php
        }else{
        ?>
        <th>Tipo di scommessa</th>
        <td colspan="3">Squalifica (DSQ)</td>
        <?
        }
    ?>
</tr>
<tr>
    <th>Quota</th>
    <td colspan="3"><? echo $scom['quota']; ?></td>
</tr>
<tr class="text-center">
        <td colspan="4">
            <a class="btn btn-outline-info" role="button" href="/?p=visual_part&id_part=<? echo $scom['id_part']; ?>">Modifica</a> 
            <a class="btn btn-outline-danger" role="button" href="/?p=rem_scom&id_scom=<? echo $scom['id_scom']; ?>">Elimina</a>
        </td>
    </tr>
</tbody>
</table>