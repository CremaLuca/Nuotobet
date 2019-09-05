<?php
include_once("includes/db.inc.php");
include_once("classes/manifestazione.class.php");
include_once("classes/articolo.class.php");

$manifestazioni_aperte = Manifestazione::getManifestazioniFromDate(date('Y-m-d H:m:s'));
$manifestazioni_passate = Manifestazione::getManifestazioniFromDate(null,date('Y-m-d H:m:s'));
$articoli = Articolo::getArticoli(5);
?>
<title>Nuotobet - Home</title>
<meta name="description" content="Nuotobet, la prima e unica applicazione web per scommettere sull e gare regionali in Veneto" />
<h1 class="h2 text-center p-3">Manifestazioni aperte</h2>
    <table class="table table-striped table-hover">
        <tbody>
            <?php
            if (count($manifestazioni_aperte) > 0) {
                foreach ($manifestazioni_aperte as $manif) {
                    ?>
                    <tr data-href='/?p=visual_gara&id_manif=<?php echo $manif->id; ?>' class='text-center'>
                        <td>
                            <h1 class='h6'><? echo $manif->nome; ?></h1>
                            <div class="d-flex justify-content-between">
                                <span><? echo $manif->luogo; ?></span>
                                <span><? echo date("d/m/y", strtotime($manif->data)); ?></span>
                            </div>
                        </td>
                    </tr>
            <?
                }
            } else {
                echo "<tr><td colspan='3' class='text-center'>Al momento non ci sono manifestazioni aperte alle scommesse</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <h1 class="h2 text-center p-3">Manifestazioni passate</h2>
        <table class="table table-striped table-hover">
            <tbody>
                <?php
                if (count($manifestazioni_passate) > 0) {
                    foreach ($manifestazioni_passate as $manif) {
                        ?>
                        <tr data-href='/?p=visual_gara&id_manif=<?php echo $manif->id; ?>'>
                            <td>
                                <h1 class='h6'><? echo $manif->nome; ?></h1>
                                <div class="d-flex justify-content-between">
                                    <span><? echo $manif->luogo; ?></span>
                                    <span><? echo date("d/m/y", strtotime($manif->data)); ?></span>
                                </div>
                            </td>
                        </tr>
                <?
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>Al momento non ci sono manifestazioni passate</td></tr>";
                }
                ?>
                <tr data-href='/?p=visual_manif' class='text-center'>
                    <td colspan='5'>
                        <a class="btn-sm btn-dark" href='/?p=visual_manif'>Manifestazioni più vecchie</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="container">
            <h1 class="h2 text-center p-3">News</h2>
                <?php
                foreach ($articoli as $articolo) {
                    
                    ?>
                    <div class="card shadow p-3 mb-5 bg-white rounded">
                        <div class="card-header">
                            Notizia
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <? echo $articolo->titolo; ?>
                            </h5>
                            <p class="card-text">
                                <? echo $articolo->getShortTesto(); ?>...</p>
                            <a href="index.php?p=articoli&id=<? echo $articolo->id; ?>" class="card-link">Leggi l'articolo</a>
                        </div>
                    </div>
                <?
                }
                ?>
        </div>