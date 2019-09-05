<?php
error_reporting(E_ALL);
session_start();


if(isset($_SESSION['id_utente'])){ //Se abbiamo già fatto l'accesso accediamo alla home
    refresh(0,'/');
    exit;
}else{
    include_once("funzioni.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Nuotobet - Benvenuto</title>
    <meta name="description" content="Nuotobet, il primo sito di scommesse sulle gare di nuoto regionali del Veneto" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="stile.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style>
    html,body{
        height: 100%;
    }
    body{
        display: flex;
        -ms-flex-align: center;
        -ms-flex-pack: center;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }
    p.extra-buttons{
        display:flex;
        justify-content:space-between;
    }
    video.video-bg{
        position: fixed;
        z-index:-1;
        min-width: 100%; 
        min-height: 100%;
    }
    </style>
    <script>
    var current_page = "";

    function changePage(page){
        if(page != current_page){
            current_page = page;
            $.ajax({
                url: page+".php",
                success: function(result){
                    $("body").fadeOut(500,function(){
                        $("body").html(result,function(){
                        });
                        $("body").fadeIn(500);
                    })
            }});
        }
    }  
    function formAlert(text){
        formAlert(text,2000);
    }
    function formAlert(text,time){
        $("#alert-box").fadeIn(400).html("<div class='alert alert-danger' role='alert'><i class='fa fa-exclamation-circle'></i>"+text+"</div>").delay(time).fadeOut(500);
    }
    function formSuccess(text,time){
        $("#alert-box").fadeIn(400).html("<div class='alert alert-success' role='alert'><i class='fa fa-check-circle'></i>"+text+"</div>").delay(time).fadeOut(500);
    }
    </script>
</head>
<body onload="changePage('landing_login');">

</body>
<?php
}
?>