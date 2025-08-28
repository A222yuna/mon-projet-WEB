<?php

class EtudiantSujet
{
    private $id;
    private $etudiant_id;
    private $sujet_id;
    private $statut;
    private $note;

    public function __construct($id, $etudiant_id, $sujet_id, $statut, $note)
    {
        $this->id = $id;
        $this->etudiant_id = $etudiant_id;
        $this->sujet_id = $sujet_id;
        $this->statut = $statut;
        $this->note = $note;
    }

    public function getId() { return $this->id; }
    public function getEtudiantId() { return $this->etudiant_id; }
    public function getSujetId() { return $this->sujet_id; }
    public function getStatut() { return $this->statut; }
    public function getNote() { return $this->note; }

    public function setEtudiantId($etudiant_id) { $this->etudiant_id = $etudiant_id; }
    public function setSujetId($sujet_id) { $this->sujet_id = $sujet_id; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setNote($note) { $this->note = $note; }
}
