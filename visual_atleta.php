<?php
include_once("includes/time.inc.php");
include_once("classes/atleta.class.php");
if(!isset($_GET['id_atleta'])){
    refresh(0,'/p=visual_manif');
    exit;
}
$id_atleta = $_GET['id_atleta'];

$atleta = new Atleta($_GET['id_atleta']);
$atleta->getData();
$partecipazioni = $atleta->getPartecipazioni();

$conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
?>
<title>Nuotobet - <?= $atleta->outputNome() ?></title>
<meta name="description" content="Informazioni sulle prestazioni di <?= $atleta->outputNome() ?>" />
<h1 class='h2'><?= $atleta->outputNome() ?></h1>
<table class='table'>
<thead class='thead-dark'>
    <tr class='text-center'>
        <th colspan='2'>Dettagli</th>
    </tr>
</thead>
<tbody>
<tr>
    <th>Anno di nascita</th>
    <td><?= $atleta->anno ?></td>
</tr>
<tr>
    <th>Squadra</th>
    <td><?= $atleta->squadra ?></td>
</tr>
</tbody>
</table>
<table class='table'>
<thead class='thead-dark'>
    <tr class='text-center'>
        <th colspan='6'>Gare a cui ha partecipato</th>
    </tr>
    <tr class='text-center'>
        <th>Gara</th>
        <th>Tempo iscrizione</th>
        <th>Tempo risultato</th>
    </tr>
</thead>
<tbody>
<?
$last_manif = 0;
    foreach($partecipazioni as $part){
        if($part->gara->manifestazione->id != $last_manif){
            $last_manif = $part->gara->manifestazione->id;
            echo "<tr><th colspan='6'><h1 class='h4'>".$part->gara->manifestazione->outputInfo()." ".$part->gara->manifestazione->outputData()."</h1></th></tr>";
        }
        ?>

        <tr class='text-center'>
        <td><?= $part->gara->outputNome() ?></td>
        <td ><?= $part->outputTempoIscrizione() ?></td>
        <td class='text-<?= $part->outputRisultatoColor() ?>'><?= $part->outputCompletoRisultato()?></td>
        </tr>
        <?
    }
?>
</tbody>
</table>