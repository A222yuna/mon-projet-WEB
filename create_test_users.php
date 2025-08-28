<?php
// Test script to create admin user
include_once '../Controller/UtilisateurC.php';
include_once '../Model/Utilisateur.php';

try {
    $utilisateurC = new UtilisateurC();
    
    // Create admin user
    $admin = new Utilisateur(
        null,
        'admin@cultrify.com',
        'Admin123!', // This will be hashed automatically
        'Admin',
        'User',
        'admin'
    );
    
    // Check if admin already exists
    $existingAdmin = $utilisateurC->getUserByEmail('admin@cultrify.com');
    if (!$existingAdmin) {
        $utilisateurC->addUtilisateur($admin);
        echo "Admin user created successfully!<br>";
        echo "Email: admin@cultrify.com<br>";
        echo "Password: Admin123!<br>";
    } else {
        echo "Admin user already exists!<br>";
    }
    
    // Create test student user
    $student = new Utilisateur(
        null,
        'student@cultrify.com',
        'Student123!', // This will be hashed automatically
        'Test',
        'Student',
        'etudiant'
    );
    
    $existingStudent = $utilisateurC->getUserByEmail('student@cultrify.com');
    if (!$existingStudent) {
        $utilisateurC->addUtilisateur($student);
        echo "Student user created successfully!<br>";
        echo "Email: student@cultrify.com<br>";
        echo "Password: Student123!<br>";
    } else {
        echo "Student user already exists!<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>