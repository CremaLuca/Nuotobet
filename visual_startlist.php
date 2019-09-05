<?php
include_once("classes/gara.class.php");

if (!isset($_GET['id_gara'])) {
    refresh(0, '/p=visual_manif');
}

$gara = new Gara($_GET['id_gara']);
$gara->getData();
$partecipazioni = $gara->getPartecipazioni()->partecipazioni;
$n_partecipanti = count($gara->partecipazioni);
$n_migliorati = $gara->calcMigliorati();
?>
<title>Nuotobet - Startlist <?= $gara->outputNome() ?></title>
<meta name="description" content="Startlist dei <?= $gara->outputNome() ?>" />

<style>
    table.startlist-table-striped tbody tr:nth-child(4n+1),
    table.startlist-table-striped tbody tr:nth-child(4n+2) {
        background-color: rgba(0, 0, 0, .05);
    }

    table.startlist-table-striped tbody tr:nth-child(2n) td {
        border-top: 0;
    }
</style>

<table class='table startlist-table-striped table-hover'>
    <thead class="thead-dark">
        <tr class="text-center">
            <th colspan="6">
                <h1 class="h2"><?= $gara->outputNome() ?></h1>
            </th>
        </tr>
        <tr class="text-center">
            <th>Nome e cognome</th>
            <th>Anno</th>
            <th>Squadra</th>
            <th><?= ($gara->conclusa) ? "Tempo risultato" : "Tempo iscrizione" ?></th>
            <th class="d-none d-md-table-cell"></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($partecipazioni as $part) {
            if ($gara->conclusa) {
                $diff_t_iscr = $part->outputDifferenzaRisultato();
            }
            ?>
            <tr data-href="/?p=visual_part&id_part=<?= $part->id ?>">
                <td>
                    <a href='/?p=visual_atleta&id_atleta=<?= $part->atleta->id ?>'><?= $part->atleta->outputNome() ?></a>
                </td>
                <td class="text-center">
                    <?= $part->atleta->anno ?>
                </td>
                <td class="text-center">
                    <a href='/?p=visual_squadra&id_squadra=<?= $partd->atleta->id_squadra ?>'><?= $part->atleta->squadra ?></a>
                </td>
                <td class="text-center <?= ($gara->conclusa) ? 'text-' . $part->outputRisultatoColor() : '' ?>">
                    <?= ($gara->conclusa) ? $part->outputCompletoRisultato() : $part->outputTempoIscrizione() ?>
                </td>
                <td class="text-center d-none d-md-table-cell">
                    <a class="btn-sm btn-dark enabled" href="/?p=visual_part&id_part=<?= $part->id ?>"> Visualizza </a>
                </td>
            </tr>
            <tr class="d-md-none" data-href="/?p=visual_part&id_part=<?= $part->id ?>">
                <td class="text-center" colspan="5">
                    <a class="btn-sm btn-dark enabled" href="/?p=visual_part&id_part=<?= $part->id ?>"> Visualizza </a>
                </td>
            </tr>
        <?
        }
        ?>
    </tbody>
</table>

<?php
if ($gara->conclusa) {
    ?>
    <table class='table table-statistiche'>
        <thead class='thead-dark text-center'>
            <tr>
                <th colspan="3">
                    <h1 class='h2'>Statistiche gara</h1>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>
                    Atleti che hanno migliorato
                </th>
                <td>
                    <?="$n_migliorati/$n_partecipanti - " . round($n_migliorati / $n_partecipanti * 100, 2) . "%" ?>
                </td>
            </tr>
        </tbody>
    </table>

<?
}
?>