<?php
if(isset($_GET['url']))
    $url_pagina_manif = $_GET['url'];
    include_once("simple_html_dom.php");
    include_once("includes/time.inc.php");
    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    $html_manifestazione = file_get_html($url_pagina_manif);
    $is_fresh = isset($_GET['is_fresh']);

/// ISCRIZIONE AUTOMATICA MANIFESTAZIONI GARE E ATLETI
    echo "INIZIO: Controllo se esiste la manifestazione <br/>";

    $nome_manifestazione = mysqli_real_escape_string($conn, $html_manifestazione->find("div.col-C div.col-centro h3",0)->plaintext);
    $data_manifestazione = date('Y-m-d', strtotime(str_replace('/', '-', explode(" ",$html_manifestazione->find("div.slide div.slide_contenuto table.tab[summary=Programma manifestazione] tbody.tab tr td.tabcolspan",0)->plaintext)[1])));
    //$luogo_manifestazione = mysqli_real_escape_string($conn, $html_manifestazione->find("div.leaflet-popup-content-wrapper",0)->plaintext);
    $parti = parse_url($url_pagina_manif, PHP_URL_QUERY);
    parse_str($parti, $query_url_manif);
    $id_finveneto_manifestazione = $query_url_manif['id_manifestazione'];
    echo "Manifestazione $nome_manifestazione in data $data_manifestazione $id_finveneto_manifestazione<br/>";

    if(!isset($nome_manifestazione) && !isset($data_manifestazione)){
       echo "Non ho trovato nome o data <br/>";
    }else{
        
        $query_manifestazione = mysqli_query($conn,"SELECT id FROM manifestazione m WHERE m.nome='$nome_manifestazione' AND m.id_finveneto = '$id_finveneto_manifestazione'");
        if(mysqli_num_rows($query_manifestazione) == 0){
            echo "La manifestazione non c'è già, la aggiungo <br/>";
            mysqli_query($conn,"INSERT INTO Manifestazione (nome, data, id_finveneto) VALUES ('$nome_manifestazione','$data_manifestazione', '$id_finveneto_manifestazione')"); //Inserisco la nuova manif nel db
            $id_manifestazione = mysqli_insert_id($conn);
            echo mysqli_error($conn);
        }else{
            $id_manifestazione = mysqli_fetch_assoc($query_manifestazione)['id'];
            echo "La manifestazione esisteva già (id: $id_manifestazione), provo ad aggiornare<br/>";
        }
        //Arrivati a questo punto la manifestazione esiste nel database, ora passiamo ogni gara e aggiungiamola al database
        foreach($html_manifestazione->find("div.slide_contenuto table.tab[summary=Programma manifestazione] tbody.tab tr") as $riga_gara){
            $cont_riga = 0;
            $metri_gara = null;
            $categoria_gara = null;
            $stile_gara = null;
            $turno_gara = null;
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
                        $turno_gara =  $colonna->plaintext;
                        break;
                    case 5:
                        //Vogliamo prendere il primo link che troviamo
                        $link_startlist = "http://www.finveneto.org/".$colonna->find("a",0)->href;
                        break;
                }
            }
            //Dovremmo aver ottenuto tutte le informazioni necessarie
            if(isset($categoria_gara) && isset($metri_gara) && isset($link_startlist)){
                if($turno_gara != "Finali"){
                //Procediamo ad aggiungerla al database
                echo "Controllo $metri_gara $stile_gara $categoria_gara: ";
                $query_gara = mysqli_query($conn, "SELECT id FROM Gara g WHERE g.id_manifestazione = '$id_manifestazione' AND g.metri = '$metri_gara' AND g.stile = '$stile_gara' AND g.categoria = '$categoria_gara' ");
                echo mysqli_error($conn);
                if(mysqli_num_rows($query_gara) == 0){
                    echo "La aggiungo al database<br/>";
                    //TODO: Mettere anche il turno (e gestire le finali in generale, sarà complicato in generale)
                    mysqli_query($conn, "INSERT INTO Gara (id_manifestazione,metri,stile,categoria) VALUES ('$id_manifestazione','$metri_gara','$stile_gara','$categoria_gara') ");
                    echo mysqli_error($conn);
                    $id_gara = mysqli_insert_id($conn);
                }else{
                    $id_gara = mysqli_fetch_assoc($query_gara)['id'];
                    echo "C'era già (id: $id_gara)<br/>";
                }
                //Arrivati qui abbiamo aggiunto la gara al database e abbiamo preso l'ID (TODO: Che serva controllare?)
                //Andiamo a prendere la startlist ora
                $html_startlist = file_get_html($link_startlist);
                if(isset($html_startlist)){
                    foreach($html_startlist->find("div.col-C div.col-centro table.tab tbody.tab tr") as $riga_partecipazione){
                        //Non è detto sia partecipazione, può anche essere un intestazione
                        $cont_riga_part = 0;
                        $nome_atleta = null;
                        $anno_atleta = null;
                        $squadra_atleta = null;
                        $tempo_iscrizione = null;
                        //echo "(Sto considerando tr: $riga_partecipazione)<br/>";
                        foreach($riga_partecipazione->find("td") as $colonna_part){
                            $cont_riga_part++;
                            switch($cont_riga_part){
                                case 1:
                                if($is_fresh)
                                    $nome_atleta = mysqli_real_escape_string($conn, $colonna_part->plaintext);
                                break;
                                case 2:
                                if($is_fresh){
                                    $anno_atleta = mysqli_real_escape_string($conn,$colonna_part->plaintext);
                                }else{
                                    $nome_atleta = mysqli_real_escape_string($conn, $colonna_part->plaintext);
                                }
                                break;
                                case 3:
                                if($is_fresh){
                                    $squadra_atleta = mysqli_real_escape_string($conn,$colonna_part->plaintext);
                                }else{
                                    $anno_atleta = mysqli_real_escape_string($conn,$colonna_part->plaintext);
                                }
                                break;
                                case 4:
                                if($is_fresh){
                                    $tempo_iscrizione = sege_sede($colonna_part->plaintext);
                                }else{
                                    $squadra_atleta = mysqli_real_escape_string($conn,$colonna_part->plaintext);
                                }
                                break;
                                case 5:
                                if(!$is_fresh){
                                    $tempo_iscrizione = sege_sede($colonna_part->plaintext);
                                }
                                break;
                            }
                        }
                        //Abbiamo ciclato su tutte le colonne, dovremmo avere tutto
                        if(isset($nome_atleta) && isset($tempo_iscrizione)){
                            $nome_atleta = translateAthleteName($nome_atleta);
                            //Prendiamo l'id della squadra
                            $query_squadra = mysqli_query($conn,"SELECT id FROM squadra WHERE nome = '$squadra_atleta' LIMIT 1");
                            if(mysqli_num_rows($query_squadra) > 0){
                                $id_squadra_atleta = mysqli_fetch_assoc($query_squadra)['id'];
                            }else{
                                //Controlliamo se magari ha un nome diverso
                                $query_traduzione_squadra = mysqli_query($conn,"SELECT id_squadra FROM traduzione_squadra WHERE nome = '$squadra_atleta' LIMIT 1");
                                if(mysqli_num_rows($query_traduzione_squadra) > 0){
                                    $id_squadra_atleta = mysqli_fetch_assoc($query_traduzione_squadra)['id_squadra'];
                                    echo "Si ma questa squadra ha cambiato nome però $id_squadra_atleta<br/>";
                                }else{
                                    //prendiamo l'ultimo id della squadra
                                    $query_insert_squadra = mysqli_query($conn,"INSERT INTO Squadra(nome) VALUES ('$squadra_atleta')");
                                    $id_squadra_atleta = mysqli_insert_id($conn);
                                    echo mysqli_error($conn);
                                    echo " Ho creato una nuova squadra: $id_squadra_atleta $squadra_atleta </br>";
                                }
                            }
                           
                            //Controlliamo di averlo nella lista atleti
                            $query_atleta = mysqli_query($conn,"SELECT id FROM atleta WHERE nome = '$nome_atleta' AND anno = '$anno_atleta' AND id_squadra = '$id_squadra_atleta' ");
                            if(mysqli_num_rows($query_atleta) == 0){
                                echo "Atleta $nome_atleta, $anno_atleta, $id_squadra_atleta, $squadra_atleta, non registrato, aggiungo al db <br/>";
                                mysqli_query($conn,"INSERT INTO Atleta (nome, anno, id_squadra) VALUES ('$nome_atleta','$anno_atleta','$id_squadra_atleta') ");
                                echo mysqli_error($conn);
                                
                                $id_atleta = mysqli_insert_id($conn);
                            }else{
                                $id_atleta = mysqli_fetch_assoc($query_atleta)['id'];
                                echo "Atleta $nome_atleta $anno_atleta $id_squadra_atleta $squadra_atleta presente nel db (id: $id_atleta)<br/>";
                            }
                            //Ora sappiamo con chi abbiamo a che fare
                            //Aggiungiamo la sua partecipazione a questa gara
                            if(isset($id_atleta)){
                                $query_partecipazione = mysqli_query($conn,"SELECT id, tempo_iscrizione FROM partecipazione WHERE id_gara = '$id_gara' AND id_atleta='$id_atleta' ");
                                if(mysqli_num_rows($query_partecipazione) == 0){
                                    mysqli_query($conn,"INSERT INTO Partecipazione (id_gara,id_atleta,tempo_iscrizione) VALUES ('$id_gara','$id_atleta','$tempo_iscrizione')");
                                    echo mysqli_error($conn);
                                    echo "Partecipazione $metri_gara $stile_gara $nome_atleta aggiunta con tempo $tempo_iscrizione <br/>";
                                }else{
                                    $db_partecipazione_row = mysqli_fetch_assoc($query_partecipazione);

                                    $tempo_iscrizione_db = $db_partecipazione_row['tempo_iscrizione'];
                                    $id_partecipazione = $db_partecipazione_row['id'];
                                    if($tempo_iscrizione != $tempo_iscrizione_db){
                                        mysqli_query($conn, "UPDATE Partecipazione SET tempo_iscrizione='$tempo_iscrizione' WHERE id = '$id_partecipazione'");
                                        echo mysqli_error($conn);
                                        echo "Tempo iscrizione aggiornato<br/>";
                                    }else{
                                        echo "Partecipazione $metri_gara $stile_gara $nome_atleta già presente<br/>";
                                    }
                                }
                            }else{
                                echo "ID atleta mancante, MOLTO GRAVE <br/>";
                            }
                        }else{
                            echo "Dati mancanti per questo atleta $nome_atleta $anno_atleta <br/>";
                        }
                    }
                }else{
                    echo "HTML startlist non esistente per gara $id_gara <br/>";
                }
            }else{
                echo "Evito di importare una finale ($metri_gara $stile_gara)";
            }
            }else{
                echo "Dati mancanti per questa gara <br/>";
            }
        }

    }

?>