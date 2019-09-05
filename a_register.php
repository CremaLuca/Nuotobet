<?php
function checkUsername($conn,$username){
    //ritrona true se disponibile, false se già usato
    if(strlen($username) > 3){
        $query_e_username = mysqli_query($conn,"SELECT username FROM Utente WHERE username='$username'");
        if(mysqli_num_rows($query_e_username) == 0){
            return true;
        }else{
            header('HTTP/1.1 401 Not found');
            echo "Username già in uso";
            return false;
        }
    }else{
        header('HTTP/1.1 404 Not found');
        echo "Username troppo corto";
        return false;
    }
}
function checkEmail($conn,$email){
    $email = strtolower($email);
    $query_email = mysqli_query($conn,"SELECT id FROM Utente WHERE email='$email'");
    if(mysqli_num_rows($query_email) == 0){
        return true;
    }else{
        header('HTTP/1.1 401 Not found');
        echo "Email già in uso";
        return false;
    }
}
    if(isset($_POST['e_username'])){
        $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
        $username = mysqli_real_escape_string($conn,$_POST['username']);
        checkUsername($conn, $username);
    }
    if(isset($_POST['e_email'])){
        $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
        $email = mysqli_real_escape_string($conn,$_POST['email']);
        checkEmail($conn,$email);
    }
    if(isset($_POST['register'])){
        if(isset($_POST['username']) && isset($_POST['password'])){

            $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");

            $username = mysqli_real_escape_string($conn,$_POST['username']);
            $password = mysqli_real_escape_string($conn,$_POST['password']);
            $email = "";

            if(checkUsername($conn,$username)){
                if(isset($_POST['email'])){
                    $email = mysqli_real_escape_string($conn,$_POST['email']);
                    if(!checkEmail($conn,$email)){
                        exit;
                    }
                }
                
                mysqli_query($conn,"INSERT INTO Utente(username,password,email) VALUES ('$username','$password','$email')");
                if(mysqli_error($conn) != null){
                    header('HTTP/1.1 500 Internal Server Error');
                    echo mysqli_error($conn);
                }
                $user_id = mysqli_insert_id($conn);
                $query_new_user = mysqli_query($conn,"SELECT id, username, crediti FROM Utente WHERE id='$user_id'");
                if(mysqli_error($conn) != null){
                    header('HTTP/1.1 500 Internal Server Error');
                    echo mysqli_error($conn);
                }
                $db_user = mysqli_fetch_assoc($query_new_user);
                session_start();
                $_SESSION['id_utente'] = $db_user['id'];
                $_SESSION['username'] = $db_user['username'];
                $_SESSION['is_admin'] = $db_user['id_admin'];
                $_SESSION['crediti'] = $db_user['crediti'];
            }
        }else{
            header('HTTP/1.1 500 Internal Server Error');
            echo "Errore interno: Manca username o password";
        }
    }
?>