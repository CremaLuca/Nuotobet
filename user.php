<?php
    include_once("includes/time.inc.php");
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    if(!isset($_SESSION['id_utente'])){
        
?>
    <div class='alert alert-primary' role="alert">
        Per visualizzare la pagina utente devi <a href="/landing.php" class="alert-link">aver effettuato l'accesso</a>
    </div>
<?php
    }else{
        //In ordine vogliamo visualizzare prima tutte le impostazioni sotto forma di bottoni, poi la lista delle schedine
        $id_utente = $_SESSION['id_utente'];
        $username = $_SESSION['username'];
?>
<title>Nuotobet - Pagina utente</title>
<meta name="description" content="Pagina dell'utente" />
<h1 class='h1 text-center m-4'>Schedine chiuse</h1>
<?
        //Mostriamo la lista delle schedine che abbiamo chiuso
        $query_schedine_chiuse = mysqli_query($conn,"SELECT s.id,s.crediti,s.conclusa FROM schedina s WHERE s.id_utente = '$id_utente' AND s.aperta = '0' ORDER BY s.id ");

        while($schedina_u =  mysqli_fetch_assoc($query_schedine_chiuse)){
            $id_schedina = $schedina_u['id'];
            $quota_totale = 1;
            $quota_vincite = 1;
            $n_perse = 0;
            ?>
            <table class='table table-striped'>
                <thead class='thead-dark'>
                <tr>
                    <th class='text-center' colspan='5'><h1 class='h3'>Schedina cod. <? echo $id_schedina; ?></h1></th>
                </tr>
                <tr class='text-center'>
                    <th>Atleta</th>
                    <th>Gara</th>
                    <th>Tempo</th>
                    <?php
                        if($schedina_u['conclusa'] == 1){
                            echo "<th>Risultato</th>";
                        }
                    ?>
                    <th>Quota</th>
                </tr>
            </thead>
            <?
            $query_scommesse = mysqli_query($conn,"SELECT s.tempo_min,s.tempo_max,s.quota,a.nome,g.metri,g.stile,r.tempo,p.tempo_iscrizione,s.stato,m.nome as nome_manif,m.luogo as luogo_manif,s.id_partecipazione as id_partecipazione FROM scommessa s INNER JOIN partecipazione p ON p.id = s.id_partecipazione INNER JOIN gara g ON p.id_gara = g.id INNER JOIN manifestazione m ON m.id = g.id_manifestazione INNER JOIN atleta a ON a.id = p.id_atleta LEFT JOIN Risultato r ON p.id = r.id_partecipazione WHERE id_schedina='$id_schedina'");
            echo mysqli_error($conn);
            $last_manif = "";
            $n_scommesse = mysqli_num_rows($query_scommesse);
            while($scommessa_u = mysqli_fetch_assoc($query_scommesse)){
                $quota_totale *= $scommessa_u['quota'];
                if($scommessa_u['stato'] == 1){
                    $quota_vincite *= $scommessa_u['quota'];
                }else{
                    $n_perse++;
                }
                if($scommessa_u['nome_manif'] != $last_manif){
                    $last_manif = $scommessa_u['nome_manif'];
                    echo "<tr class='bg-primary text-white text-center'><th colspan='5'>".$last_manif." ".$scommessa_u['luogo_manif']."</th></tr>";
                }
                ?>
                <tr data-href='/?p=visual_part&id_part=<? echo $scommessa_u['id_partecipazione']; ?>'>
                    <td><? echo retrieveAtletheName($scommessa_u['nome']); ?></td>
                    <td><? echo $scommessa_u['metri']." ".$scommessa_u['stile']; ?></td>
                    <td class='text-center' >
                        <?
                        if($scommessa_u['tempo_min'] > 0) { 
                            echo sede_sege($scommessa_u['tempo_min'])." - ".sede_sege($scommessa_u['tempo_max']); 
                        } else if($scommessa_u['tempo_max'] > 0){ 
                            echo "<= ".sede_sege($scommessa_u['tempo_max']); 
                        } else {
                            echo "DSQ";
                        }
                        ?>
                    </td>
                    <?php
                    if($scommessa_u['stato'] != 0 && $schedina_u['conclusa'] == 1){ //Non basta avere un risultato, tutta la schedina deve essere completa
                        $colore_tempo = ($scommessa_u['stato'] >= 1) ? "text-success font-weight-bold" : "text-danger font-weight-bold";
                        if($scommessa_u['tempo'] == -2){
                            $scommessa_u['tempo'] = "ASSENTE";
                        }else if($scommessa_u['tempo'] == -1){
                            $scommessa_u['tempo'] = "SQUALIFICA";
                        }else{
                            $scommessa_u['tempo'] = sede_sege($scommessa_u['tempo']);
                        }
                        echo "<td class='$colore_tempo text-center'>".$scommessa_u['tempo']."</td>";
                    }
                    ?>
                    <td class='text-center'><? echo $scommessa_u['quota']; ?></td>
                </tr>
                <?
            }
            ?>
            <tr class='text-center'>
                <th>Crediti scommessi</th>
                <td><? echo $schedina_u['crediti']; ?></td>
                <?
                if($schedina_u['conclusa'] == 1){
                    echo "<td></td>";
                }
                ?>
                <?
                if($schedina_u['conclusa'] == 1){
                ?>
                    <th>Quota vincite</th>
                    <td><? echo round($quota_vincite,2); ?></td>
                <?
                }else{
                ?>
                    <th>Quota totale</th>
                    <td><? echo round($quota_totale,2); ?></td>
                <?
                }
                ?>
            </tr>
            <tr class='text-center'>
                <th>Moltiplicatore</th>
                <td><? echo calcoloMoltiplicatore($n_scommesse);?></td>
                <td></td>
                <th>Moltiplicatore effettivo (vincite)</th>
                <td><? echo calcoloMoltiplicatore($n_scommesse-$n_perse); ?></td>
            </tr>
            <tr class='text-center'>
                <th <? if($schedina_u['conclusa'] == 0) echo "colspan = '2'"; ?>>
                    Possibile vincita
                </th>
                <td <? if($schedina_u['conclusa'] == 0) echo "colspan = '2'"; ?>>
                <? echo calcoloVincita($quota_totale,$n_scommesse,0,$schedina_u['crediti']);?>
                </td>
                <?
                 if($schedina_u['conclusa'] == 1){
                ?>
                    <th>
                        Vincita effettiva
                    </th>
                    <td colspan='2'>
                    <? 
                    echo calcoloVincita($quota_vincite,$n_scommesse,$n_perse,$schedina_u['crediti']);  
                    ?>
                    </td>
                <?
                    }  
                ?>
            </tr>
            <?
            echo "</table>";
        }
    }
?>