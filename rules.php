<?php
    //Scarti per capire quando uno è nel circa uguale, più lento/veloce oppure molto più lento/veloce
    $scarti_scommesse = [
        "50" => 40,
        "100" => 80,
        "200" => 140,
        "400" => 280,
        "800" => 400,
        "1500" => 800,
    ];
    $quote_fisse = [
        "<<" => 0.8,
        "<" => 0.5,
        "=" => 0.3,
        ">" => 0.2,
        ">>" => 0.25,
        "dsq" => 4,
    ];

    $codici_risultati = [
        "RIT" => -3,
        "ASS" => -2,
        "SQU" => -1,
    ];

?>