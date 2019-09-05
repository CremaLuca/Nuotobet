<?php
include_once("classes/manifestazione.class.php");
include_once("classes/gara.class.php");

if (!isset($_GET['id_manif'])) {
    refresh(0, '/p=visual_manif');
}

$manifestazione = new Manifestazione($_GET['id_manif']);
$manifestazione->getData();
$gare = $manifestazione->getGare()->gare;

$conn = mysqli_connect("localhost", "nuotobet", "password", "my_nuotobet");
?>
<title>Nuotobet - <?= $manifestazione->nome ?></title>
<meta name="description" content="Lista di gare alla manifestazione <?= $manifestazione->nome ?>" />
<table class='table table-striped table-hover'>
    <thead class="thead-dark">
        <tr class="text-center">
            <th colspan="5">
                <h1 class="h2"><?= $manifestazione->nome ?> </h1>
            </th>
        </tr>
        <tr class="text-center">
            <th>Categoria</th>
            <th>Gara</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($gare as $gara) {
            ?>

            <tr data-href="/?p=visual_startlist&id_gara=<?= $gara->id ?>">
                <td>
                    <?= $gara->categoria ?>
                </td>
                <td>
                    <?= $gara->outputNome() ?>
                </td>
                <td class="text-center">
                    <a class="btn-sm btn-dark enabled" href="/?p=visual_startlist&id_gara=<?= $gara->id ?>"> Visualizza </a>
                </td>
            </tr>

        <?
        }
        ?>
    </tbody>
</table>
<?
//squadre partecipanti
$query_squadre = mysqli_query($conn, "SELECT sq.nome,sq.id as id_squadra FROM Gara g INNER JOIN partecipazione p ON p.id_gara = g.id INNER JOIN atleta a ON a.id = p.id_atleta INNER JOIN squadra sq ON sq.id = a.id_squadra WHERE g.id_manifestazione = '$manifestazione->id' GROUP BY a.id_squadra ORDER BY sq.nome");
?>
<table class='table table-striped table-hover'>
    <thead class="thead-dark">
        <tr class="text-center">
            <th colspan="5">
                <h1 class="h3">Squadre partecipanti</h1>
            </th>
        </tr>
    </thead>
    <tbody>
        <?
        while ($sq_part = mysqli_fetch_assoc($query_squadre)) {
            echo "<tr data-href='/?p=visual_squadra&id_squadra=" . $sq_part['id_squadra'] . "'><td>" . $sq_part['nome'] . "</td></tr>";
        }
        ?>
    </tbody>
</table>