<?php

class Articolo
{
    public $id;
    public $tiolo;
    public $testo;
    public $autore;

    static function getArticoli($limit = 5)
    {
        $articoli = array();
        $database = Database::getInstance();

        $res = $database->query("SELECT a.id,titolo,testo,username as nome_autore FROM articolo a INNER JOIN utente u ON a.id_utente = u.id ORDER BY a.id DESC LIMIT $limit");
        while ($row = $res->fetch_assoc()) {
            $articoli[] = new Articolo($row['id'], $row['titolo'], $row['testo'], $row['nome_autore']);
        }
        return $articoli;
    }

    function __construct($id, $titolo = null, $testo = null, $autore = null)
    {
        $this->id = $id;
        $this->titolo = $titolo;
        $this->testo = $testo;
        $this->autore = $autore;
    }

    function getData()
    {
        $database = Database::getInstance();

        $res = $database->query("SELECT titolo,testo,username as nome_autore FROM articolo a INNER JOIN utente u ON a.id_utente = u.id WHERE a.id='" . $this->id . "' LIMIT 1");
        $row = $res->fetch_assoc();
        $this->tiolo = $row['tiolo'];
        $this->testo = $row['testo'];
        $this->autore = $row['nome_autore'];

        return $this;
    }

    function getShortTesto($lenght = 250)
    {
        $testo = $this->testo;
        if (strlen($testo) > $lenght) {
            $testo = substr($testo, 0, $lenght);
        }
        return $testo;
    }

    function updateData()
    {
        $database = Database::getInstance();
        $database->query("UPDATE articolo SET titolo = '$this->titolo', testo = '$this->testo' WHERE id='$this->id'");
    }
}
