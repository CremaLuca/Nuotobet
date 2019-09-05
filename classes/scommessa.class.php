<?php
require_once(dirname(dirname(__FILE__))."/includes/db.inc.php");
require_once(dirname(dirname(__FILE__))."/includes/time.inc.php");
include_once("schedina.class.php");
include_once("partecipazione.class.php");

class Scommessa{
    public $id;
    public $schedina;
    public $partecipazione;
    public $tempo_min;
    public $tempo_max;
    public $quota;
    public $scadenza;
    public $stato;

    function __construct($id,$schedina,$partecipazione = null,$tempo_min = null,$tempo_max = null,$quota = null,$scadenza = null,$stato = null)
    {
        $this->id = $id;
        $this->schedina = $schedina;
        $this->partecipazione = $partecipazione;
        $this->tempo_min = $tempo_min;
        $this->tempo_max = $tempo_max;
        $this->quota = $quota;
        $this->scadenza = $scadenza;
        $this->stato = $stato;
    }

    function getData(){
        $database = Database::getInstance();
        $res = $database->query("SELECT tempo_min,tempo_max,quota,scadenza,stato,id_partecipazione FROM scommessa WHERE id='$this->id' LIMIT 1");
        $row = $res->fetch_assoc();
        $this->tempo_min = $row['tempo_min'];
        $this->tempo_max = $row['tempo_max'];
        $this->quota = $row['quota'];
        $this->scadenza = $row['scadenza'];
        $this->stato = $row['stato'];
        if($this->partecipazione == null){
            $partecipazione = new Partecipazione($row['id_partecipazione']);
            $this->partecipazione = $partecipazione.getData();
        }
        return $this;
    }

    function updateData(){
        $database = Database::getInstance();
        $database->query("INSERT INTO scommessa(tempo_min,tempo_max,quota,scadenza,stato) VALUES ('$this->tempo_min','$this->tempo_max','$this->quota','$this->scadenza','$this->stato') ON DUPLICATE KEY UPDATE SET tempo_min = '$this->tempo_min', tempo_max = '$this->tempo_max', quota = '$this->quota', scadenza = '$this->scadenza', stato = '$this->stato'");
    }

    function outputTempoMax(){
        return sede_sege($this->tempo_max);
    }

    function outputTempoMin(){
        return sede_sege($this->tempo_min);
    }
}
