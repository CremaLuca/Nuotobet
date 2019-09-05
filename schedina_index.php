<?php
    //Ricordo che se questa pagina viene caricata allora sicuramente c'è una schedina
    if(!isset($_SESSION['id_schedina'])){
        refresh(0,'/');
    }
    $id_schedina = $_SESSION['id_schedina'];
    $crediti_utente = $_SESSION['crediti'];
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    require_once("includes/time.inc.php")
?>
<script>
    function calcolo_vincita(quote, crediti) {
        return Math.round(quote * crediti)
    }

    function editCreditiSchedina(id_schedina, crediti) {
        $.ajax({
            url: "a_schedina.php",
            type: "post",
            data: "edit_crediti=1&id_schedina=" + id_schedina + "&crediti=" + crediti,
            success: function (response) {
                // you will get response from your php page (what you echo or print)                 
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    function checkCrediti(){
        var somma_quote = Number($("#somma_quote").text());
        var moltiplicatore = Number($("#moltiplicatore_n_quote").text());
        var crediti_scommessa = Number($("#crediti_schedina").val());
        var id_schedina = Number($("#id_schedina").val());
        if (crediti_scommessa < 10) {
            console.log("Hai messo troppi pochi crediti:" + crediti_scommessa);
            crediti_scommessa = 10;
        }
        if (crediti_scommessa > max_crediti) {
            console.log("Hai messo troppi crediti:" + crediti_scommessa + ", il massimo è " +
                max_crediti);
            crediti_scommessa = max_crediti;
        }
        console.log(crediti_scommessa);
        $("#crediti_schedina").val(crediti_scommessa);
        $("#calcolo_vincita").text(calcolo_vincita(somma_quote * moltiplicatore, crediti_scommessa));
        editCreditiSchedina(id_schedina, crediti_scommessa);
    } 
    $(document).ready(function () {
        var max_crediti = $("#max_crediti").val();
        $("#crediti_schedina").change(checkCrediti);
        checkCrediti();
        $('button#header_schedina').on("click", function () {
            $("#schedina").toggle(200,function(){
                var visible = $("#schedina").is(":visible");
                $.ajax({
                    url: "a_schedina.php",
                    type: "post",
                    data: "toggle_schedina="+visible,
                    success: function (response) {
                        // you will get response from your php page (what you echo or print)                 
                        //console.log(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            });
        });
    });
</script>
<?php
    $query_info_schedina = mysqli_query($conn,"SELECT crediti FROM schedina WHERE id = '$id_schedina'");
    $info_schedina = mysqli_fetch_assoc($query_info_schedina);
    $query_scommesse = mysqli_query($conn,"SELECT sc.id as id_scommessa,sc.tempo_min,sc.tempo_max,sc.quota,sc.scadenza,p.tempo_iscrizione,a.nome,g.metri,g.stile,m.nome as nome_manif,m.id as id_manif,m.data as data_manifestazione FROM scommessa sc INNER JOIN partecipazione p ON sc.id_partecipazione = p.id INNER JOIN atleta a ON p.id_atleta = a.id INNER JOIN gara g ON p.id_gara = g.id  INNER JOIN manifestazione m ON g.id_manifestazione = m.id WHERE sc.id_schedina = '$id_schedina' ORDER BY id_manifestazione, g.id,p.id");
    $style_schedina_visibile = "";
    if(isset($_SESSION['toggle_schedina']) && $_SESSION['toggle_schedina'] == "false"){
        $style_schedina_visibile = "display:none;";
    }
?>
<input type="hidden" id="id_schedina" value="<? echo $id_schedina; ?>">
<input type="hidden" id="max_crediti" value="<? echo $crediti_utente; ?>">
<div class="card w-100">
    <div class="card-header">
        <button class="btn btn-light w-100" id="header_schedina" type="button">
            <i class="far fa-list-alt" title="Schedina"></i>Schedina corrente
        </button>
    </div>
    <div class="row m-0 justify-content-center">
        <div class="collapse show card-body col-md-6 " id="schedina" style="<? echo $style_schedina_visibile; ?>" >
            <table class="table shadow" style='font-size:0.8rem;'>
                <thead class="thead-dark text-center">
                    <tr>
                        <th>Atleta</th>
                        <th>Gara</th>
                        <th>Tempo</th>
                        <th>Quota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                $dopodomani = new DateTime("tomorrow");
                $dopodomani = date_modify($dopodomani,"+1 day");
                $last_id = -1;
                $totale_quota = 1;
                $n_scommesse = mysqli_num_rows($query_scommesse);
                if($n_scommesse > 0){
                    while($scom = mysqli_fetch_assoc($query_scommesse)){
                        $data_manifestazione = new DateTime($scom['data_manifestazione']);
                        $totale_quota *= $scom['quota'];
                        if($scom['id_manif'] != $last_id){
                            $last_id = $scom['id_manif'];
                            echo "<tr><th colspan='4' class='p-2 text-center bg-primary text-white'>".$scom['nome_manif']."</th></tr>";
                            if($data_manifestazione <= $dopodomani){
                                echo "<tr class='bg-warning'><td class='py-1 px-2 text-center' colspan='4'>Le scommesse chiuderanno a breve</td></tr>";
                            }
                        }
            ?>
                    <tr class='text-center'>
                        <td class='p-1'><a href="/?p=visual_scom&id_scom=<? echo $scom['id_scommessa']; ?>">
                                <? echo retrieveAtletheName($scom['nome']); ?></a></td>
                        <td class='p-1'>
                            <? echo $scom['metri']." ".$scom['stile'];?>
                        </td>
                        <td class='p-1'>
                            <? if($scom['tempo_min'] > 0) { echo sede_sege($scom['tempo_min'])." - ".sede_sege($scom['tempo_max']); } else if($scom['tempo_max'] > 0){ echo "<= ".sede_sege($scom['tempo_max']); } else { echo "DSQ";} ?>
                        </td>
                        <td class='p-1'>
                            <? echo $scom['quota'];?>
                        </td>
                    </tr>
                    <?
                }
            }else{
                ?>
                    <tr class='text-center'>
                        <td colspan="4" style='padding: .4rem;'>Nessuna scommessa</td>
                    </tr>
                    <?
            }
        ?>
                    <tr>
                        <th class="align-middle text-right">Crediti scommessi:</th>
                        <td><input type='number' step='10' max="<? echo $crediti_utente; ?>" min="10"
                                style="font-size:inherit; width:5rem;" class='form-control' id='crediti_schedina'
                                name='crediti_schedina' value="<? echo $info_schedina['crediti']; ?>">
                        </td>
                        <th class="align-middle text-right">Totale quote: </th>
                        <td class="align-middle text-center" id="somma_quote">
                            <? echo round($totale_quota,2); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="align-middle text-right">Moltiplicatore: </th>
                        <td class="align-middle text-center" id="moltiplicatore_n_quote">
                            <? echo calcoloMoltiplicatore($n_scommesse); ?>
                        </td>
                        <th colspan='1' class="text-right">Possibile vincita:</th>
                        <td colspan='1' id='calcolo_vincita' class="align-middle"></td>
                    </tr>
                    <?php
                if($totale_quota > 1 && $n_scommesse > 0){
            ?>
                    <tr class="text-center align-middle">
                        <td colspan='4'><a class="btn btn-primary"
                                href="/?p=agg_sche&id_sche=<? echo $id_schedina; ?>">Concludi schedina</a></td>
                    </tr>
                    <?php
                }
            ?>
                </tbody>
            </table>
        </div>
    </div>
</div>