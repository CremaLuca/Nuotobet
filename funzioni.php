<?php

function refresh($time = 0,$url = ""){
    echo "<meta http-equiv='refresh' content='$time;url=$url'>";
}

function calcoloQuota($scommessa, $tempo_iscrizione, $mediana){
    require("rules.php");
    $quota = 1 + ($quote_fisse[$scommessa] * (2*($mediana/$tempo_iscrizione) - 0.95 ));
    if($quota < 1.01)
        $quota = 1.01;
    return round($quota,2);
}

function calcoloMoltiplicatore($n_scommesse){
    //1 1.8 2.6 3.4 4.2 5.0
    return round(1 + ($n_scommesse-1) * 0.8,2);
}

function calcoloQuotaVincita($quote_vincite,$n_scommesse,$n_perdite){
    $n_vincite = $n_scommesse - $n_perdite;
    $quota = round($quote_vincite * calcoloMoltiplicatore($n_vincite) * pow(0.8,$n_perdite),2);
    return ($quota > 0) ? $quota : 0;
}

function calcoloVincita($quote_vincite,$n_scommesse,$n_perdite,$crediti){
    return round(calcoloQuotaVincita($quote_vincite,$n_scommesse,$n_perdite) * $crediti,0);
}

function translateAthleteName($athlete_name){
    return preg_replace("/[aeiouAEIOU]/","*",$athlete_name);
}

function retrieveAtletheName($db_name){
    $weird_name = $db_name;
    $vowels = "AEIOU";
    for($i=0;$i<strlen($weird_name);$i++){
        if($weird_name[$i] == "*"){
            $weird_name[$i] = $vowels[mt_rand(0,4)];
        }
    }
    return $weird_name;
}

function calcoloTempo($scommessa, $tempo_iscrizione, $metri){
    require("rules.php");
    $scarto = $scarti_scommesse[$metri];
    switch($scommessa){
        case "<<":
            return array(0,$tempo_iscrizione - 1 - (3*$scarto/2));
        break;
        case "<":
            return array($tempo_iscrizione - (3*$scarto/2), $tempo_iscrizione - 1 - ($scarto/2));
        break;
        case "=":
            return array($tempo_iscrizione - ($scarto/2), $tempo_iscrizione + ($scarto/2));
        break;
        case ">":
            return array($tempo_iscrizione + 1 + ($scarto/2), $tempo_iscrizione + (3*$scarto/2));
        break;
        case ">>":
            return array($tempo_iscrizione + (3*$scarto/2) + 1, $tempo_iscrizione * 2 );
        break;
        case "dsq":
            return array(-1,-1);
        break;
    }
}

function GetOutputFromDBResTime($db_res_time){
    require("rules.php");
    require_once("includes/time.inc.php");
    switch($db_res_time){
        case $codici_risultati['ASS']:
            $db_res_time = "Assente";
        break;
        case $codici_risultati['SQU']:
            $db_res_time = "Squalificato";
        break;
        case $codici_risultati['RIT']:
            $db_res_time = "Ritirato";
        break;
        default:
            $db_res_time = sede_sege($db_res_time);
        break;
    }
    return $db_res_time;
}

function curlPost($postUrl, $postFields) {
     
    $useragent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3'; // Setting useragent of a popular browser
     
    $cookie = 'cookie.txt'; // Setting a cookie file to store cookie
     
    $ch = curl_init();  // Initialising cURL session
 
    // Setting cURL options
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // Prevent cURL from verifying SSL certificate
    curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);    // Script should fail silently on error
    curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);  // Use cookies
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); // Follow Location: headers
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Returning transfer as a string
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);  // Setting cookiefile
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);   // Setting cookiejar
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);    // Setting useragent
    curl_setopt($ch, CURLOPT_URL, $postUrl);    // Setting URL to POST to
             
    curl_setopt($ch, CURLOPT_POST, TRUE);   // Setting method as POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);  // Setting POST fields as array
             
    $results = curl_exec($ch);  // Executing cURL session
    curl_close($ch);    // Closing cURL session
     
    return $results;
}

function manifDate($manifDate){
    return date("d/m/y",strtotime($manifDate));
}

?>