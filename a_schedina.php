<?php
    //Pagina ajax per le modifiche a schedina
    if(isset($_POST['edit_crediti'])){
        if(isset($_POST['id_schedina']) && isset($_POST['crediti'])){
            $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
            //Non sicurissimo, dovrei controllare che l'id della schedina sia il suo, oppure direttamente prendermelo dalla session
            mysqli_query($conn,"UPDATE Schedina SET crediti='".$_POST['crediti']."' WHERE id='".$_POST['id_schedina']."'");
            echo mysqli_error($conn);
        }else{
            header('HTTP/1.1 404 Not found');
            echo "Manca roba per modificare i crediti";
        }
    }
    if(isset($_POST['toggle_schedina'])){
        session_start();
        $_SESSION['toggle_schedina'] = $_POST['toggle_schedina'];
        echo "Settato schedina ".$_POST['toggle_schedina'];
    }
?>