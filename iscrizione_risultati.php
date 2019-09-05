<script src="decode_time.js"></script>
<script>
function setTempo(id_input,codice_tempo){
    console.log("Ciao");
    console.log("Tempo decriptato: " + decryptString(codice_tempo));
    $('#input_tempo_'+id_input).val(decryptString(codice_tempo));
}

function decryptString(encrypted){
    encrypted = dehexify(encrypted);
    decrypted = cryptN(encrypted,"242174266C406E44323131363335",false,16);
    return decrypted;
}
</script>
 
<?php
//Obiettivi:
// Passiamo tutte le GARE di manifestazioni con data <= oggi che non abbiano un RISULTATO
// Controlliamo nella pagina della manifestazione e per ogni riga di gara controlliamo se esiste il link dei risultati
// Aggiungiamo alla tabella RISULTATI i tempi
$conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
if(!isset($_GET['id_manif'])){
    $query_manif_gare_senza_risultati = mysqli_query($conn,"SELECT m.nome,m.id_finveneto,m.luogo,r.tempo FROM manifestazione m INNER JOIN gara g ON g.id_manifestazione = m.id INNER JOIN partecipazione p ON p.id_gara = g.id LEFT JOIN Risultato r ON p.id = r.id_partecipazione WHERE (data <= NOW() AND (r.tempo = '0' OR r.tempo IS NULL)) GROUP BY m.id ORDER BY data DESC");
    echo mysqli_error($conn)."<br/>";
    echo "<div class='text-center'>";
    while($manif_senza_res = mysqli_fetch_assoc($query_manif_gare_senza_risultati)){
        if($manif_senza_res)
        echo "<a class='btn btn-outline-success my-3' href='/?p=admin&adminPage=iscrizione_risultati&id_manif=".$manif_senza_res['id_finveneto']."' role='button'>".$manif_senza_res['nome']." ".$manif_senza_res['luogo']." ".$manif_senza_res['tempo']."</a><br/>";
    }
    echo "</div>";
}else if(isset($_POST['importa_risultati'])){
    include_once("includes/time.inc.php");
    include_once("rules.php");

    //Qui è la raccolta dati, procediamo
    $num_righe = $_POST['n_righe'];
    for($i = 1; $i <= $num_righe;$i ++){
        $id_partecipazione = $_POST['partecipazione_'.$i];
        $tempo_risultato = $_POST['tempo_'.$i];
        if($tempo_risultato == 'ASS'){
            $tempo_risultato = $codici_risultati['ASS'];
        }
        else if($tempo_risultato == 'SQU'){
            $tempo_risultato = $codici_risultati['SQU'];
        }else if($tempo_risultato == 'RIT'){
            $tempo_risultato = $codici_risultati['RIT'];
        }else{
            $tempo_risultato = sege_sede($tempo_risultato);
        }
        mysqli_query($conn, "INSERT INTO Risultato (id_partecipazione,tempo) VALUES ('$id_partecipazione','$tempo_risultato') ON DUPLICATE KEY UPDATE tempo = '$tempo_risultato' ");
        echo mysqli_error($conn);
    }
    //Ora controllo tutte quelle schedine aperte con scommesse che hanno risultati
    //Siamo sicuri che siano scommesse cattive perchè utilizziamo inner join risultato e where aperta = 1
    //Alla fine le schedine rimangono ma solo con scommesse senza risultati
    $query_scommesse = mysqli_query($conn,"DELETE sc FROM scommessa sc INNER JOIN schedina s ON s.id = sc.id_schedina INNER JOIN Risultato r ON sc.id_partecipazione = r.id_partecipazione WHERE s.aperta = 1 ");
    echo mysqli_error($conn);
        ?>
        <div class='alert alert-success' role='alert'>Se non ci sono errori hai importato i risultati con successo</div>
        <a href='/?p=admin&adminPage=iscrizione_risultati' class='btn btn-primary'>Registra altri risultati</a>
        <a href='/?p=admin' class='btn btn-primary'>Torna all'amministrazione</a>
        <a href='/?' class='btn btn-primary'>Home</a>
        <?
}else{
    include_once("simple_html_dom.php");
    
    $id_finveneto_manifestazione = $_GET['id_manif'];
    echo "Ho l'indicazione di recuperare da finveneto i risultati della gara id: $id_finveneto_manifestazione <br/>";
    
    
    $html_manifestazione = file_get_html("http://www.finveneto.org/nuoto_schedamanifestazione.php?id_manifestazione=$id_finveneto_manifestazione");
    $nome_manifestazione = mysqli_real_escape_string($conn, $html_manifestazione->find("div.col-C div.col-centro h3",0)->plaintext);
    $data_manifestazione = date('Y-m-d', strtotime(str_replace('/', '-', explode(" ",$html_manifestazione->find("div.slide div.slide_contenuto table.tab[summary=Programma manifestazione] tbody.tab tr td.tabcolspan",0)->plaintext)[1])));
    echo "<h1 class='h2'>$nome_manifestazione in data $data_manifestazione</h2>";
    if(!isset($nome_manifestazione)){
        echo "Non ho trovato nome o data <br/>";
    }else{
        echo "<form method='post'> <div class='text-center'><button type='submit' name='importa_risultati' class='btn btn-primary'>Importa risultati</button></div> <table class='table'>";
        $counter_input = 0;
        $query_manif = mysqli_query($conn,"SELECT id FROM manifestazione WHERE id_finveneto = '$id_finveneto_manifestazione'");
        $id_manifestazione = mysqli_fetch_assoc($query_manif)['id'];
        foreach($html_manifestazione->find("div.slide_contenuto table.tab[summary=Programma manifestazione] tbody.tab tr") as $riga_gara){
            $cont_riga = 0;
            unset($categoria_gara,$metri_gara, $stile_gara,$turno_gara);
            foreach($riga_gara->find("td") as $colonna){
                $cont_riga++; //Ovviamente si parte da 1
                switch($cont_riga){
                    case 1:
                        //Orario
                        break;
                    case 2:
                        $categoria_gara = $colonna->plaintext;
                            break;
                    case 3:
                        if(substr($colonna->plaintext,0,5) == "Staff"){
                            //Skippiamo le staffette e andiamo avanti
                            continue;
                        }
                        $specialita_exp = explode(" ",$colonna->plaintext, 2); //Dividi in due, al massimo due pezzi, così abbiamo metri e stile
                        $metri_gara = $specialita_exp[0];
                        $stile_gara = $specialita_exp[1];
                    case 4:
                        //Turno
                        $turno_gara = $colonna->plaintext;
                        break;
                    case 5:
                        //Vogliamo prendere il secondo link, quello dei risultati
                        $link_startlist = "http://www.finveneto.org/".$colonna->find("a",1)->href;
                        break;
                }
            }
            //Ora abbiamo le informazioni della Gara, apriamo la startlist con i risultati
            if(isset($categoria_gara) && isset($metri_gara) && isset($stile_gara)){
                if($turno_gara != "Finale"){
                echo "<tr><th>$metri_gara $stile_gara</th><th>$categoria_gara</th> </tr>";
                $query_gara = mysqli_query($conn, "SELECT id FROM Gara g WHERE g.id_manifestazione = '$id_manifestazione' AND g.metri = '$metri_gara' AND g.stile = '$stile_gara' AND g.categoria = '$categoria_gara' ");
                if(mysqli_num_rows($query_gara) > 0){
                    $id_gara = mysqli_fetch_assoc($query_gara)['id'];

                    $html_startlist_risultati = file_get_html($link_startlist);
                    if(isset($html_startlist_risultati)){
                        foreach($html_startlist_risultati->find("div.col-C div.col-centro table.tab[summary=Risultati Totali] tbody.tab tr[!id]") as $riga_risultati){
                            $cont_riga_part = 0;
                            unset($nome_atleta,$anno_atleta,$squadra_atleta,$codice_tempo);
                            foreach($riga_risultati->find("td") as $colonna_risultati){
                                $cont_riga_part++;
                                switch($cont_riga_part){
                                    case 4:
                                        $nome_atleta = mysqli_real_escape_string($conn, $colonna_risultati->find("b",0)->plaintext);
                                    break;
                                    case 5:
                                        $anno_atleta = $colonna_risultati->plaintext;
                                    break;
                                    case 6:
                                        $squadra_atleta = mysqli_real_escape_string($conn,$colonna_risultati->plaintext);
                                    break;
                                    case 7:
                                        $codice_tempo = explode('"',$colonna_risultati->find("script")[0],3)[1];
                                    break;
                                }
                            }
                            if(isset($nome_atleta) && isset($anno_atleta) && isset($squadra_atleta) && isset($codice_tempo)){
                                $nome_atleta = translateAthleteName($nome_atleta);
                                $query_squadra = mysqli_query($conn,"SELECT id FROM squadra WHERE nome='$squadra_atleta'");
                                if(mysqli_num_rows($query_squadra) > 0){
                                    $id_squadra = mysqli_fetch_assoc($query_squadra)['id'];
                                }else{
                                    $query_traduzione_squadra = mysqli_query($conn,"SELECT id_squadra FROM traduzione_squadra WHERE nome = '$squadra_atleta' LIMIT 1");
                                    //TODO : aggiungere if mysqli_num > 0
                                    $id_squadra = mysqli_fetch_assoc($query_traduzione_squadra)['id_squadra'];
                                }
                                $query_atleta = mysqli_query($conn,"SELECT a.id FROM atleta a WHERE a.nome = '$nome_atleta' AND a.anno = '$anno_atleta' AND a.id_squadra = '$id_squadra' LIMIT 1");
                                echo "<tr></td>".mysqli_error($conn)."</td></tr>";
                                if(mysqli_num_rows($query_atleta) > 0){
                                    $id_atleta = mysqli_fetch_assoc($query_atleta)['id'];
                                    $query_partecipazione = mysqli_query($conn,"SELECT id, tempo_iscrizione FROM partecipazione WHERE id_gara = '$id_gara' AND id_atleta='$id_atleta' ");
                                    if(mysqli_num_rows($query_partecipazione) > 0){
                                        $id_partecipazione = mysqli_fetch_assoc($query_partecipazione)['id'];
                                        $counter_input++;
                                        ?>
                                        <tr>
                                        	<td>
                                                <input type='text' placeholder='<? echo $codice_tempo; ?>' id='<? echo "input_tempo_$counter_input"; ?>' name='<? echo "tempo_$counter_input"; ?>'>
                                                <script>setTempo(<? echo "'$counter_input','$codice_tempo'"; ?>);</script>
                                            </td>
                                            <td>
                                                <? echo $nome_atleta; ?>
                                            </td>
                                            <td>
                                            <? echo $anno_atleta; ?>
                                            </td>
                                            <td>
                                            <? echo $squadra_atleta; ?>
                                            </td>
                                            <td>
                                                <input type='text' name='<? echo "partecipazione_$counter_input"; ?>' value='<? echo $id_partecipazione; ?>'>
                                            </td>
                                        </tr>
                                        <?
                                    }else{
                                        echo "<tr><td>Partecipazione non esistente? $nome_atleta $anno_atleta $squadra_atleta</td></tr>";
                                    }
                                }else{
                                    echo "<tr><td>Atleta non registrato? Non è possibile</td></tr>";
                                }
                            }else{
                                "<tr><td>Questa riga non è il risultato di una persona</td></tr>";
                            }
                    }
                    }else{
                        echo "<tr><td>Non sono riuscito a caricare la startlist dei risultati</td></tr>";
                    }
                }else{
                    echo "<tr><td>$metri_gara $stile_gara non è presente nel database</td></tr>";
                }
            }else{
                echo "<tr><td>Questa gara è una finale, a noi non interessano le finali</td></tr>";
            }
            }else{
                echo "<tr><td>Manca qualcosa per questa gara".isset($categoria_gara).isset($metri_gara).isset($stile_gara)."</td></tr>";
            }
        }
        echo "</table><input type='hidden' name='n_righe' value='$counter_input'></form>";
    }
}
?>