<?php
session_start();
include_once '../../Controller/SujetC.php';
include_once '../../Controller/EtudiantSujetC.php';
include_once '../../Controller/DocumentC.php';
include_once '../../Model/EtudiantSujet.php';
include_once '../../Model/Document.php';
include_once '../../config.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: auth/login.php');
    exit();
}

$sujetC = new SujetC();
$etudiantSujetC = new EtudiantSujetC();
$documentC = new DocumentC();

$successMsg = $errMsg = '';
$etudiant_id = $_SESSION['user_id'];

// Handle subject selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choisir_sujet'])) {
    $sujet_id = $_POST['sujet_id'] ?? '';
    
    if (empty($sujet_id)) {
        $errMsg = 'Veuillez sélectionner un sujet!';
    } elseif ($etudiantSujetC->hasStudentChosenSubject($etudiant_id, $sujet_id)) {
        $errMsg = 'Vous avez déjà choisi ce sujet!';
    } else {
        try {
            $etudiantSujet = new EtudiantSujet(null, $etudiant_id, $sujet_id, 'en_cours', null);
            $etudiantSujetC->addEtudiantSujet($etudiantSujet);
            // Redirect to My Subjects tab to show the newly added subject
            header('Location: mes_sujets.php?added=true#my-subjects');
            exit();
        } catch (Exception $e) {
            $errMsg = 'Une erreur s\'est produite lors de la sélection du sujet.';
        }
    }
}

// Handle document submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_document'])) {
    // FORCE DEBUG OUTPUT TO SCREEN - TEMPORARY
    echo "<div style='position:fixed; top:0; left:0; background:red; color:white; padding:20px; z-index:9999; width:100%; max-height:300px; overflow:auto;'>";
    echo "<h3>🔍 REAL FORM DEBUG (mes_sujets.php)</h3>";
    echo "<strong>POST:</strong><pre>" . print_r($_POST, true) . "</pre>";
    echo "<strong>FILES:</strong><pre>" . print_r($_FILES, true) . "</pre>";
    echo "<strong>Session User:</strong> " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
    echo "<strong>Request Method:</strong> " . $_SERVER['REQUEST_METHOD'] . "<br>";
    echo "<strong>Content Type:</strong> " . ($_SERVER['CONTENT_TYPE'] ?? 'NOT SET') . "<br>";
    
    $etudiant_sujet_id = $_POST['etudiant_sujet_id'] ?? '';
    $titre = trim($_POST['titre'] ?? '');
    
    echo "<strong>Extracted Data:</strong><br>";
    echo "- etudiant_sujet_id: '$etudiant_sujet_id'<br>";
    echo "- titre: '$titre'<br>";
    echo "- Files uploaded: " . (isset($_FILES['document']) ? 'YES' : 'NO') . "<br>";
    
    if (isset($_FILES['document'])) {
        echo "- File error code: " . $_FILES['document']['error'] . "<br>";
        echo "- File name: " . $_FILES['document']['name'] . "<br>";
        echo "- File size: " . $_FILES['document']['size'] . "<br>";
    }
    
    echo "<button onclick='this.parentElement.style.display=\"none\"'>Close Debug</button>";
    echo "</div>";
    
    // Debug: Add comprehensive logging
    error_log("🔍 DOCUMENT SUBMISSION DEBUG:");
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    error_log("etudiant_sujet_id: $etudiant_sujet_id");
    error_log("titre: $titre");
    
    // Check current status of the etudiant_sujet
    $currentEtudiantSujet = $etudiantSujetC->showEtudiantSujet($etudiant_sujet_id);
    
    // Prevent submission if already submitted (except for rejected cases)
    if ($currentEtudiantSujet && in_array($currentEtudiantSujet['statut'], ['soumis', 'approuve'])) {
        $errMsg = 'Vous ne pouvez pas soumettre de documents pour ce sujet car il a déjà été soumis ou approuvé.';
    } elseif (empty($titre)) {
        $errMsg = 'Le titre du document est requis!';
    } elseif (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $errMsg = 'Veuillez sélectionner un fichier à télécharger! Erreur: ' . ($_FILES['document']['error'] ?? 'Aucun fichier sélectionné');
        error_log("File upload error: " . print_r($_FILES, true));
    } else {
        $file = $_FILES['document'];
        $allowedTypes = ['pdf', 'doc', 'docx', 'txt'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        error_log("File details: name={$file['name']}, size={$file['size']}, type={$file['type']}, extension=$fileExtension");
        
        if (!in_array($fileExtension, $allowedTypes)) {
            $errMsg = 'Seuls les fichiers PDF, DOC, DOCX et TXT sont autorisés!';
        } elseif ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
            $errMsg = 'La taille du fichier doit être inférieure à 10MB!';
        } else {
            // Create uploads directory if it doesn't exist
            $uploadDir = __DIR__ . '/../../uploads/documents/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $errMsg = 'Échec de la création du répertoire de téléchargement.';
                } else {
                    chmod($uploadDir, 0777);
                }
            }
            
            if (empty($errMsg)) {
                // Generate unique filename to prevent conflicts
                $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
                $filePath = $uploadDir . $fileName;
                // Store relative path for database (without full system path)
                $relativePath = 'uploads/documents/' . $fileName;
                
                error_log("📁 Attempting to save file to: $filePath");
                error_log("💾 Database will store relative path: $relativePath");
                
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    error_log("✅ File uploaded successfully to: $filePath");
                    
                    try {
                        error_log("🛠️ Creating Document object with: etudiant_sujet_id=$etudiant_sujet_id, titre=$titre, fileExtension=$fileExtension, relativePath=$relativePath");
                        
                        // Use Document model and controller properly with file path
                        $document = new Document(
                            null,
                            $etudiant_sujet_id,
                            $titre,
                            $fileExtension,
                            $relativePath, // Store relative path in database
                            'brouillon'
                        );
                        
                        error_log("✅ Document object created successfully");
                        
                        $documentId = $documentC->addDocument($document);
                        
                        error_log("Database insert result: " . ($documentId ? "SUCCESS (ID: $documentId)" : "FAILED"));
                        
                        if ($documentId) {
                            // Update the etudiant_sujets status to show work has been submitted
                            $updateResult = $etudiantSujetC->updateEtudiantSujetStatus($etudiant_sujet_id, 'soumis');
                            error_log("Status update result: " . ($updateResult ? "SUCCESS" : "FAILED"));
                            
                            // Redirect with success message
                            error_log("🎉 Submission completed successfully, redirecting...");
                            header('Location: mes_sujets.php?submitted=true#my-subjects');
                            exit();
                        } else {
                            $errMsg = 'Échec de la sauvegarde du document dans la base de données.';
                            error_log("❌ Database save failed");
                            // Clean up uploaded file
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                        }
                    } catch (Exception $e) {
                        $errMsg = 'Erreur de base de données: ' . $e->getMessage();
                        error_log("Document submission error: " . $e->getMessage());
                        // Clean up uploaded file if database save fails
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                } else {
                    $errMsg = 'Échec du téléchargement du fichier. Veuillez vérifier les permissions du fichier.';
                }
            }
        }
    }
}

