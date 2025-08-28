<?php

include_once 'C:/xampp/htdocs/yomen/Model/EtudiantSujet.php';
include_once 'C:/xampp/htdocs/yomen/config.php';

class EtudiantSujetC
{
    public function listEtudiantSujets()
    {
        $sql = "SELECT * FROM etudiant_sujets";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function showEtudiantSujet($id)
    {
        $sql = "SELECT * FROM etudiant_sujets WHERE id = :id";
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

    public function addEtudiantSujet($etudiantSujet)
    {
        $sql = "INSERT INTO etudiant_sujets (etudiant_id, sujet_id, statut, note) VALUES (:etudiant_id, :sujet_id, :statut, :note)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':etudiant_id' => $etudiantSujet->getEtudiantId(),
                ':sujet_id' => $etudiantSujet->getSujetId(),
                ':statut' => $etudiantSujet->getStatut(),
                ':note' => $etudiantSujet->getNote()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateEtudiantSujet($etudiantSujet, $id)
    {
        $sql = "UPDATE etudiant_sujets SET etudiant_id = :etudiant_id, sujet_id = :sujet_id, statut = :statut, note = :note WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':etudiant_id' => $etudiantSujet->getEtudiantId(),
                ':sujet_id' => $etudiantSujet->getSujetId(),
                ':statut' => $etudiantSujet->getStatut(),
                ':note' => $etudiantSujet->getNote()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function deleteEtudiantSujet($id)
    {
        $sql = "DELETE FROM etudiant_sujets WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getEtudiantSujetByStudentId($etudiant_id)
    {
        $sql = "SELECT es.*, s.titre as sujet_titre, s.description as sujet_description 
                FROM etudiant_sujets es 
                JOIN sujets s ON es.sujet_id = s.id 
                WHERE es.etudiant_id = :etudiant_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':etudiant_id', $etudiant_id);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function hasStudentChosenSubject($etudiant_id, $sujet_id)
    {
        $sql = "SELECT COUNT(*) FROM etudiant_sujets WHERE etudiant_id = :etudiant_id AND sujet_id = :sujet_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':etudiant_id', $etudiant_id);
            $query->bindValue(':sujet_id', $sujet_id);
            $query->execute();
            return $query->fetchColumn() > 0;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getLastInsertId()
    {
        $db = config::getConnexion();
        return $db->lastInsertId();
    }

    public function updateEtudiantSujetStatus($etudiant_sujet_id, $statut)
    {
        $sql = "UPDATE etudiant_sujets SET statut = :statut WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':id' => $etudiant_sujet_id,
                ':statut' => $statut
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function getSubmissionsByTeacher($enseignant_id)
    {
        $sql = "SELECT es.*, s.titre as sujet_titre, u.prenom, u.nom, 
                       CONCAT(u.prenom, ' ', u.nom) as student_name
                FROM etudiant_sujets es 
                JOIN sujets s ON es.sujet_id = s.id 
                JOIN utilisateurs u ON es.etudiant_id = u.id
                WHERE s.propose_par = :enseignant_id 
                ORDER BY es.id DESC";
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

    public function updateEtudiantSujetGrade($etudiant_sujet_id, $statut, $note = null)
    {
        $sql = "UPDATE etudiant_sujets SET statut = :statut, note = :note WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':id' => $etudiant_sujet_id,
                ':statut' => $statut,
                ':note' => $note
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function getProjectStudentCount($project_id)
    {
        $sql = "SELECT COUNT(*) FROM etudiant_sujets WHERE sujet_id = :project_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':project_id', $project_id);
            $query->execute();
            return $query->fetchColumn();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getSubmissionDetails($submission_id, $enseignant_id)
    {
        $sql = "SELECT es.*, s.titre as sujet_titre 
                FROM etudiant_sujets es 
                JOIN sujets s ON es.sujet_id = s.id 
                WHERE es.id = :submission_id AND s.propose_par = :enseignant_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':submission_id', $submission_id);
            $query->bindValue(':enseignant_id', $enseignant_id);
            $query->execute();
            return $query->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}
