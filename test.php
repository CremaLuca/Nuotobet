<?php
    include_once("classes/manifestazione.class.php");

    $manifestazione = new Manifestazione(37);
    $manifestazione->getData();
    $manifestazione->getGare();
?>
<h1><?= $manifestazione->nome ?> <?= $manifestazione->luogo?> <?= $manifestazione->data?></h1>
<table>
    <tr>
    <th>Nome gara</th>
</tr>
<?php
    foreach($manifestazione->gare as $gara){
        ?>
    <tr>
        <td><?= $gara->metri?><?= $gara->stile?><?= $gara->categoria?></td>
    </tr>
        <?php
    }
?>
</table>