// Get available subjects
$allSujets = $sujetC->listSujets();

// Get student's chosen subjects
$mesSujets = $etudiantSujetC->getEtudiantSujetByStudentId($etudiant_id);

// Separate subjects by status for better organization
$activeSubjects = []; // Can still submit (en_cours, rejete)
$completedSubjects = []; // Cannot submit anymore (soumis, approuve)

foreach ($mesSujets as $sujet) {
    if (in_array($sujet['statut'], ['en_cours', 'rejete'])) {
        $activeSubjects[] = $sujet;
    } else {
        $completedSubjects[] = $sujet;
    }
}

// Check for subject addition success
if (isset($_GET['added']) && $_GET['added'] === 'true') {
    $successMsg = 'Sujet ajouté avec succès! Vous pouvez maintenant soumettre votre travail depuis l\'onglet "Mes Sujets".';
}

// Check for submission success
if (isset($_GET['submitted']) && $_GET['submitted'] === 'true') {
    $successMsg = 'Document soumis avec succès! Vous pouvez le voir dans l\'onglet "Mes Sujets".';
}

// Check if we should default to My Subjects tab
$defaultToMySubjects = isset($_GET['submitted']) && $_GET['submitted'] === 'true' || isset($_GET['added']) && $_GET['added'] === 'true';

// Also default to My Subjects if student has any submitted work
if (!$defaultToMySubjects && !empty($mesSujets)) {
    foreach ($mesSujets as $sujet) {
        if (in_array($sujet['statut'], ['soumis', 'approuve', 'rejete'])) {
            $defaultToMySubjects = true;
            break;
        }
    }
}

// Set variables for header
$loggedin = true;
$fullname = $_SESSION['fullname'];
$pageTitle = 'Mes Sujets - Cultrify';

include_once 'partials/header.php';
?>

<style>
/* Enhanced gradient background with animation */
body { 
    background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c); 
    background-size: 400% 400%; 
    animation: gradientShift 15s ease infinite;
    min-height: 100vh; 
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Container and layout improvements */
.subjects-container { 
    padding: 100px 0 60px; 
    position: relative;
}

.subjects-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    pointer-events: none;
}

/* Enhanced card design */
.subject-card { 
    background: rgba(255, 255, 255, 0.95); 
    backdrop-filter: blur(20px);
    border-radius: 25px; 
    box-shadow: 0 20px 60px rgba(0,0,0,0.15); 
    margin-bottom: 40px; 
    overflow: hidden; 
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
}

.subject-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 300% 100%;
    animation: borderGlow 3s ease-in-out infinite;
}

@keyframes borderGlow {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.subject-card:hover { 
    transform: translateY(-15px) scale(1.02); 
    box-shadow: 0 30px 80px rgba(0,0,0,0.25);
}

.subject-card.border-warning {
    border-left: 6px solid #ffc107;
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.05), rgba(255, 255, 255, 0.95));
}

.subject-card.border-success {
    border-left: 6px solid #28a745;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(255, 255, 255, 0.95));
}

/* Enhanced headers */
.subject-header { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%); 
    color: white; 
    padding: 40px 35px;
    position: relative;
    overflow: hidden;
}

.subject-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.subject-card:hover .subject-header::before {
    transform: translateX(100%);
}

.subject-header.bg-success { 
    background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%) !important; 
}

.subject-body { 
    padding: 40px 35px;
    position: relative;
}

