<?php
require_once(dirname(dirname(__FILE__))."/includes/db.inc.php");
include_once("gara.class.php");

class Atleta
{
    public $id;
    public $nome;
    public $anno;
    public $squadra;
    public $id_squadra;

    function __construct($id, $nome = null, $anno = null,$squadra = null,$id_squadra = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->anno = $anno;
        $this->squadra = $squadra;
        $this->id_squadra = $id_squadra;
    }

    function getData()
    {
        $database = Database::getInstance();

        $query = "SELECT a.nome,a.anno,s.nome as squadra,a.id_squadra FROM atleta a INNER JOIN squadra s ON s.id = a.id_squadra WHERE a.id='" . $this->id . "' LIMIT 1";
        $res = $database->query($query);
        $row = $res->fetch_assoc();
        $this->nome = $row['nome'];
        $this->anno = $row['anno'];
        $this->squadra = $row['squadra'];
        $this->id_squadra = $row['id_squadra'];

        return $this;
    }

    function getPartecipazioni(){
        $database = Database::getInstance();
        $partecipazioni = array();
        $res = $database->query("SELECT p.id FROM partecipazione p INNER JOIN gara g ON p.id_gara = g.id INNER JOIN manifestazione m ON m.id = g.id_manifestazione LEFT JOIN Risultato r ON r.id_partecipazione = p.id WHERE p.id_atleta = '$this->id' ORDER BY m.data DESC");
        while($row = $res->fetch_assoc()){
            $part = new Partecipazione($row['id'],null,$this);
            $part->getData();
            $part->getRisultato();
            $partecipazioni[] = $part;
        }
        return $partecipazioni;
    }

    function outputNome()
    {
        $weird_name = $this->nome;
        $vowels = "AEIOU";
        for ($i = 0; $i < strlen($weird_name); $i++) {
            if ($weird_name[$i] == "*") {
                $weird_name[$i] = $vowels[mt_rand(0, 4)];
            }
        }
        return $weird_name;
    }
}
