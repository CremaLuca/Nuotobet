<?php
require_once(dirname(dirname(__FILE__)). "/includes/db.inc.php");
require_once(dirname(dirname(__FILE__))."/includes/time.inc.php");
require_once(dirname(dirname(__FILE__))."/rules.php");
include_once("gara.class.php");
include_once("atleta.class.php");

class Partecipazione
{
    public $id;
    public $atleta;
    public $gara;
    public $tempo_iscrizione;
    public $risultato;
    public $partecipazioni_precedenti;

    function __construct($id, $gara = null, $atleta = null, $tempo_iscrizione = null, $risultato = null, $partecipazioni_precedenti = null)
    {
        $this->id = $id;
        $this->atleta = $atleta;
        $this->gara = $gara;
        $this->tempo_iscrizione = $tempo_iscrizione;
        $this->risultato = $risultato;
        $this->partecipazioni_precedenti = $partecipazioni_precedenti;
    }

    //DATABASE

    function getData()
    {
        $database = Database::getInstance();

        $query = "SELECT id_atleta,id_gara,tempo_iscrizione FROM partecipazione WHERE id='" . $this->id . "' LIMIT 1";
        $res = $database->query($query);
        $row = $res->fetch_assoc();
        $this->tempo_iscrizione = $row['tempo_iscrizione'];
        if ($this->gara == null) {
            $this->getGara($row['id_gara']);
        }
        if ($this->atleta == null) { 
            $this->getAtleta($row['id_atleta']);
        }

        return $this;
    }

    function getGara($id_gara = null)
    {
        if ($id_gara == null) {
            $database = Database::getInstance();
            $res = $database->query("SELECT id_gara FROM partecipazione WHERE id='" . $this->id . "' LIMIT 1");
            $row = $res->fetch_assoc();
            $id_gara = $row['id_gara'];
        }
        $this->gara = new Gara($id_gara);
        $this->gara->getData();
    }

    function getAtleta($id_atleta = null)
    {
        if ($id_atleta == null) {
            $database = Database::getInstance();
            $res = $database->query("SELECT id_atleta FROM partecipazione WHERE id='" . $this->id . "' LIMIT 1");
            $row = $res->fetch_assoc();
            $id_atleta = $row['id_atleta'];
        }
        $this->atleta = new Atleta($id_atleta);
        $this->atleta->getData();
    }

    function getRisultato()
    {
        $database = Database::getInstance();
        $res = $database->query("SELECT tempo FROM risultato WHERE id_partecipazione = '$this->id'");
        $row = $res->fetch_assoc();
        $this->risultato = $row['tempo'];
        return $this;
    }

    function getPartecipazioniPrecedenti()
    {
        $database = Database::getInstance();
        $res = $database->query("SELECT p.id,m.data,p.id_gara,r.tempo as risultato,p.tempo_iscrizione FROM partecipazione p INNER JOIN gara g ON g.id = p.id_gara INNER JOIN manifestazione m ON m.id = g.id_manifestazione LEFT JOIN risultato r ON p.id = r.id_partecipazione WHERE g.stile = '" . $this->gara->stile . "' AND g.metri = '" . $this->gara->metri . "' AND p.id_atleta = '" . $this->atleta->id . "' AND m.data < '" . $this->gara->manifestazione->data . "'");
        while ($row = $res->fetch_assoc()) {
            $part = new Partecipazione($row['id'], null, $this->atleta,$row['tempo_iscrizione'],$row['risultato']);
            $part->getGara($row['id_gara']);
            $this->partecipazioni_precedenti[] = $part;
        }
        return $this;
    }

    //OUTPUT

    function outputTempoIscrizione()
    {
        return sede_sege($this->tempo_iscrizione);
    }

    function outputRisultato()
    {
        global $codici_risultati;
        if ($this->risultato == null)
            return null;
        if ($this->risultato == $codici_risultati['ASS']) {
            return "ASSENTE";
        } else if ($this->risultato == $codici_risultati['SQU']) {
            return "SQUALIFICATO";
        } else if ($this->risultato == $codici_risultati['RIT']) {
            return "RITIRATO";
        }
        //Se per caso non dovesse essere un risultato codificato per qualche motivo
        if ($this->risultato < 0)
            return "ERRORE";
        //Ci andrebbe un else ma per il compilatore evito così la funzione ritorna sempre qualcosa
        return sede_sege($this->risultato);
    }

    function outputRisultatoColor()
    {
        global $codici_risultati;
        if ($this->risultato == $codici_risultati['ASS']) {
            return "warning";
        } else if ($this->risultato == $codici_risultati['SQU']) {
            return "danger";
        } else if ($this->risultato == $codici_risultati['RIT']) {
            return "warning";
        }
        //Se non abbiamo un valore valido anche dopo aver fatto i controlli diamo errore
        if ($this->risultato < 0)
            return "info";

        //Arrivati qui siamo sicuri che sia un numero valido
        if ($this->tempo_iscrizione > $this->risultato) {
            return "success";
        } else if ($this->tempo_iscrizione == $this->risultato) {
            return "warning";
        }
        return "danger";
    }

    function outputDifferenzaRisultato()
    {
        if ($this->risultato == null)
            return null;
        if ($this->risultato < 0)
            return null;
        return sede_sege($this->risultato - $this->tempo_iscrizione);
    }

    function outputCompletoRisultato()
    {
        if ($this->risultato == null)
            return null;
        if ($this->risultato < 0)
            return $this->outputRisultato();

        $ret = sede_sege($this->risultato);
        $diff = $this->risultato - $this->tempo_iscrizione;
        if ($diff != 0) {
            $ret .= " <span class='font-weight-bold'>(";
            if ($diff > 0) {
                $ret .= "+";
            }
            $ret .= number_format($diff / 100, 2, '.', ':') . ")</span>"; //Un casino solo per stampare il + prima dei positivi
        }
        return $ret;
    }
}