/* Enhanced buttons */
.btn-choose { 
    background: linear-gradient(135deg, #667eea, #764ba2); 
    color: white; 
    border: none; 
    padding: 15px 35px; 
    border-radius: 50px; 
    font-weight: 600;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-choose::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-choose:hover::before {
    left: 100%;
}

.btn-choose:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
}

.btn-submit { 
    background: linear-gradient(135deg, #f093fb, #f5576c); 
    color: white; 
    border: none; 
    padding: 15px 35px; 
    border-radius: 50px; 
    font-weight: 600;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-submit::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-submit:hover::before {
    left: 100%;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(240, 147, 251, 0.4);
}

.btn-secondary:disabled, .btn-success:disabled { 
    opacity: 0.7; 
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

/* Enhanced form controls */
.form-control { 
    padding: 18px 25px; 
    border: 2px solid rgba(102, 126, 234, 0.1); 
    border-radius: 15px; 
    margin-bottom: 25px;
    background: rgba(248, 249, 250, 0.8);
    backdrop-filter: blur(10px);
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-control:focus { 
    border-color: #667eea; 
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 8px 25px rgba(102,126,234,0.15), 0 0 0 3px rgba(102,126,234,0.1);
    transform: translateY(-2px);
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 10px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Enhanced document items */
.document-item { 
    background: linear-gradient(135deg, rgba(248, 249, 250, 0.8), rgba(255, 255, 255, 0.9)); 
    padding: 25px; 
    border-radius: 15px; 
    margin-bottom: 20px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.document-item:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Enhanced status badges */
.status-badge { 
    padding: 8px 18px; 
    border-radius: 25px; 
    font-size: 12px; 
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}

.status-en-attente { 
    background: linear-gradient(135deg, #fff3cd, #ffeaa7); 
    color: #856404;
    border: 1px solid rgba(133, 100, 4, 0.2);
}

.status-en_cours { 
    background: linear-gradient(135deg, #cce7ff, #74b9ff); 
    color: #004085;
    border: 1px solid rgba(0, 64, 133, 0.2);
}

.status-soumis { 
    background: linear-gradient(135deg, #d1ecf1, #81ecec); 
    color: #0c5460;
    border: 1px solid rgba(12, 84, 96, 0.2);
}

.status-approuve { 
    background: linear-gradient(135deg, #d4edda, #00b894); 
    color: #155724;
    border: 1px solid rgba(21, 87, 36, 0.2);
}

.status-rejete { 
    background: linear-gradient(135deg, #f8d7da, #fd79a8); 
    color: #721c24;
    border: 1px solid rgba(114, 28, 36, 0.2);
}

/* Enhanced navigation tabs */
.nav-tabs { 
    border: none; 
    margin-bottom: 40px;
    justify-content: center;
    position: relative;
}

.nav-tabs::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
}

.nav-tabs .nav-link { 
    border: none; 
    border-radius: 30px; 
    margin: 0 15px; 
    padding: 15px 30px; 
    background: rgba(255, 255, 255, 0.1); 
    backdrop-filter: blur(10px);
    color: rgba(255, 255, 255, 0.8); 
    font-weight: 600;
    font-size: 16px;
    transition: all 0.4s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.nav-tabs .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-2px);
}

.nav-tabs .nav-link.active { 
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.95));
    color: #667eea;
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.4);
}

.nav-tabs .badge { 
    background: rgba(102, 126, 234, 0.8) !important; 
    color: white !important;
    border-radius: 12px;
    padding: 4px 8px;
    font-size: 11px;
}

.nav-tabs .nav-link:not(.active) .badge { 
    background: rgba(255, 255, 255, 0.3) !important; 
    color: rgba(255, 255, 255, 0.9) !important; 
}

/* Enhanced alerts */
.alert {
    border-radius: 15px;
    border: none;
    padding: 20px 25px;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.alert-success {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
    border-left: 4px solid #28a745;
    color: #155724;
}

.alert-warning {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 234, 167, 0.1));
    border-left: 4px solid #ffc107;
    color: #856404;
}

.alert-info {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(116, 185, 255, 0.1));
    border-left: 4px solid #17a2b8;
    color: #0c5460;
}

/* Enhanced section headers */
h4.text-white {
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    font-weight: 700;
    margin-bottom: 30px;
    position: relative;
    padding-left: 60px;
}

h4.text-white i {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

/* Enhanced page title */
.display-4 {
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    font-weight: 800;
    letter-spacing: 2px;
}

.lead {
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    font-size: 20px;
    opacity: 0.9;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .subjects-container {
        padding: 80px 0 40px;
    }
    
    .subject-card {
        margin-bottom: 25px;
    }
    
    .subject-header, .subject-body {
        padding: 25px 20px;
    }
    
    .nav-tabs .nav-link {
        margin: 5px;
        padding: 12px 20px;
        font-size: 14px;
    }
    
    h4.text-white {
        font-size: 20px;
        padding-left: 45px;
    }
    
    .btn-choose, .btn-submit {
        padding: 12px 25px;
        font-size: 14px;
    }
}

/* Loading animation for better UX */
.fadeInUp {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<section class="subjects-container">
    <div class="container">
        <div class="text-center mb-5 fadeInUp">
            <h1 class="display-4 font-weight-bold text-white mb-4">
                <i class="fas fa-graduation-cap mr-3"></i>
                Mes Sujets de Recherche
            </h1>
            <p class="lead text-white">Choisissez des sujets et soumettez vos documents de recherche facilement</p>
            <div class="mt-4">
                <span class="badge badge-light px-3 py-2 mr-2">
                    <i class="fas fa-tasks mr-1"></i> Total: <?= count($mesSujets) ?>
                </span>
                <span class="badge badge-warning px-3 py-2 mr-2">
                    <i class="fas fa-clock mr-1"></i> En Attente: <?= count($activeSubjects) ?>
                </span>
                <span class="badge badge-success px-3 py-2">
                    <i class="fas fa-check mr-1"></i> Terminés: <?= count($completedSubjects) ?>
                </span>
            </div>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success fadeInUp">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Succès!</strong> <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($errMsg)): ?>
            <div class="alert alert-danger fadeInUp">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Erreur!</strong> <?= htmlspecialchars($errMsg) ?>
                <br><small>Débogage: Vérifiez la console du navigateur et les journaux du serveur pour plus de détails.</small>
                
                <!-- Debug Information (only show on error) -->
                <?php if (isset($_POST['submit_document'])): ?>
                    <br><small>
                        <strong>🔍 Info de Débogage:</strong>
                        POST détecté: <?= isset($_POST['submit_document']) ? 'OUI' : 'NON' ?> |
                        ID Sujet Étudiant: <?= htmlspecialchars($_POST['etudiant_sujet_id'] ?? 'MANQUANT') ?> |
                        Titre: <?= htmlspecialchars($_POST['titre'] ?? 'MANQUANT') ?> |
                        Fichier téléchargé: <?= isset($_FILES['document']) ? 'OUI' : 'NON' ?>
                        <?php if (isset($_FILES['document'])): ?>
                            | Erreur fichier: <?= $_FILES['document']['error'] ?>
                            | Nom fichier: <?= htmlspecialchars($_FILES['document']['name'] ?? 'AUCUN') ?>
                        <?php endif; ?>
                    </small>
                <?php endif; ?>
            </div>
        <?php endif; ?>



        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs justify-content-center" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= !$defaultToMySubjects ? 'active' : '' ?>" data-toggle="tab" href="#available-subjects">
                    <i class="fas fa-list"></i> Sujets Disponibles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $defaultToMySubjects ? 'active' : '' ?>" data-toggle="tab" href="#my-subjects">
                    <i class="fas fa-bookmark"></i> Mes Sujets 
                    <?php if (count($mesSujets) > 0): ?>
                        <span class="badge badge-light ml-1"><?= count($mesSujets) ?></span>
                        <?php if (count($activeSubjects) > 0): ?>
                            <small class="text-warning ml-1">(<?= count($activeSubjects) ?> en attente)</small>
                        <?php endif; ?>
                        <?php if (count($completedSubjects) > 0): ?>
                            <small class="text-success ml-1">(<?= count($completedSubjects) ?> terminés)</small>
                        <?php endif; ?>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Available Subjects Tab -->
            <div class="tab-pane fade <?= !$defaultToMySubjects ? 'show active' : '' ?>" id="available-subjects">
                <div class="row">
                    <?php if (empty($allSujets)): ?>
                        <div class="col-12">
                            <div class="subject-card">
                                <div class="subject-body text-center">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h3>Aucun Sujet Disponible</h3>
                                    <p class="text-muted">Il n'y a actuellement aucun sujet de recherche disponible.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($allSujets as $sujet): ?>
                            <div class="col-lg-6 col-xl-4">
                                <div class="subject-card">
                                    <div class="subject-header">
                                        <h4 class="mb-0"><?= htmlspecialchars($sujet['titre']) ?></h4>
                                    </div>
                                    <div class="subject-body">
                                        <p class="text-muted mb-4"><?= htmlspecialchars($sujet['description']) ?></p>
                                        
                                        <?php if ($etudiantSujetC->hasStudentChosenSubject($etudiant_id, $sujet['id'])): ?>
                                            <button class="btn btn-success" disabled>
                                                <i class="fas fa-check"></i> Déjà Choisi
                                            </button>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="sujet_id" value="<?= $sujet['id'] ?>">
                                                <button type="submit" name="choisir_sujet" class="btn btn-choose">
                                                    <i class="fas fa-plus"></i> Choisir ce Sujet
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- My Subjects Tab -->
            <div class="tab-pane fade <?= $defaultToMySubjects ? 'show active' : '' ?>" id="my-subjects">
                
                <!-- Active Subjects Section -->
                <?php if (!empty($activeSubjects)): ?>
                    <div class="mb-5">
                        <h4 class="text-white mb-4">
                            <i class="fas fa-edit text-warning"></i> Sujets Nécessitant une Action (<?= count($activeSubjects) ?>)
                        </h4>
                        <div class="row">
                            <?php foreach ($activeSubjects as $index => $monSujet): ?>
                                <div class="col-lg-6 fadeInUp" style="animation-delay: <?= $index * 0.1 ?>s;">
                                    <div class="subject-card border-warning">
                                        <div class="subject-header">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h4 class="mb-2">
                                                        <i class="fas fa-book mr-2"></i>
                                                        <?= htmlspecialchars($monSujet['sujet_titre']) ?>
                                                    </h4>
                                                    <span class="status-badge status-<?= $monSujet['statut'] ?>">
                                                        <?php
                                                        $statusLabels = [
                                                            'en_cours' => 'En Cours',
                                                            'soumis' => 'Travail Soumis',
                                                            'approuve' => 'Approuvé',
                                                            'rejete' => 'Nécessite une Révision'
                                                        ];
                                                        echo $statusLabels[$monSujet['statut']] ?? ucfirst(str_replace('_', ' ', $monSujet['statut']));
                                                        ?>
                                                    </span>
                                                    <?php if (!empty($monSujet['note'])): ?>
                                                        <div class="mt-2">
                                                            <span class="badge badge-secondary px-3 py-2" style="font-size: 0.9rem;">
                                                                <i class="fas fa-star mr-1"></i>
                                                                Précédent: <?= htmlspecialchars($monSujet['note']) ?>/20
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-right">
                                                    <i class="fas fa-clock text-white-50 fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="subject-body">
                                            <p class="text-muted mb-4"><?= htmlspecialchars($monSujet['sujet_description']) ?></p>
                                            
                                            <!-- Document Submission Form -->
                                            <div class="border-top pt-4">
                                                <?php if ($monSujet['statut'] === 'rejete'): ?>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> 
                                                        <strong>Le Travail Nécessite une Révision.</strong> Veuillez examiner les commentaires et resoumettre.
                                                        <?php if (!empty($monSujet['note'])): ?>
                                                            <br><strong>Note Précédente: <?= htmlspecialchars($monSujet['note']) ?>/20</strong>
                                                        <?php endif; ?>
                                                    </div>
                                                    <h5 class="mb-3">
                                                        <i class="fas fa-upload"></i> Resoumettre le Document
                                                    </h5>
                                                <?php else: // en_cours status ?>
                                                    <h5 class="mb-3">
                                                        <i class="fas fa-upload"></i> Soumettre le Document
                                                    </h5>
                                                <?php endif; ?>
                                                <form method="POST" enctype="multipart/form-data" class="modern-form">
                                                    <input type="hidden" name="etudiant_sujet_id" value="<?= $monSujet['id'] ?>">
                                                    
                                                    <div class="form-group">
                                                        <label class="form-label">
                                                            <i class="fas fa-file-alt mr-2"></i>
                                                            Titre du Document
                                                        </label>
                                                        <input type="text" name="titre" class="form-control" 
                                                               placeholder="Entrez un titre descriptif pour votre document" required>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label class="form-label">
                                                            <i class="fas fa-upload mr-2"></i>
                                                            Choisir un Fichier
                                                        </label>
                                                        <input type="file" name="document" class="form-control" 
                                                               accept=".pdf,.doc,.docx,.txt" required>
                                                        <small class="text-muted mt-2 d-block">
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                            Formats acceptés: PDF, DOC, DOCX, TXT (Taille maximum: 10MB)
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="text-center mt-4">
                                                        <button type="submit" name="submit_document" class="btn btn-submit btn-lg">
                                                            <i class="fas fa-paper-plane mr-2"></i>
                                                            <?= $monSujet['statut'] === 'rejete' ? 'Resoumettre' : 'Soumettre' ?> le Document
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Existing Documents for this subject -->
                                            <?php
                                            $documents = $documentC->getDocumentsByEtudiantSujetId($monSujet['id']);
                                            if (!empty($documents)):
                                            ?>
                                                <div class="border-top pt-4 mt-4">
                                                    <h5 class="mb-3"><i class="fas fa-file-alt"></i> Soumissions Précédentes</h5>
                                                    <?php foreach ($documents as $doc): ?>
                                                        <div class="document-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1"><?= htmlspecialchars($doc['titre']) ?></h6>
                                                                    <small class="text-muted">
                                                                        <?= strtoupper($doc['type_fichier']) ?> file
                                                                    </small>
                                                                </div>
                                                                <span class="status-badge status-<?= $doc['statut'] ?>">
                                                                    <?php
                                                                    $docStatusLabels = [
                                                                        'brouillon' => 'En Attente d\'Examen',
                                                                        'soumis' => 'Soumis',
                                                                        'approuve' => 'Approuvé',
                                                                        'rejete' => 'Rejeté'
                                                                    ];
                                                                    echo $docStatusLabels[$doc['statut']] ?? ucfirst(str_replace('_', ' ', $doc['statut']));
                                                                    ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Completed Subjects Section -->
                <?php if (!empty($completedSubjects)): ?>
                    <div class="mb-4">
                        <h4 class="text-white mb-4">
                            <i class="fas fa-check-circle text-success"></i> Sujets Terminés (<?= count($completedSubjects) ?>)
                        </h4>
                        <div class="row">
                            <?php foreach ($completedSubjects as $index => $monSujet): ?>
                                <div class="col-lg-6 fadeInUp" style="animation-delay: <?= ($index + count($activeSubjects)) * 0.1 ?>s;">
                                    <div class="subject-card border-success">
                                        <div class="subject-header bg-success">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h4 class="mb-2">
                                                        <i class="fas fa-book mr-2"></i>
                                                        <?= htmlspecialchars($monSujet['sujet_titre']) ?>
                                                    </h4>
                                                    <span class="status-badge status-<?= $monSujet['statut'] ?>">
                                                        <?php
                                                        $statusLabels = [
                                                            'en_cours' => 'En Cours',
                                                            'soumis' => 'Travail Soumis',
                                                            'approuve' => 'Approuvé',
                                                            'rejete' => 'Nécessite une Révision'
                                                        ];
                                                        echo $statusLabels[$monSujet['statut']] ?? ucfirst(str_replace('_', ' ', $monSujet['statut']));
                                                        ?>
                                                    </span>
                                                    <?php if (!empty($monSujet['note'])): ?>
                                                        <div class="mt-2">
                                                            <span class="badge badge-warning px-3 py-2" style="font-size: 1rem;">
                                                                <i class="fas fa-star mr-1"></i>
                                                                Note: <?= htmlspecialchars($monSujet['note']) ?>/20
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-right">
                                                    <i class="fas fa-<?= $monSujet['statut'] === 'approuve' ? 'trophy' : 'hourglass-half' ?> text-white-50 fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="subject-body">
                                            <p class="text-muted mb-4"><?= htmlspecialchars($monSujet['sujet_description']) ?></p>
                                            
                                            <!-- Status Message -->
                                            <?php if ($monSujet['statut'] === 'soumis'): ?>
                                                <div class="alert alert-success">
                                                    <i class="fas fa-check-circle"></i> 
                                                    <strong>Travail Soumis avec Succès!</strong> Vos documents sont en cours d'examen.
                                                    <?php if (!empty($monSujet['note'])): ?>
                                                        <br><strong>Note Actuelle: <?= htmlspecialchars($monSujet['note']) ?>/20</strong>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-center">
                                                    <button class="btn btn-secondary" disabled>
                                                        <i class="fas fa-lock"></i> Soumission Terminée
                                                        <?php if (!empty($monSujet['note'])): ?>
                                                            - <?= htmlspecialchars($monSujet['note']) ?>/20
                                                        <?php endif; ?>
                                                    </button>
                                                </div>
                                            <?php elseif ($monSujet['statut'] === 'approuve'): ?>
                                                <div class="alert alert-success">
                                                    <i class="fas fa-award"></i> 
                                                    <strong>Travail Approuvé!</strong> Félicitations, votre soumission a été approuvée.
                                                    <?php if (!empty($monSujet['note'])): ?>
                                                        <br><strong>Note Finale: <?= htmlspecialchars($monSujet['note']) ?>/20</strong>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-center">
                                                    <button class="btn btn-success" disabled>
                                                        <i class="fas fa-trophy"></i> Approuvé
                                                        <?php if (!empty($monSujet['note'])): ?>
                                                            - <?= htmlspecialchars($monSujet['note']) ?>/20
                                                        <?php endif; ?>
                                                    </button>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Final Documents -->
                                            <?php
                                            $documents = $documentC->getDocumentsByEtudiantSujetId($monSujet['id']);
                                            if (!empty($documents)):
                                            ?>
                                                <div class="border-top pt-4 mt-4">
                                                    <h5 class="mb-3"><i class="fas fa-file-alt"></i> Documents Soumis</h5>
                                                    <?php foreach ($documents as $doc): ?>
                                                        <div class="document-item">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1"><?= htmlspecialchars($doc['titre']) ?></h6>
                                                                    <small class="text-muted">
                                                                        <?= strtoupper($doc['type_fichier']) ?> file
                                                                    </small>
                                                                </div>
                                                                <span class="status-badge status-<?= $doc['statut'] ?>">
                                                                    <?php
                                                                    $docStatusLabels = [
                                                                        'brouillon' => 'En Attente d\'Examen',
                                                                        'soumis' => 'Soumis',
                                                                        'approuve' => 'Approuvé',
                                                                        'rejete' => 'Rejeté'
                                                                    ];
                                                                    echo $docStatusLabels[$doc['statut']] ?? ucfirst(str_replace('_', ' ', $doc['statut']));
                                                                    ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- No Subjects Message -->
                <?php if (empty($mesSujets)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="subject-card">
                                <div class="subject-body text-center">
                                    <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                                    <h3>Aucun Sujet Choisi</h3>
                                    <p class="text-muted">Vous n'avez pas encore choisi de sujets de recherche. Allez dans l'onglet "Sujets Disponibles" pour en sélectionner un.</p>
                                    <a href="#available-subjects" class="btn btn-choose" data-toggle="tab">
                                        <i class="fas fa-search"></i> Parcourir les Sujets Disponibles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// PHP variable to JavaScript
const defaultToMySubjects = <?= $defaultToMySubjects ? 'true' : 'false' ?>;

// Add fadeInUp animation to elements as they become visible
function animateOnScroll() {
    const elements = document.querySelectorAll('.fadeInUp');
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
}

// Handle URL fragment for tab navigation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize fade animations
    const fadeElements = document.querySelectorAll('.fadeInUp');
    fadeElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'all 0.6s ease-out';
    });
    
    // Trigger initial animation
    setTimeout(() => {
        animateOnScroll();
    }, 100);
    
    // Add scroll listener for animations
    window.addEventListener('scroll', animateOnScroll);
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.subject-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Check if there's a hash in the URL
    if (window.location.hash) {
        const hash = window.location.hash;
        if (hash === '#my-subjects' || hash === '#available-subjects') {
            // Remove active class from all tabs and tab content
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Activate the correct tab
            const targetTab = document.querySelector(`a[href="${hash}"]`);
            const targetPane = document.querySelector(hash);
            
            if (targetTab && targetPane) {
                targetTab.classList.add('active');
                targetPane.classList.add('show', 'active');
                
                // If Bootstrap tab functionality is available, use it
                if (typeof $ !== 'undefined') {
                    $(targetTab).tab('show');
                } else if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    const tabInstance = new bootstrap.Tab(targetTab);
                    tabInstance.show();
                }
            }
        }
    } else if (defaultToMySubjects && !window.location.hash) {
        // If no hash but we should default to My Subjects, set it
        const mySubjectsTab = document.querySelector('a[href="#my-subjects"]');
        if (mySubjectsTab) {
            window.location.hash = '#my-subjects';
            mySubjectsTab.click();
        }
    }
});

// Add click handler for tabs to update URL
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.getAttribute('href');
        });
    });
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 5000);

