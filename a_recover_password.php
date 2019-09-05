<?
    if(isset($_POST['recover'])){
        if(isset($_POST['usermail'])){
            $usermail = $_POST['usermail'];
            $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
            $query_user = mysqli_query($conn,"SELECT id, email, username FROM Utente WHERE username='$usermail' OR email='$usermail' LIMIT 1");
            echo mysqli_error($conn);

            if(mysqli_num_rows($query_user) > 0){
                $db_user = mysqli_fetch_assoc($query_user);
                if($db_user['email'] != ""){
                    $mail = $db_user['email'];
                    $user_id = $db_user['id'];
                    $username = $db_user['username'];

                    //Generazione password
                    $random_md5 = md5(rand());
                    $new_pass = substr($random_md5, 0, 10);
                    $md5_new_pass = md5($new_pass);

                    //Settiamo la password nel database
                    mysqli_query($conn,"UPDATE Utente SET password='$md5_new_pass' WHERE id='$user_id' ");
                    echo mysqli_error($conn);

                    //Mandiamo la mail
                    $webmaster = "nuotobet@altervista.org"; //TODO: cambiare mail
                    $headers  = "From: NuotoBet < nuotobet@altervista.org >\n";
                    $headers .= "X-Sender: NuotoBet < nuotobet@altervista.org >\n";
                    $headers .= 'X-Mailer: PHP/' . phpversion();
                    $headers .= "X-Priority: 1\n"; // Urgent message!
                    $headers .= "Return-Path: nuotobet@altervista.org\n"; // Return path for errors
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\n";
                    $subject = "Reset password Nuotobet";
                    include_once("mail_recover_password.php");
                    $message = get_email_template($username,$new_pass);
                    if( mail($mail, $subject, $message, $headers) ){
                        echo "Ti è stata inviata una mail a $mail, controlla anche la casella SPAM";
                    }else{
                        header('HTTP/1.1 500 Server error');
                        echo "Non sono riuscito a mandare la mail";
                    }
                }else{
                    header('HTTP/1.1 401 Not found');
                    echo "Mi spiace, non hai inserito la mail, non è possibile recuperare questo account";
                }
            }else{
                header('HTTP/1.1 404 Not found');
                echo "Utente non presente nel database";
            }
        }else{
            header('HTTP/1.1 500 Server error');
            echo "Errore lato server: Mancano dei dati";
        }
    }
    if(isset($_POST['change'])){
        if(isset($_POST['new_password']) && isset($_POST['old_password'])){
            $new_password = $_POST['new_password'];
            $old_password = $_POST['old_password'];
            session_start();
            if(isset($_SESSION['id_utente'])){
                $id_utente = $_SESSION['id_utente'];
                $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");

                $query_old_pass = mysqli_query($conn,"SELECT password FROM Utente WHERE id = '$id_utente'");
                $db_pass = mysqli_fetch_assoc($query_old_pass)['password'];
                if($old_password == $db_pass){
                    mysqli_query($conn,"UPDATE Utente SET password='$new_password' WHERE id = '$id_utente' ");
                    if(!mysqli_error($conn)){
                        echo "Password cambiata con successo";
                    }else{
                        header('HTTP/1.1 500 Server error');
                        echo "Impossibile cambiare la password: ".mysqli_error($conn);
                    }
                }else{
                    header('HTTP/1.1 404 Not found');
                    echo "La vecchia password è errata";
                }
            }else{
                header('HTTP/1.1 404 Not found');
                echo "Non è stato effettuato il login??";
            }
        }else{
            header('HTTP/1.1 500 Server error');
            echo "Errore lato server: Mancano dei dati";
        }
    }
?>