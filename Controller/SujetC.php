<?php

include_once 'C:/xampp/htdocs/yomen/Model/Sujet.php';
include_once 'C:/xampp/htdocs/yomen/config.php';

class SujetC
{
    public function listSujets()
    {
        $sql = "SELECT * FROM sujets";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function showSujet($id)
    {
        $sql = "SELECT * FROM sujets WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            return $query->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function addSujet($sujet)
    {
        $sql = "INSERT INTO sujets (titre, description, propose_par, statut) VALUES (:titre, :description, :propose_par, :statut)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':titre' => $sujet->getTitre(),
                ':description' => $sujet->getDescription(),
                ':propose_par' => $sujet->getProposePar(),
                ':statut' => $sujet->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateSujet($sujet, $id)
    {
        $sql = "UPDATE sujets SET titre = :titre, description = :description, propose_par = :propose_par, statut = :statut WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':titre' => $sujet->getTitre(),
                ':description' => $sujet->getDescription(),
                ':propose_par' => $sujet->getProposePar(),
                ':statut' => $sujet->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function deleteSujet($id)
    {
        $sql = "DELETE FROM sujets WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getSujetsByTeacher($enseignant_id)
    {
        $sql = "SELECT * FROM sujets WHERE propose_par = :enseignant_id ORDER BY id DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':enseignant_id', $enseignant_id);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}