// Enhanced file validation with better UX
document.querySelectorAll('input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function() {
        const file = this.files[0];
        const feedback = this.parentNode.querySelector('.file-feedback') || document.createElement('div');
        feedback.className = 'file-feedback mt-2';
        
        if (!this.parentNode.querySelector('.file-feedback')) {
            this.parentNode.appendChild(feedback);
        }
        
        if (file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedExtensions = ['pdf', 'doc', 'docx', 'txt'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            if (file.size > maxSize) {
                feedback.innerHTML = '<small class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>File size must be less than 10MB!</small>';
                this.value = '';
                this.classList.add('is-invalid');
                return;
            }
            
            if (!allowedExtensions.includes(fileExtension)) {
                feedback.innerHTML = '<small class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Only PDF, DOC, DOCX, and TXT files are allowed!</small>';
                this.value = '';
                this.classList.add('is-invalid');
                return;
            }
            
            // File appears valid
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            feedback.innerHTML = `<small class="text-success"><i class="fas fa-check-circle mr-1"></i>File selected: ${file.name} (${fileSize}MB)</small>`;
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            feedback.innerHTML = '';
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
});

// Add form submission handling with better feedback
document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const titleInput = this.querySelector('input[name="titre"]');
        const fileInput = this.querySelector('input[name="document"]');
        const etudiantSujetId = this.querySelector('input[name="etudiant_sujet_id"]');
        
        console.log('🚀 FORM SUBMISSION DETECTED:', {
            title: titleInput?.value,
            file: fileInput?.files[0]?.name,
            etudiantSujetId: etudiantSujetId?.value,
            formAction: this.action || 'same page',
            formMethod: this.method
        });
        
        // Basic validation WITH DETAILED LOGGING
        if (!titleInput || !titleInput.value.trim()) {
            console.error('❌ VALIDATION FAILED: Title is empty');
            e.preventDefault();
            titleInput?.focus();
            alert('Please enter a document title!');
            return;
        }
        
        if (!fileInput || !fileInput.files[0]) {
            console.error('❌ VALIDATION FAILED: No file selected');
            e.preventDefault();
            fileInput?.focus();
            alert('Please select a file to upload!');
            return;
        }
        
        if (!etudiantSujetId || !etudiantSujetId.value) {
            console.error('❌ VALIDATION FAILED: Missing etudiant_sujet_id');
            e.preventDefault();
            alert('Missing etudiant_sujet_id. This is a system error.');
            return;
        }
        
        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        }
        
        console.log('✅ VALIDATION PASSED - Form will submit now!');
        // DO NOT preventDefault here - let form submit naturally
    });
    });
});

