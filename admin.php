<?php
  $useradmin = $_SESSION['is_admin'];

if(isset($_GET['adminPage']))
    $adminPage = $_GET['adminPage'];

if(!isset($useradmin) || $useradmin == false){
    refresh(0,'/');
    exit;
  }
if(!isset($adminPage)){
    include_once("funzioni.php");

    $conn = mysqli_connect("localhost","nuotobet","password","my_nuotobet");
    $query_scomm_controllare = mysqli_query($conn,"SELECT sc.id FROM scommessa sc INNER JOIN schedina s ON s.id = sc.id_schedina WHERE sc.stato = '0' AND s.aperta='0'");
    $n_scomm_controllare = mysqli_num_rows($query_scomm_controllare);
    $query_risultati = mysqli_query($conn,"SELECT m.id FROM manifestazione m INNER JOIN gara g ON g.id_manifestazione = m.id INNER JOIN partecipazione p ON p.id_gara = g.id LEFT JOIN Risultato r ON p.id = r.id_partecipazione WHERE (data <= NOW() AND (r.tempo = '0' OR r.tempo IS NULL)) GROUP BY m.id");
    $n_risultati_registrare = mysqli_num_rows($query_risultati);
?>
<style>
.card{
  min-width:10rem;
  max-width:25rem;
}
</style>
<title>Nuotobet - Amministrazione</title>
<meta name="description" content="Amministra le cose di Nuotobet" />
<div class="d-flex flex-row flex-wrap m-0">
    <div class="card shadow m-3">
      <div class="card-header">
        <h4>Manifestazioni</h4>
      </div>
      <div class="card-body">
        <p class="card-text">Importa nuove manifestazioni dal sito di finveneto.org</p>
        <a class="btn btn-primary" href="index.php?p=admin&adminPage=lista">Importa</a>
      </div>
    </div>
    <div class="card shadow m-3">
      <div class="card-header">
        <h4>Registrazione risultati</h4>
      </div>
      <div class="card-body">
        <h5 class="card-title"><?= $n_risultati_registrare ?> manifestazioni aperte</h5>
        <p class="card-text">Importa i risultati di manifestazioni concluse per poi poter controllare le schedine e avere le statistiche della gara</p>
        <a class="btn btn-primary" href="index.php?p=admin&adminPage=iscrizione_risultati">Registra</a>
      </div>
    </div>
    <div class="card shadow m-3">
      <div class="card-header">
        <h4>Scommesse da controllare</h4>
      </div>
      <div class="card-body">
        <h5 class="card-title"><?= $n_scomm_controllare ?> scommesse da controllare</h5>
        <p class="card-text">Controlla le scommesse per chiudere schedine e ricompensare i vincitori</p>
        <a class="btn btn-primary" href="index.php?p=admin&adminPage=controllo">Controlla</a>
      </div>
    </div>
</div>
<div class="text-center">
  <a class='btn btn-primary my-2' href="index.php?p=admin&adminPage=debug">Debug</a><br />
  <a class='btn btn-primary my-2' href="index.php?p=admin&adminPage=controllo_manif">Controllo manif</a><br />
</div>
<?php
  }else{
    switch($adminPage){
      case 'controllo':
        include('controllo_scommesse.php');
      break;
      case 'lista':
        include('iscrizione_manifestazioni.php');
      break;
      case 'iscrizione_gare':
      include('iscrizione_gare.php');
      break;
      case 'iscrizioni':
      include('iscrizione_atleti.php');
      break;
      case 'iscrizione_risultati':
      include('iscrizione_risultati.php');
      break;
      case 'debug':
      include('debug.php');
      break;
      case 'controllo_manif':
      include('admin_controllo_manif.php');
      break;
      default:
      echo "Non hai selezionato niente ".$adminPage;
      break;
    }
}
?>