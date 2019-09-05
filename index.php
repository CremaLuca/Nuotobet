<?
error_reporting(E_ALL);
session_start();
include_once("funzioni.php");
include_once("timer_functions.php");

if(isset($_GET['p'])){
    $pagina = $_GET['p'];
}

if(isset($_SESSION['is_admin'])){
    $user_is_admin = $_SESSION['is_admin'];
}else{
    $user_is_admin = false;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="description" content="Nuotobet, il primo sito di scommesse sulle gare di nuoto regionali del Veneto" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
        integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(function () {
            $('tr[data-href]').on("click", function () {
                document.location = $(this).data('href');
            });
            $('[data-target]').on("click", function () {
                console.log('ei');
                $($(this).data('target')).toggle(200);
            });
        });
    </script>
    <style>
        body {
            font-size: 0.90rem;
        }

        main {
            font-size: 0.8rem;
        }
        .table td, .table tr{
            padding: .65rem;
        }
        .thead-dark tr td, .thead-dark tr th{
            border-bottom:0;
            border-top:0;
        }
        .feather {
            width: 16px;
            height: 16px;
            vertical-align: text-bottom;
        }

        .sidebar {
            top: 64px;
            bottom: 0;
            left: 0;
            z-index: 100;
            /* Behind the navbar */
            padding: 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            
        }

        .sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 48px;
            /* Height of navbar */
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
            /* Scrollable contents if viewport is shorter than content. */
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: #333;
        }

        .sidebar .nav-link .feather {
            margin-right: 4px;
            color: #999;
        }

        .sidebar .nav-link.active {
            color: #007bff;
        }

        .sidebar .nav-link:hover .feather,
        .sidebar .nav-link.active .feather {
            color: inherit;
        }

        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
        }

        /*
        * Navbar
        */

        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }

        .navbar .form-control {
            padding: .75rem 1rem;
            border-width: 0;
            border-radius: 0;
        }

        .form-control-dark {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .1);
        }

        .form-control-dark:focus {
            border-color: transparent;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
        }

        i {
            margin-left: 5px;
            margin-right: 5px;
            width:15px;
        }
        tr[data-href]{
            cursor:pointer;
        }
        a[onclick]{
            cursor:pointer;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark flex-nowrap p-0 col-sm-12 border-bottom border-primary sticky-top">
        <a class="font-weight-bold text-primary py-3 col-sm-2 col-md-2 mr-0" style='box-shadow:none;font-size:1.3rem;text-decoration:none;' href="/?">Nuotobet</a>
        <div class="top-bar d-flex col-sm-10 col-md-10">
            <input class="form-control form-control-dark w-100 col-md-4 rounded mx-2 d-none d-md-inline-block" type="text"
                placeholder="Nome atleta o Squadra" aria-label="Cerca">
            <button class="btn btn-info mx-2 d-none d-md-inline-block"><i class="fa fa-search" title="Cerca"></i></button>
            <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars" style="font-size: 1.5rem;" title="Menu"></i>
            </button>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-inline-block bg-light sidebar navbar-expand-md border-bottom border-primary position-sticky">
                <div class="sidebar-sticky navbar-collapse collapse align-items-start flex-column position-sm-absolute"
                    id="navbarSupportedContent">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/?">
                                <i class="fa fa-home" title="Home"></i>
                                Home <!-- <span class="sr-only">(current)</span> -->
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/?p=visual_manif">
                                <i class="fa fa-align-justify" title="Manifestazioni"></i>
                                Manifestazioni
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/?p=classifica">
                                <i class="fa fa-users" title="Classifica"></i>
                                Classifica
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/?p=faq">
                                <i class="fa fa-question" title="FAQ"></i>
                                FAQ
                            </a>
                        </li>
                    </ul>
                    <?php
                        if(!isset($_SESSION['username'])){
                    ?>
                    <h6
                        class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Entra in Nuotobet</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/landing.php">
                                <i class="fa fa-sign-in-alt" title="Accedi"></i>
                                Accedi o registrati
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/?p=registrazione">
                                <i class="fa fa-pencil-alt" title="Registrati"></i>
                                Registrati
                            </a>
                        </li> -->
                    </ul>
                    <?php
                        }else{
                    ?>
                    <h6
                        class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>ACCOUNT</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/?p=user">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fa fa-user" title="Utente"></i>
                                    <? echo $_SESSION['username']; ?>
                                </div>
                                <div>
                                    <i class="fa fa-coins" title="crediti"></i>
                                    <? echo $_SESSION['crediti']; ?>
                                </div>
                            </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/?p=user_settings">
                                <i class="fa fa-user-cog" title="Impostazioni"></i>
                                Impostazioni
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout.php">
                                <i class="fa fa-sign-out-alt" title="Logout"></i>
                                Logout
                            </a>
                        </li>
                        <?php
                            if($user_is_admin){
                        ?>
                        <h6
                            class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>STRUMENTI</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link" href="/?p=admin">
                                <i class="fa fa-tools" title="Admin"></i>
                                Admin
                            </a>
                        </li>
                        <?php
                            }
                            ?>
                    </ul>
                    <?php

                        }//Ende else !isset(session username)
                        ?>
            <div class='text-center riferimento-casuale p-4'>Sito web in via di sviluppo, i dati sono forniti da <a href='http://finveneto.org'>finveneto.org</a>
                </div>
            </nav>
            <div class="col-md-10 p-0">
                <?php
                    if(isset($_SESSION['username']) && isset($_SESSION['id_schedina'])){
                ?>
                <div class="row m-0">
                    <? include_once("schedina_index.php"); ?>
                </div>
                <?php
                    }
                ?>
                <div class="row m-0">
                    <main role="main" class="w-100 ">
                        <?php
                    if(isset($pagina)){
                        switch ($pagina) {
                            case "listagare":
                                include("listagare.php");
                                break;
                            case "admin":
                                include("admin.php");
                                break;
                            case "user":
                                include("user.php");
                                break;
                            case "visual_manif":
                                include("visual_manifestazione.php");
                                break;
                            case "visual_gara":
                                include("visual_gara.php");
                                break;
                            case "visual_startlist":
                                include("visual_startlist.php");
                                break;
                            case "visual_part":
                                include("visual_partecipazione.php");
                                break;
                            case "visual_scom":
                                include("visual_scommessa.php");
                                break;
                            case "visual_atleta":
                                include("visual_atleta.php");
                                break;
                            case "visual_squadra":
                                include("visual_squadra.php");
                                break;
                            case "agg_scom":
                                include("agg_scommessa.php");
                                break;
                            case "rem_scom":
                                include("rem_scommessa.php");
                                break;
                            case "agg_sche":
                                include("agg_schedina.php");
                                break;
                            case "faq":
                                include("faq.php");
                                break;
                            case "classifica":
                                include("classifica.php");
                                break;
                            case "user_settings":
                                include("user_settings.php");
                                break;
                            case "home":
                            default:
                                include("main.php");
                                break;
                        }
                    }else{
                        include_once("main.php");
                    }
                ?>
                    </main>
                </div>
            </div>
        </div>
    </div>
</body>

</html>