<?php

//Cosa deve fare questo script?
//Deve passare per tutte le scommesse con stato 0 e controllare se c'è il riusltato per quella partecipazione
//Nel caso ci dovesse essere controllo se la scommessa è vinta
//Una volta controllate tutte le scommesse con stato 0 controllo tutte le schedine solo di quelle scommesse modificate e controllo se
//Tutte sono con stato != 0, per ogni scommessa persa dimezzo la vincita
//STATI: -1 PERSA, 0 BOH, 1 VINTA

require_once("funzioni.php");
require_once("rules.php");
$conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");

$array_schedine_modificate = array();

echo "<h1 class='h3'>Controllo scommesse</h3>";
$query_scommesse = mysqli_query($conn,"DELETE sc FROM scommessa sc INNER JOIN schedina s ON s.id = sc.id_schedina INNER JOIN Risultato r ON sc.id_partecipazione = r.id_partecipazione WHERE s.aperta = '1' ");
echo mysqli_error($conn);
$query_scommesse_aperte = mysqli_query($conn,"SELECT sc.id,sc.tempo_min,sc.tempo_max,sc.id_partecipazione,sc.id_schedina FROM scommessa sc INNER JOIN schedina s ON s.id = sc.id_schedina WHERE sc.stato = '0' AND s.aperta='0' ORDER BY sc.id_schedina");
while($scommessa = mysqli_fetch_assoc($query_scommesse_aperte)){
    $id_scommessa = $scommessa['id'];
    $id_partecipazione = $scommessa['id_partecipazione'];
    $tempo_min = $scommessa['tempo_min'];
    $tempo_max = $scommessa['tempo_max'];
    $query_partecipazione = mysqli_query($conn,"SELECT tempo FROM Risultato WHERE id_partecipazione = '$id_partecipazione' ");
    if(mysqli_num_rows($query_partecipazione) >= 1){
        $tempo_risultato = mysqli_fetch_assoc($query_partecipazione)['tempo'];
        if($tempo_risultato == $codici_risultati['ASS'] || $tempo_risultato == $codici_risultati['RIT']){
            //L'atleta era assente, settiamo la quota a 0 però mettiamola vinta
            mysqli_query($conn,"UPDATE Scommessa SET quota = '0', stato = '1' WHERE id='$id_scommessa'");
        }else if($tempo_risultato == $codici_risultati['SQU']){
            if($tempo_max == -1 && $tempo_min == -1){
                //Ha vinto, è proprio stato squalificato, incredibile
                mysqli_query($conn,"UPDATE Scommessa SET stato = '1' WHERE id='$id_scommessa'");
            }else{
                //Mi dispiace ma non è stato squalificato
                mysqli_query($conn,"UPDATE Scommessa SET stato = '-1' WHERE id='$id_scommessa'");
            }
        }else{
            if($tempo_risultato > $tempo_min && $tempo_risultato < $tempo_max){
                //Allora abbiamo vinto la scommessa!
                mysqli_query($conn,"UPDATE Scommessa SET stato = '1' WHERE id='$id_scommessa'");
            }else{
                //Scommessa persa, siamo fuori da tempo min e tempo max
                mysqli_query($conn,"UPDATE Scommessa SET stato = '-1' WHERE id='$id_scommessa'");
            }
        }
        array_push($array_schedine_modificate,$scommessa['id_schedina']);
    }else{
        echo "Non abbiamo ancora i risultati per la partecipazione $id_partecipazione<br/>";
    }
}

//Controllo se sono tutte chiuse, se non lo sono skippo il giro

echo "<h1 class='h3'>Controllo schedine con tutte le scommesse</h3>";

foreach ($array_schedine_modificate as $id_schedina) {
    $query_schedina = mysqli_query($conn,"SELECT u.username, s.id_utente, s.crediti,s.conclusa FROM schedina s INNER JOIN Utente u ON u.id = s.id_utente WHERE s.id = '$id_schedina'");
    $schedina = mysqli_fetch_assoc($query_schedina);
    if($schedina['conclusa'] == 1){
        continue;
    }
    $query_scommessa = mysqli_query($conn,"SELECT sc.stato,sc.quota FROM scommessa sc WHERE sc.id_schedina = '$id_schedina' ");
    echo mysqli_error($conn);
    $n_scommesse = mysqli_num_rows($query_scommessa);
    $n_perse = 0;
    $quota_vincite = 1;
    
    while($scommessa = mysqli_fetch_assoc($query_scommessa)){
        switch($scommessa['stato']){
            case 0:
                continue 2; //passa alla prossima schedina
            break;
            case 1:
                $quota_vincite *= $scommessa['quota'];
            break;
            case -1:
                $n_perse++;
            break;
        }
    }
    //Arrivati a questo punto abbiamo saltato il continue e siamo sicuri di avere quello che ci serve
    //Formula quota finale = moltipl_quote_vittorie * (0.8 * scommesse_perse)
    $quota_finale = calcoloQuotaVincita($quota_vincite,$n_scommesse,$n_perse);
    $crediti_vinti = calcoloVincita($quota_vincite,$n_scommesse,$n_perse,$schedina['crediti']);

    $username = $schedina['username'];
    $id_utente = $schedina['id_utente'];
    echo "$username ha vinto $crediti_vinti con ".($n_scommesse-$n_perse)." vinte e $n_perse perse e una quota finale di $quota_finale dalla schedina $id_schedina<br/>";
    mysqli_query($conn,"UPDATE Utente SET crediti = crediti + '$crediti_vinti' WHERE id = '$id_utente' ");
    echo mysqli_error($conn);
    mysqli_query($conn, "UPDATE Schedina SET conclusa='1' WHERE id='$id_schedina' ");
    echo mysqli_error($conn);
}

?>