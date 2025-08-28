<?php

class Document
{
    private $id;
    private $etudiant_sujet_id;
    private $titre;
    private $type_fichier;
    private $chemin_fichier;
    private $statut;

    public function __construct($id, $etudiant_sujet_id, $titre, $type_fichier, $chemin_fichier, $statut)
    {
        $this->id = $id;
        $this->etudiant_sujet_id = $etudiant_sujet_id;
        $this->titre = $titre;
        $this->type_fichier = $type_fichier;
        $this->chemin_fichier = $chemin_fichier;
        $this->statut = $statut;
    }

    public function getId() { return $this->id; }
    public function getEtudiantSujetId() { return $this->etudiant_sujet_id; }
    public function getTitre() { return $this->titre; }
    public function getTypeFichier() { return $this->type_fichier; }
    public function getCheminFichier() { return $this->chemin_fichier; }
    public function getStatut() { return $this->statut; }

    public function setEtudiantSujetId($etudiant_sujet_id) { $this->etudiant_sujet_id = $etudiant_sujet_id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setTypeFichier($type_fichier) { $this->type_fichier = $type_fichier; }
    public function setCheminFichier($chemin_fichier) { $this->chemin_fichier = $chemin_fichier; }
    public function setStatut($statut) { $this->statut = $statut; }
}
