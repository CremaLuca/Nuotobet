<?php
include_once("classes/manifestazione.class.php");

$manifestazioni_aperte = Manifestazione::getManifestazioniFromDate(date("Y-m-d H:m:s"),100);
$manifestazioni_chiuse = Manifestazione::getManifestazioniFromDate(null, date("Y-m-d H:m:s"),100);
?>
<title>Nuotobet - Manifestazioni</title>
<meta name="description" content="Visualizza tutte le manifestazioni future e passate registrate in Nuotobet" />

<table class='table table-striped table-hover'>
    <thead class="thead-dark">
        <tr class="text-center">
            <th colspan="5">
                <h1 class="h2"> Manifestazioni </h1>
            </th>
        </tr>
        <tr class="text-center">
            <th>Nome manifestazione</th>
            <th>Luogo</th>
            <th>Data</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class="text-center" colspan="5">
                <h1 class="h4">Aperte alle scommesse</h1>
            </th>
        </tr>
        <?php
        if (count($manifestazioni_aperte) > 0) {
            foreach ($manifestazioni_aperte as $manif) {
                ?>
                <tr data-href='/?p=visual_gara&id_manif=<?= $manif->id ?>'>
                    <td>
                        <?= $manif->nome ?>
                    </td>
                    <td>
                        <?= $manif->luogo ?>
                    </td>
                    <td class="text-center">
                        <?= $manif->outputData() ?>
                    </td>
                    <td class="text-center">
                        <a class="btn-sm btn-dark" href='/?p=visual_gara&id_manif=<?= $manif->id ?>'> Visualizza
                        </a>
                    </td>
                </tr>
        <?
            }
        } else {
            echo "<tr><td class='text-center' colspan='5'> Al momento non ci sono manifestazioni aperte alle scommesse</td></tr>";
        }
        ?>
        <tr>
            <th class="text-center" colspan="5">
                <h1 class="h4">Chiuse alle scommesse</h1>
            </th>
        </tr>
        <?php
        foreach ($manifestazioni_chiuse as $manif) {
            ?>
            <tr data-href='/?p=visual_gara&id_manif=<?= $manif->id ?>'>
                <td>
                    <?= $manif->nome ?>
                </td>
                <td>
                    <?= $manif->luogo ?>
                </td>
                <td class="text-center">
                    <?= $manif->outputData() ?>
                </td>
                <td class="text-center">
                    <a class="btn-sm btn-dark" href='/?p=visual_gara&id_manif=<?= $manif->id ?>'> Visualizza
                    </a>
                </td>
            </tr>
        <?
        }
        ?>
    </tbody>
</table>