// Initialize advanced form validation for all document submission forms
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing custom form validation for mes_sujets.php');
    
    // Enhanced document submission validation
    const documentForms = document.querySelectorAll('form[method="POST"][enctype="multipart/form-data"]');
    
    documentForms.forEach((form, index) => {
        console.log(`Setting up validation for document form ${index + 1}`);
        
        // Create validator instance
        const validator = new FormValidator(form);
        
        // Add specific validation rules for document submission
        validator
            .addRule('titre', {
                required: true,
                minLength: 5,
                maxLength: 100,
                alphanumeric: true,
                custom: function(value) {
                    // Custom validation: must contain meaningful text
                    const words = value.trim().split(/\s+/);
                    if (words.length < 2) {
                        return 'Le titre doit contenir au moins 2 mots';
                    }
                    // Check for forbidden words
                    const forbiddenWords = ['test', 'exemple', 'demo'];
                    if (forbiddenWords.some(word => value.toLowerCase().includes(word))) {
                        return 'Le titre ne peut pas contenir de mots de test';
                    }
                    return true;
                }
            })
            .addRule('document', {
                required: true,
                maxFileSize: 10, // 10MB limit
                allowedTypes: ['pdf', 'doc', 'docx', 'txt'],
                custom: function(value, field) {
                    if (field.files.length === 0) {
                        return 'Veuillez sélectionner un fichier pour la soumission';
                    }
                    
                    const file = field.files[0];
                    
                    // Validate file name
                    if (file.name.length < 5) {
                        return 'Le nom du fichier doit être plus descriptif';
                    }
                    
                    // Check for proper file naming
                    const invalidChars = /[<>:"/\\|?*]/;
                    if (invalidChars.test(file.name)) {
                        return 'Le nom du fichier contient des caractères non autorisés';
                    }
                    
                    return true;
                }
            });
            
        // Add visual feedback for form validation
        form.addEventListener('submit', function(e) {
            console.log('Document submission form validation triggered');
            
            // Add loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Validation en cours...';
                
                // Re-enable button after validation
                setTimeout(() => {
                    if (validator.hasErrors()) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Soumettre le Document';
                    }
                }, 100);
            }
        });
        
        console.log(`Document form ${index + 1} validation setup complete`);
    });
    
    // Add subject selection validation
    const subjectLinks = document.querySelectorAll('a[href*="sujet_id="]');
    subjectLinks.forEach((link, index) => {
        link.addEventListener('click', function(e) {
            console.log(`Subject selection ${index + 1} validation triggered`);
            
            // Simulate validation check
            const confirmSelection = confirm('Êtes-vous sûr de vouloir choisir ce sujet? Cette action ne peut pas être annulée.');
            if (!confirmSelection) {
                e.preventDefault();
                console.log('Subject selection cancelled by user');
            } else {
                console.log('Subject selection confirmed');
            }
        });
    });
    
    console.log('All form validations initialized successfully for mes_sujets.php');
});

