<?php

include_once 'C:/xampp/htdocs/yomen/Model/Utilisateur.php';
include_once 'C:/xampp/htdocs/yomen/config.php';

class UtilisateurC
{
    public function listUtilisateurs()
    {
        $sql = "SELECT * FROM utilisateurs";
        $db = config::getConnexion();
        try {
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function showUtilisateur($id)
    {
        $sql = "SELECT * FROM utilisateurs WHERE id = :id";
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

    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':email', $email);
            $query->execute();
            return $query->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function authenticateUser($email, $password)
    {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }

    public function addUtilisateur($utilisateur)
    {
        $sql = "INSERT INTO utilisateurs (email, mot_de_passe, prenom, nom, role) VALUES (:email, :mot_de_passe, :prenom, :nom, :role)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                ':email' => $utilisateur->getEmail(),
                ':mot_de_passe' => password_hash($utilisateur->getMotDePasse(), PASSWORD_DEFAULT),
                ':prenom' => $utilisateur->getPrenom(),
                ':nom' => $utilisateur->getNom(),
                ':role' => $utilisateur->getRole()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateUtilisateur($utilisateur, $id)
    {
        $sql = "UPDATE utilisateurs SET email = :email, mot_de_passe = :mot_de_passe, prenom = :prenom, nom = :nom, role = :role WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $hashedPassword = password_verify($utilisateur->getMotDePasse(), '$2y$10$') ? 
                $utilisateur->getMotDePasse() : 
                password_hash($utilisateur->getMotDePasse(), PASSWORD_DEFAULT);
            
            $query->execute([
                ':id' => $id,
                ':email' => $utilisateur->getEmail(),
                ':mot_de_passe' => $hashedPassword,
                ':prenom' => $utilisateur->getPrenom(),
                ':nom' => $utilisateur->getNom(),
                ':role' => $utilisateur->getRole()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function deleteUtilisateur($id)
    {
        $sql = "DELETE FROM utilisateurs WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }
}
