<?php
//Senza le classi: 10ms
include_once("includes/time.inc.php");
include_once("rules.php");
include_once("classes/partecipazione.class.php");
$conn = mysqli_connect("localhost", "nuotobet", "password", "my_nuotobet");
if (!isset($_GET['id_part'])) {
   refresh(0, '/p=visual_manif');
}
$partecipazione = new Partecipazione($_GET['id_part']);
$partecipazione->getData();
$partecipazione->getRisultato();
$partecipazione->getPartecipazioniPrecedenti();
$gara = $partecipazione->gara;
$manifestazione = $gara->manifestazione;
$mediana = $gara->calcMediana();
$part_precedenti = $partecipazione->partecipazioni_precedenti;

//Aggiungiamo i bottoni solo se ha fatto l'accesso
$button_disabled = "disabled";
$class_active = "disabled";
if (isset($_SESSION['id_utente'])) {
    $button_disabled = "";
    $class_active = "";
}
?>
<style>
    .bet-group {
        width: 100%;
        border: 0;
    }

    .bet-group table {
        margin: 1rem;
    }

    .bet-group table tr td,
    .bet-group table tr th {
        padding: .5rem;
    }

    .bet-group button.btn {
        min-width: 8rem;
    }

    .bet-group .card-body {
        padding: 0.5rem;
    }

    .bet-group .card-header {
        display: flex;
        align-items: center !important;
        justify-content: space-between !important;
    }
