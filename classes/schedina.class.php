<?php
require_once(dirname(dirname(__FILE__))."/includes/db.inc.php");
include_once("utente.class.php");
include_once("scommessa.class.php");

class Schedina
{
    public $id;
    public $utente;
    public $crediti;
    public $aperta;
    public $conclusa;
    public $scommesse;

    function __construct($id, $utente = null, $crediti = null, $aperta = null, $conclusa = null)
    {
        $this->id = $id;
        $this->utente = $utente;
        $this->crediti = $crediti;
        $this->aperta = $aperta;
        $this->conclusa = $conclusa;
    }

    function getData()
    {
        $database = Database::getInstance();

        $res = $database->query("SELECT crediti,aperta,conclusa,id_utente FROM schedina WHERE id='$this->id' LIMIT 1");
        $row = $res->fetch_assoc();
        $this->crediti = $row['crediti'];
        $this->aperta = $row['aperta'];
        $this->conclusa = $row['conclusa'];
        if ($this->utente == null) {
            $utente = new Utente($row['id_utente']);
            $this->utente = $utente . getData();
        }
        return $this;
    }

    function getScommesse()
    {
        $database = Database::getInstance();

        $res = $database->query("SELECT id FROM scommessa WHERE id_schedina='$this->id' LIMIT 1");
        while ($row = $res->fetch_assoc()) {
            $scommessa = new Scommessa($row['id'], $this->id,);
            $scommessa->getData();
            $this->scommesse[] = $scommessa;
        }
        return $this;
    }

    function updateData()
    {
        $database = Database::getInstance();
        $database->query("INSERT INTO schedina(id_utente,crediti,aperta,conclusa) VALUES ('$this->utente->id','$this->crediti','$this->aperta','$this->conclusa') ON DUPLICATE KEY UPDATE SET crediti = '$this->crediti', aperta = '$this->aperta', conclusa = '$this->conclusa'");
        foreach ($this->scommesse as $scommessa) {
            $scommessa . updateData();
        }
    }
}
