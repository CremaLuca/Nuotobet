<?php
require_once(dirname(dirname(__FILE__))."/includes/db.inc.php");
include_once("partecipazione.class.php");
include_once("manifestazione.class.php");

class Gara
{
    public $id;
    public $manifestazione;
    public $metri;
    public $stile;
    public $categoria;
    public $partecipazioni;
    public $conclusa;

    function __construct($id, $manifestazione = null, $metri = null, $stile = null, $categoria = null, $partecipazioni = null, $conclusa = null)
    {
        $this->id = $id;
        $this->manifestazione = $manifestazione;
        $this->metri = $metri;
        $this->stile = $stile;
        $this->categoria = $categoria;
        $this->partecipazioni = $partecipazioni;
        $this->conclusa = $conclusa;
    }

    //DATABASE

    function getData()
    {
        $database = Database::getInstance();

        $res = $database->query("SELECT g.id_manifestazione,g.metri,g.stile,g.categoria,g.conclusa FROM gara g WHERE id='" . $this->id . "' LIMIT 1");
        $row = $res->fetch_assoc();
        $this->metri = $row['metri'];
        $this->stile = $row['stile'];
        $this->categoria = $row['categoria'];
        $this->conclusa = $row['conclusa'];
        
        if ($this->manifestazione == null) {
            $this->getManifestazione($row['id_manifestazione']);
        }

        return $this;
    }

    function getManifestazione($id_manifestazione){
        if($id_manifestazione == null){
            $database = Database::getInstance();
            $res = $database->query("SELECT g.id_manifestazione FROM gara g WHERE id='" . $this->id . "' LIMIT 1");
            $row = $res->fetch_assoc();
            $id_manifestazione = $row['id_manifestazione'];
        }
        $this->manifestazione = new Manifestazione($id_manifestazione);
    }

    function getPartecipazioni()
    {
        $database = Database::getInstance();

        $res =  $database->query("SELECT p.id,p.tempo_iscrizione,r.tempo as risultato,p.id_atleta  FROM partecipazione p LEFT JOIN risultato r ON p.id = r.id_partecipazione WHERE p.id_gara = '$this->id'");
        while ($row = $res->fetch_assoc()) {
            $part = new Partecipazione($row['id'], $this, null, $row['tempo_iscrizione']);
            $part->getAtleta($row['id_atleta']);
            if ($this->conclusa)
                $part->risultato = $row['risultato'];
            $this->partecipazioni[] = $part;
        }
        return $this;
    }

    function updateData()
    {
        $database = Database::getInstance();
        $database->query("INSERT INTO gara(id_manifestazione,metri,stile,categoria,conclusa) VALUES ('$this->id_manifestazione','$this->metri','$this->stile','$this->categoria','$this->conclusa') ON DUPLICATE KEY UPDATE gara SET id_manifestazione = '$this->id_manifestazione', metri = '$this->metri', stile='$this->stile', categoria = '$this->categoria', conclusa = '$this->conclusa'");
    }

    //OUTPUT

    function outputNome()
    {
        return $this->metri . " " . $this->stile;
    }

    //ALTRO

    function calcMigliorati()
    {
        if (!$this->conclusa)
            return null;
        if ($this->partecipazioni == null)
            $this->getPartecipazioni();
        $n_migliorati = 0;
        foreach ($this->partecipazioni as $partecipazione) {
            if ($partecipazione->tempo_iscrizione > $partecipazione->risultato)
                $n_migliorati++;
        }
        return $n_migliorati;
    }

    function calcMediana()
    {
        if ($this->partecipazioni == null) {
            //Sarebbe da prendere tutti i risultati solo che ci vuole una settimana
            $database = Database::getInstance();
            $res = $database->query("SELECT tempo_iscrizione FROM partecipazione WHERE id_gara = '$this->id' LIMIT 15");
            $num_rows = mysqli_num_rows($res);
            $tempo = 1;
            for ($i = 0; $i < $num_rows / 2; $i++)
                $tempo = $res->fetch_assoc()['tempo_iscrizione'];
            return $tempo;
        } else {
            return $this->partecipazioni[count($this->partecipazioni) / 2]->tempo_iscrizione;
        }
    }
}
