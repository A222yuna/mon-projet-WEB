<?php

include_once 'C:/xampp/htdocs/yomen/Model/Document.php';
include_once 'C:/xampp/htdocs/yomen/config.php';

class DocumentC
{
    public function listDocuments()
    {
        $sql = "SELECT * FROM documents";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function showDocument($id)
    {
        $sql = "SELECT * FROM documents WHERE id = :id";
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

    public function addDocument($document)
    {
        $sql = "INSERT INTO documents (etudiant_sujet_id, titre, type_fichier, chemin_fichier, statut) VALUES (:etudiant_sujet_id, :titre, :type_fichier, :chemin_fichier, :statut)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':etudiant_sujet_id' => $document->getEtudiantSujetId(),
                ':titre' => $document->getTitre(),
                ':type_fichier' => $document->getTypeFichier(),
                ':chemin_fichier' => $document->getCheminFichier(),
                ':statut' => $document->getStatut()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            error_log('DocumentC::addDocument Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateDocument($document, $id)
    {
        $sql = "UPDATE documents SET etudiant_sujet_id = :etudiant_sujet_id, titre = :titre, type_fichier = :type_fichier, chemin_fichier = :chemin_fichier, statut = :statut WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':id' => $id,
                ':etudiant_sujet_id' => $document->getEtudiantSujetId(),
                ':titre' => $document->getTitre(),
                ':type_fichier' => $document->getTypeFichier(),
                ':chemin_fichier' => $document->getCheminFichier(),
                ':statut' => $document->getStatut()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function deleteDocument($id)
    {
        $sql = "DELETE FROM documents WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getDocumentsByEtudiantSujetId($etudiant_sujet_id)
    {
        $sql = "SELECT * FROM documents WHERE etudiant_sujet_id = :etudiant_sujet_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':etudiant_sujet_id', $etudiant_sujet_id);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getDocumentsBySubmissionId($submission_id)
    {
        $sql = "SELECT * FROM documents WHERE etudiant_sujet_id = :submission_id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':submission_id', $submission_id);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}
