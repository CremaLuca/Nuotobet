<?php
    if(isset($_POST['rimuovi_partecipazioni_senza_risultato'])){
        $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
        require_once("rules.php");
        $codice_assente = $codici_risultati['ASS'];
        $query_part_senza_res = mysqli_query($conn,"SELECT p.id as id_partecipazione FROM manifestazione m INNER JOIN gara g ON g.id_manifestazione = m.id INNER JOIN partecipazione p ON p.id_gara = g.id LEFT JOIN Risultato r ON p.id = r.id_partecipazione WHERE (data <= NOW() AND (r.tempo = '0' OR r.tempo IS NULL))");
        echo mysqli_error($conn);
        while($part = mysqli_fetch_assoc($query_part_senza_res)){
            $id_part = $part['id_partecipazione'];
            mysqli_query($conn, "INSERT INTO Risultato (id_partecipazione,tempo) VALUES ('$id_part','$codice_assente ') ON DUPLICATE KEY UPDATE id = id ");
            echo mysqli_error($conn);
        }
    }
    if(isset($_POST['modifica_nome'])){
        if(isset($_POST['id_manif']) && isset($_POST['nome'])){
            $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
            $id_manif = $_POST['id_manif'];
            $nome = $_POST['nome'];
            mysqli_query($conn,"UPDATE Manifestazione SET nome='$nome' WHERE id = '$id_manif' ");
            if(mysqli_error($conn)){
                header('HTTP/1.1 500 Server error');
                echo "Errore lato server: ".mysqli_error($conn);
            }else{
                echo "Nome della manifestazione aggiornato";
            }
        }else{
            header('HTTP/1.1 500 Server error');
            echo "Errore lato server: Mancano dei dati";
        }
    }
    if(isset($_POST['modifica_data'])){
        if(isset($_POST['id_manif']) && isset($_POST['data'])){
            $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
            $id_manif = $_POST['id_manif'];
            $data = $_POST['data'];
            mysqli_query($conn,"UPDATE Manifestazione SET data='$data' WHERE id = '$id_manif' ");
            if(mysqli_error($conn)){
                header('HTTP/1.1 500 Server error');
                echo "Errore lato server: ".mysqli_error($conn);
            }else{
                echo "Data della manifestazione aggiornato";
            }
        }else{
            header('HTTP/1.1 500 Server error');
            echo "Errore lato server: Mancano dei dati";
        }
    }
    if(isset($_POST['modifica_luogo'])){
        if(isset($_POST['id_manif']) && isset($_POST['luogo'])){
            $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
            $id_manif = $_POST['id_manif'];
            $luogo = $_POST['luogo'];
            mysqli_query($conn,"UPDATE Manifestazione SET luogo = '$luogo' WHERE id = '$id_manif'");
            echo mysqli_error($conn);
            if(mysqli_error($conn)){
                header('HTTP/1.1 500 Server error');
                echo "Errore lato server: ".mysqli_error($conn);
            }else{
                echo "Luogo della manifestazione aggiornato in $luogo, manif id $id_manif ".mysqli_error($conn);
            }
        }else{
            header('HTTP/1.1 500 Server error');
            echo "Errore lato server: Mancano dei dati";
        }
    }
?>