</style>
<title>Nuotobet - <?= $partecipazione->atleta->outputNome() . " " . $gara->outputNome() ?></title>
<meta name="description" content="Partecipazione di <?= $partecipazione->atleta->outputNome() ?> ai <?= $gara->outputNome() ?>" />
<form method="post" action="/?p=agg_scom">
    <input type="hidden" name="id_part" value="<?= $partecipazione->id ?>">
    <table class="table">
        <thead class="thead-dark">
            <tr class="text-center">
                <th colspan="6">
                    <h1 class="h2">
                        <?= $partecipazione->atleta->outputNome() . " " . $gara->outputNome() ?>
                    </h1>
                </th>
            </tr>
            <tr class="text-center">
                <th colspan="6">
                    <a href='/?p=visual_atleta&id_atleta=<?= $partecipazione->atleta->id ?>' class='btn btn-sm btn-secondary'>
                        Visualizza pagina atleta
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <th colspan="3" scope="row">Tempo iscrizione</th>
                <td colspan="3">
                    <?= $partecipazione->outputTempoIscrizione() ?>
                </td>
            </tr>
            <!-- <tr class="text-center">   
            <th colspan="5">Scommesse disponibili</th>
        </tr> -->
            <?
            $oggi = date("Y-m-d H:i:s");
            if ($oggi >= $manifestazione->data) {
                if ($gara->conclusa) { //Se sono già usciti i risultati
                    ?>
                    <tr class="text-center">
                        <th colspan="3" scope="row">Risultato</th>
                        <td colspan="3" class='text-<?= $partecipazione->outputRisultatoColor() ?> '>
                            <?= $partecipazione->outputCompletoRisultato() ?>
                        </td>
                    </tr>
            <?
                }
            }
            ?>
        </tbody>
    </table>
    <?php
    if (count($part_precedenti) > 0) {
        ?>
        <table class='table'>
            <thead class='thead-dark'>
                <tr class="text-center">
                    <th colspan="6">
                        <h1 class="h2">
                            Risultati precedenti
                        </h1>
                    </th>
                </tr>
                <tr class="text-center">
                    <th>
                        Manifestazione
                    </th>
                    <th>
                        Data
                    </th>
                    <th>
                        Tempo iscrizione
                    </th>
                    <th>
                        Risultato
                    </th>
                </tr>
            </thead>
            <tbody>
                <?
                    foreach ($part_precedenti as $part_passata) {
                        ?>
                    <tr class='text-center'>
                        <td><?= $part_passata->gara->manifestazione->outputInfo() ?></td>
                        <td><?= $part_passata->gara->manifestazione->outputData() ?></td>
                        <td><?= $part_passata->outputTempoIscrizione() ?></td>
                        <td><?= $part_passata->outputRisultato() ?></td>
                    </tr>
                <?
                    }
                    ?>
            </tbody>
        </table>
    <?
    } //end if num rows query res prec > 0
    if (!isset($_SESSION['username'])) {
        ?>
        <div class='container text-center'>
            <div class='alert alert-warning p-3 m-3 d-inline-block'>
                <table>
                    <tr>
                        <td rowspan='2' class='px-1'>
                            <i class="fa fa-exclamation-triangle" title="Manifestazioni"></i>
                        </td>
                        <td class='px-2'>Non puoi scommettere su questa gara perchè <strong>non hai fatto l'accesso.<strong>
                        </td>
                    </tr>
                    <tr class='text-center'>
                        <td class='px-2'><a class='btn btn-sm btn-primary mt-2' href='/landing.php'>Clicca qui per
                                effettuare l'accesso</a></td>
                    </tr>
                </table>
            </div>
        </div>
    <?
    } else if ($oggi <= $manifestazione->data) {
        ?>
        <div class='d-flex flex-row flex-wrap m-0'>
            <div class='bet-group card'>
                <div class='card-header' data-target='#low-bet-group'>
                    <h4 class='m-1'>Tempo inferiore</h4>
                    <i class='fa fa-arrow-down'></i>
                </div>
                <div class='card-body collapse' id='low-bet-group'>
                    <div class='d-flex'>
                        <table class='text-center'>
                            <tbody>
                                <tr>
                                    <td colspan='2'>
                                        <h5>Molto meno</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tempo</th>
                                    <td>
                                        <? $t = calcoloTempo("<<", $tempo_iscr, $metri);
                                            echo "<= " . sede_sege($t[1]); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quota</th>
                                    <td>
                                        <? echo calcoloQuota("<<", $tempo_iscr, $mediana); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'><button class="btn btn-primary <? echo $class_active; ?>" name="scomm" value="<<" <? echo $button_disabled; ?>> &lt&lt </button></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class='text-center'>
                            <tbody>
                                <tr>
                                    <td colspan='2'>
                                        <h5>Meno</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tempo</th>
                                    <td>
                                        <? $t = calcoloTempo("<", $tempo_iscr, $metri);
                                            echo sede_sege($t[0]) . " - " . sede_sege($t[1]); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quota</th>
                                    <td>
                                        <? echo calcoloQuota("<", $tempo_iscr, $mediana); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'><button class="btn btn-primary <? echo $class_active; ?>" name="scomm" value="<" <? echo $button_disabled; ?>>&lt </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class='bet-group card'>
                <div class='card-header' data-target='#same-bet-group'>
                    <h4 class='m-1'>Tempo uguale</h4>
                    <i class='fa fa-arrow-down'></i>
                </div>
                <div class='card-body collapse' id='same-bet-group'>
                    <div class='d-flex'>
                        <table class='text-center'>
                            <tbody>
                                <tr>
                                    <td colspan='2'>
                                        <h5>Uguale</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tempo</th>
                                    <td>
                                        <? $t = calcoloTempo("=", $tempo_iscr, $metri);
                                            echo sede_sege($t[0]) . " - " . sede_sege($t[1]); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quota</th>
                                    <td>
                                        <? echo calcoloQuota("=", $tempo_iscr, $mediana); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'><button class="btn btn-primary <? echo $class_active; ?>" name="scomm" value="=" <? echo $button_disabled; ?>> ≃ </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class='bet-group card'>
                <div class='card-header' data-target='#high-bet-group'>
                    <h4 class='m-1'>Tempo maggiore</h4>
                    <i class='fa fa-arrow-down'></i>
                </div>
                <div class='card-body collapse' id='high-bet-group'>
                    <div class='d-flex'>
                        <table class='text-center'>
                            <tbody>
                                <tr>
                                    <td colspan='2'>
                                        <h5>Maggiore</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tempo</th>
                                    <td>
                                        <? $t = calcoloTempo(">", $tempo_iscr, $metri);
                                            echo sede_sege($t[0]) . " - " . sede_sege($t[1]); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quota</th>
                                    <td>
                                        <? echo calcoloQuota(">", $tempo_iscr, $mediana); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'><button class="btn btn-primary <? echo $class_active; ?>" name="scomm" value=">" <? echo $button_disabled; ?>> &gt </button></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class='text-center'>
                            <tbody>
                                <tr>
                                    <td colspan='2'>
                                        <h5>Molto maggiore</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tempo</th>
                                    <td>
                                        <? $t = calcoloTempo(">>", $tempo_iscr, $metri);
                                            echo ">= " . sede_sege($t[0]); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quota</th>
                                    <td>
                                        <? echo calcoloQuota(">>", $tempo_iscr, $mediana); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'><button class="btn btn-primary <? echo $class_active; ?>" name="scomm" value=">>" <? echo $button_disabled; ?>> &gt&gt </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class='bet-group card'>
                <div class='card-header' data-target='#extra-bet-group'>
                    <h4 class='m-1'>Extra</h4>
                    <i class='fa fa-arrow-down'></i>
                </div>
                <div class='card-body collapse' id='extra-bet-group'>
                    <div class='d-flex'>
                        <table class='text-center'>
                            <tbody>
                                <tr>
                                    <td colspan='2'>
                                        <h5>Squalifica</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'>Squalifica</td>
                                </tr>
                                <tr>
                                    <th>Quota</th>
                                    <td>
                                        <? echo calcoloQuota("dsq", $tempo_iscr, $mediana); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'><button class="btn btn-primary <? echo $class_active; ?>" name="scomm" value="=" <? echo $button_disabled; ?>> ≃ </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?
    }
    ?>
</form>