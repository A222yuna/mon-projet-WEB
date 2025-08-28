<?php

class Sujet
{
    private $id;
    private $titre;
    private $description;
    private $propose_par;
    private $statut;

    public function __construct($id, $titre, $description, $propose_par, $statut = 'actif')
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->propose_par = $propose_par;
        $this->statut = $statut;
    }

    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getProposePar() { return $this->propose_par; }
    public function getStatut() { return $this->statut; }

    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setProposePar($propose_par) { $this->propose_par = $propose_par; }
    public function setStatut($statut) { $this->statut = $statut; }
}
