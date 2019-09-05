<?
    //Funzioni che vanno eseguite ogni tanto, per controllare ad esempio se sono aggiornati i soldi
    $ora = time(); //in secondi

    $update_crediti_freq = 60;//secondi

    if(isset($_SESSION['update_crediti_time'])){
        if($ora > $_SESSION['update_crediti_time']){
            if(isset($_SESSION['username'])){
                if(!isset($conn)){
                    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
                }
                $user_id = $_SESSION['id_utente'];
                $query_crediti = mysqli_query($conn,"SELECT crediti FROM Utente WHERE id='$user_id' LIMIT 1");
                $crediti = mysqli_fetch_assoc($query_crediti)['crediti'];
                $_SESSION['crediti'] = $crediti;

                $_SESSION['update_crediti_time'] = $ora + $update_crediti_freq;//resettiamo il timer
            }
        }
    }else{
        if(isset($_SESSION['username'])){
            $_SESSION['update_crediti_time'] = $ora + $update_crediti_freq;
        }
    }
?>