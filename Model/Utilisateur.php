<?php

class Utilisateur
{
    private $id;
    private $email;
    private $mot_de_passe;
    private $prenom;
    private $nom;
    private $role;

    public function __construct($id, $email, $mot_de_passe, $prenom, $nom, $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->role = $role;
    }

    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getMotDePasse() { return $this->mot_de_passe; }
    public function getPrenom() { return $this->prenom; }
    public function getNom() { return $this->nom; }
    public function getRole() { return $this->role; }

    public function setEmail($email) { $this->email = $email; }
    public function setMotDePasse($mot_de_passe) { $this->mot_de_passe = $mot_de_passe; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setRole($role) { $this->role = $role; }
}
