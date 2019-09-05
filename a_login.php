<?php
    if(isset($_POST['login'])){
        if(isset($_POST['password']) && isset($_POST['username'])){
            $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");

            $md5psw = mysqli_real_escape_string($conn,$_POST['password']);
            $username = mysqli_real_escape_string($conn,$_POST['username']);
            
            $query_user = mysqli_query($conn,"SELECT id,password,username,is_admin,crediti FROM Utente WHERE username = '$username'");
            if(mysqli_num_rows($query_user) > 0){
                $db_user = mysqli_fetch_assoc($query_user);
                $dbPsw = $db_user['password'];
                if($md5psw == $dbPsw){
                    session_start();
                    //Ok, abbiamo effettuato l'accesso
                    $_SESSION['id_utente'] = $db_user['id'];
                    $_SESSION['username'] = $db_user['username'];
                    $_SESSION['is_admin'] = $db_user['is_admin'];
                    $_SESSION['crediti'] = $db_user['crediti'];
                    $dataora = date("Y-m-d H:i:s");

                    mysqli_query($conn, "UPDATE Utente SET ultimo_accesso='$dataora' WHERE id='".$db_user['id']."' "); //Aggiorniamo l'ultimo accesso

                    //Controlliamo se ha schedine aperte
                    $query_schedina_aperta = mysqli_query($conn, "SELECT id FROM schedina WHERE id_utente = '".$db_user['id']."' AND aperta = '1' ");
                    if(mysqli_num_rows($query_schedina_aperta) > 0){
                        $db_schedina_aperta = mysqli_fetch_assoc($query_schedina_aperta);
                        $_SESSION['id_schedina'] = $db_schedina_aperta['id'];
                    }

                    //TODO: Controllare che le scomesse della schedina aperta siano ancora tutte valide, altrimenti eliminarle

                    echo "Accesso effettuato con successo";
                }else{
                    header('HTTP/1.1 401 Unauthorized');
                    echo "Password errata";
                }
            }else{
                header('HTTP/1.1 404 Not found');
                echo "Nome utente non esistente";
            }
        }else{
            header('HTTP/1.1 500 Internal Server Error');
            echo "Errore interno: Manca username o password";
        }
    }
?>