// Initialize Dashboard Analytics and Custom Tracking
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔥 Initializing advanced dashboard analytics...');
    
    // Custom analytics tracking for subject management page
    if (typeof window.AnalyticsHelpers !== 'undefined') {
        // Track page load
        window.AnalyticsHelpers.trackEvent('page_load', {
            page: 'mes_sujets',
            total_subjects: document.querySelectorAll('.subject-card').length,
            user_role: 'etudiant',
            load_time: performance.now()
        });
        
        // Track subject interactions
        document.querySelectorAll('.subject-card').forEach((card, index) => {
            card.addEventListener('mouseenter', function() {
                window.AnalyticsHelpers.trackEvent('subject_hover', {
                    subject_index: index,
                    subject_title: card.querySelector('h4') ? card.querySelector('h4').textContent.trim() : 'Unknown'
                });
            });
        });
        
        // Track form interactions
        document.querySelectorAll('form input, form textarea, form select').forEach(input => {
            input.addEventListener('focus', function() {
                window.AnalyticsHelpers.trackEvent('form_field_focus', {
                    field_name: this.name || this.id,
                    field_type: this.type,
                    form_context: 'document_submission'
                });
            });
            
            input.addEventListener('blur', function() {
                window.AnalyticsHelpers.trackEvent('form_field_blur', {
                    field_name: this.name || this.id,
                    field_filled: this.value.length > 0,
                    value_length: this.value.length
                });
            });
        });
        
        // Track button clicks
        document.querySelectorAll('button, .btn').forEach(button => {
            button.addEventListener('click', function(e) {
                window.AnalyticsHelpers.trackEvent('button_click', {
                    button_text: this.textContent.trim(),
                    button_class: this.className,
                    action_type: this.type || 'button'
                });
            });
        });
        
        // Track scroll behavior
        let scrollTimer = null;
        let maxScroll = 0;
        
        window.addEventListener('scroll', function() {
            const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
            maxScroll = Math.max(maxScroll, scrollPercent);
            
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => {
                window.AnalyticsHelpers.trackEvent('scroll_activity', {
                    max_scroll_percent: maxScroll,
                    current_scroll: scrollPercent,
                    page_height: document.body.scrollHeight
                });
            }, 1000);
        });
        
        // Track time spent on different sections
        const sectionTimers = {};
        
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5
        };
        
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const sectionId = entry.target.id || entry.target.className;
                
                if (entry.isIntersecting) {
                    sectionTimers[sectionId] = Date.now();
                } else if (sectionTimers[sectionId]) {
                    const timeSpent = Date.now() - sectionTimers[sectionId];
                    window.AnalyticsHelpers.trackEvent('section_time_spent', {
                        section: sectionId,
                        time_spent_ms: timeSpent,
                        time_spent_seconds: Math.round(timeSpent / 1000)
                    });
                    delete sectionTimers[sectionId];
                }
            });
        }, observerOptions);
        
        // Observe major sections
        document.querySelectorAll('.tab-pane, .subject-card, .form-group').forEach(section => {
            sectionObserver.observe(section);
        });
        
        // Track file selection details
        document.querySelectorAll('input[type="file"]').forEach(fileInput => {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    window.AnalyticsHelpers.trackEvent('file_selected', {
                        file_name: file.name,
                        file_size: file.size,
                        file_type: file.type,
                        file_extension: file.name.split('.').pop().toLowerCase(),
                        input_name: this.name
                    });
                    
                    // Analyze file properties
                    const fileSizeCategory = file.size < 1024 * 1024 ? 'small' : 
                                           file.size < 5 * 1024 * 1024 ? 'medium' : 'large';
                    
                    window.AnalyticsHelpers.trackEvent('file_analysis', {
                        size_category: fileSizeCategory,
                        is_pdf: file.type === 'application/pdf',
                        is_document: ['doc', 'docx'].includes(file.name.split('.').pop().toLowerCase()),
                        upload_context: 'document_submission'
                    });
                }
            });
        });
        
        // Track tab switching behavior
        document.querySelectorAll('[data-toggle="tab"]').forEach(tab => {
            tab.addEventListener('click', function(e) {
                const tabTarget = this.getAttribute('href') || this.getAttribute('data-target');
                const tabName = this.textContent.trim();
                
                window.AnalyticsHelpers.trackEvent('tab_switch', {
                    from_tab: document.querySelector('.nav-link.active') ? 
                             document.querySelector('.nav-link.active').textContent.trim() : 'unknown',
                    to_tab: tabName,
                    tab_target: tabTarget,
                    switch_time: new Date().toISOString()
                });
            });
        });
        
        // Track form validation errors
        document.addEventListener('invalid', function(e) {
            window.AnalyticsHelpers.trackEvent('form_validation_error', {
                field_name: e.target.name || e.target.id,
                validation_message: e.target.validationMessage,
                field_value_length: e.target.value.length,
                error_type: 'html5_validation'
            });
        }, true);
        
        // Track successful form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Only track if form passes validation
                if (this.checkValidity()) {
                    const formData = new FormData(this);
                    const formFields = {};
                    
                    for (let [key, value] of formData.entries()) {
                        formFields[key] = typeof value === 'string' ? value.length : 'file';
                    }
                    
                    window.AnalyticsHelpers.trackEvent('form_submission_success', {
                        form_fields: formFields,
                        submission_time: new Date().toISOString(),
                        form_action: this.action || 'current_page'
                    });
                }
            });
        });
        
        // Performance monitoring
        window.addEventListener('load', function() {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                
                window.AnalyticsHelpers.trackEvent('page_performance', {
                    dom_content_loaded: Math.round(perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart),
                    page_load_time: Math.round(perfData.loadEventEnd - perfData.loadEventStart),
                    dns_lookup_time: Math.round(perfData.domainLookupEnd - perfData.domainLookupStart),
                    server_response_time: Math.round(perfData.responseEnd - perfData.requestStart)
                });
            }, 1000);
        });
        
        // Browser compatibility tracking
        window.AnalyticsHelpers.trackEvent('browser_info', {
            user_agent: navigator.userAgent,
            viewport_width: window.innerWidth,
            viewport_height: window.innerHeight,
            screen_resolution: `${screen.width}x${screen.height}`,
            color_depth: screen.colorDepth,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            language: navigator.language
        });
        
        console.log('✅ Advanced analytics tracking initialized for mes_sujets.php');
    } else {
        console.warn('⚠️ Analytics helpers not available - basic tracking only');
    }
});
</script>

<?php include_once 'partials/footer.php'; ?>

<!-- Bootstrap JS for tab functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>