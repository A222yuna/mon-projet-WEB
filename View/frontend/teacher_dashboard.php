<?php
session_start();
include_once '../../Controller/SujetC.php';
include_once '../../Controller/EtudiantSujetC.php';
include_once '../../Controller/DocumentC.php';
include_once '../../Model/Sujet.php';
include_once '../../config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'enseignant') {
    header('Location: auth/login.php');
    exit();
}

$sujetC = new SujetC();
$etudiantSujetC = new EtudiantSujetC();
$documentC = new DocumentC();

$successMsg = $errMsg = '';
$enseignant_id = $_SESSION['user_id'];

// Handle file download
if (isset($_GET['download']) && isset($_GET['submission_id'])) {
    $submission_id = $_GET['submission_id'];
    
    // Get submission details to verify teacher owns this project
    $submissionDetails = $etudiantSujetC->getSubmissionDetails($submission_id, $enseignant_id);
    
    if ($submissionDetails) {
        // Get the document file path
        $documents = $documentC->getDocumentsBySubmissionId($submission_id);
        
        if (!empty($documents)) {
            $document = $documents[0]; // Get first document
            $filePath = __DIR__ . '/../../' . $document['chemin_fichier'];
            
            if (file_exists($filePath)) {
                // Set headers for file download
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($document['titre']) . '.' . $document['type_fichier'] . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                
                // Clear output buffer
                ob_clean();
                flush();
                
                // Read and output file
                readfile($filePath);
                exit();
            } else {
                $errMsg = 'File not found on server.';
            }
        } else {
            $errMsg = 'No documents found for this submission.';
        }
    } else {
        $errMsg = 'Access denied or submission not found.';
    }
}

// Handle adding new project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if (empty($titre)) {
        $errMsg = 'Project title is required!';
    } elseif (empty($description)) {
        $errMsg = 'Project description is required!';
    } else {
        try {
            $sujet = new Sujet(null, $titre, $description, $enseignant_id);
            $sujetC->addSujet($sujet);
            $successMsg = 'Project added successfully!';
        } catch (Exception $e) {
            $errMsg = 'Failed to add project. Please try again.';
        }
    }
}

// Handle grading student work
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_work'])) {
    $etudiant_sujet_id = $_POST['etudiant_sujet_id'] ?? '';
    $note = $_POST['note'] ?? '';
    $nouveau_statut = $_POST['nouveau_statut'] ?? '';
    
    if (!empty($etudiant_sujet_id) && !empty($nouveau_statut)) {
        try {
            $etudiantSujetC->updateEtudiantSujetGrade($etudiant_sujet_id, $nouveau_statut, $note);
            $successMsg = 'Student work graded successfully!';
        } catch (Exception $e) {
            $errMsg = 'Failed to grade work. Please try again.';
        }
    }
}

// Get teacher's projects
$teacherProjects = $sujetC->getSujetsByTeacher($enseignant_id);

// Get all student submissions for teacher's projects
$studentSubmissions = $etudiantSujetC->getSubmissionsByTeacher($enseignant_id);

// Add document information to submissions
foreach ($studentSubmissions as &$submission) {
    $documents = $documentC->getDocumentsBySubmissionId($submission['id']);
    $submission['documents'] = $documents;
    $submission['has_document'] = !empty($documents);
    if (!empty($documents)) {
        $submission['document_info'] = $documents[0]; // Get first document info
    }
}

// Set page variables
$loggedin = true;
$fullname = $_SESSION['fullname'];
$pageTitle = 'Tableau de Bord Enseignant - Cultrify';

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

.teacher-container { 
    padding: 100px 0 60px; 
    position: relative;
}

.teacher-card { 
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

.teacher-card::before {
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

.teacher-card:hover { 
    transform: translateY(-10px) scale(1.02); 
    box-shadow: 0 30px 80px rgba(0,0,0,0.25);
}

.card-header { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%); 
    color: white; 
    padding: 30px 35px;
    position: relative;
    overflow: hidden;
}

.card-body { 
    padding: 30px 35px;
}

.btn-teacher { 
    background: linear-gradient(135deg, #667eea, #764ba2); 
    color: white; 
    border: none; 
    padding: 12px 30px; 
    border-radius: 50px; 
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-teacher:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8, #20c997);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);
}

.btn-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(23, 162, 184, 0.4);
    color: white;
    text-decoration: none;
}

.d-flex.flex-column.gap-2 > * {
    margin-bottom: 8px;
}

.d-flex.flex-column.gap-2 > *:last-child {
    margin-bottom: 0;
}

.status-pending { 
    background: linear-gradient(135deg, #ffc107, #ff8800); 
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.status-submitted { 
    background: linear-gradient(135deg, #17a2b8, #20c997); 
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.status-approved { 
    background: linear-gradient(135deg, #28a745, #20c997); 
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.status-rejected { 
    background: linear-gradient(135deg, #dc3545, #e83e8c); 
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.nav-tabs .nav-link {
    color: rgba(255, 255, 255, 0.8);
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 25px 25px 0 0;
    margin-right: 10px;
    padding: 15px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link.active { 
    background: rgba(255, 255, 255, 0.9);
    color: #667eea;
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
}

.form-control { 
    padding: 15px 20px; 
    border: 2px solid rgba(102, 126, 234, 0.1); 
    border-radius: 15px; 
    margin-bottom: 20px;
    background: rgba(248, 249, 250, 0.8);
    transition: all 0.3s ease;
}

.form-control:focus { 
    border-color: #667eea; 
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 8px 25px rgba(102,126,234,0.15);
    transform: translateY(-2px);
}

.alert {
    border-radius: 15px;
    border: none;
    padding: 20px 25px;
    margin-bottom: 30px;
}

.alert-success {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
    border-left: 4px solid #28a745;
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(248, 215, 218, 0.5));
    border-left: 4px solid #dc3545;
    color: #721c24;
}

.submission-item {
    background: rgba(255, 255, 255, 0.7);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #667eea;
}

.grade-form {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    padding: 20px;
    margin-top: 15px;
}
</style>

<section class="teacher-container">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-4 font-weight-bold text-white mb-4">
                <i class="fas fa-chalkboard-teacher mr-3"></i>
                Tableau de Bord Enseignant
            </h1>
            <p class="lead text-white">Gérer les projets et examiner les soumissions d'étudiants</p>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Succès!</strong> <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errMsg)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Erreur!</strong> <?= htmlspecialchars($errMsg) ?>
            </div>
        <?php endif; ?>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs justify-content-center mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#projects">
                    <i class="fas fa-project-diagram"></i> Mes Projets
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#submissions">
                    <i class="fas fa-file-signature"></i> Soumissions d'Étudiants
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#add-project">
                    <i class="fas fa-plus"></i> Ajouter un Nouveau Projet
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- My Projects Tab -->
            <div class="tab-pane fade show active" id="projects">
                <div class="row">
                    <?php if (empty($teacherProjects)): ?>
                        <div class="col-12">
                            <div class="teacher-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                                    <h3>Aucun Projet Encore</h3>
                                    <p class="text-muted">Vous n'avez pas encore créé de projets. Cliquez sur "Ajouter un Nouveau Projet" pour commencer.</p>
                                    <a href="#add-project" class="btn btn-teacher" data-toggle="tab">
                                        <i class="fas fa-plus"></i> Créer le Premier Projet
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($teacherProjects as $project): ?>
                            <div class="col-lg-6">
                                <div class="teacher-card">
                                    <div class="card-header">
                                        <h4 class="mb-0">
                                            <i class="fas fa-project-diagram mr-2"></i>
                                            <?= htmlspecialchars($project['titre']) ?>
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3"><?= htmlspecialchars($project['description']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar mr-1"></i>
                                                ID du Projet: <?= $project['id'] ?>
                                            </small>
                                            <span class="badge badge-primary">
                                                <i class="fas fa-users mr-1"></i>
                                                <?= $etudiantSujetC->getProjectStudentCount($project['id']) ?> Étudiants
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Student Submissions Tab -->
            <div class="tab-pane fade" id="submissions">
                <?php if (empty($studentSubmissions)): ?>
                    <div class="teacher-card">
                        <div class="card-body text-center">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h3>Aucune Soumission Encore</h3>
                            <p class="text-muted">Aucun étudiant n'a encore soumis de travail pour vos projets.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($studentSubmissions as $submission): ?>
                        <div class="submission-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-1">
                                        <i class="fas fa-user mr-2"></i>
                                        <?= htmlspecialchars($submission['student_name']) ?>
                                    </h5>
                                    <p class="text-muted mb-1">
                                        <strong>Projet:</strong> <?= htmlspecialchars($submission['sujet_titre']) ?>
                                    </p>
                                    <?php if ($submission['has_document']): ?>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-file mr-1"></i>
                                            <strong>Fichier:</strong> <?= htmlspecialchars($submission['document_info']['titre']) ?>.<?= htmlspecialchars($submission['document_info']['type_fichier']) ?>
                                            <?php
                                            $fileIcon = '';
                                            switch(strtolower($submission['document_info']['type_fichier'])) {
                                                case 'pdf': $fileIcon = 'fas fa-file-pdf text-danger'; break;
                                                case 'doc':
                                                case 'docx': $fileIcon = 'fas fa-file-word text-primary'; break;
                                                case 'txt': $fileIcon = 'fas fa-file-alt text-secondary'; break;
                                                default: $fileIcon = 'fas fa-file text-muted';
                                            }
                                            ?>
                                            <i class="<?= $fileIcon ?> ml-1"></i>
                                        </p>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i>
                                        ID de Soumission: <?= $submission['id'] ?>
                                    </small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <?php
                                    $statusClass = '';
                                    $statusIcon = '';
                                    switch($submission['statut']) {
                                        case 'en_cours':
                                            $statusClass = 'status-pending';
                                            $statusIcon = 'fas fa-hourglass-half';
                                            break;
                                        case 'soumis':
                                            $statusClass = 'status-submitted';
                                            $statusIcon = 'fas fa-upload';
                                            break;
                                        case 'approuve':
                                            $statusClass = 'status-approved';
                                            $statusIcon = 'fas fa-check-circle';
                                            break;
                                        case 'rejete':
                                            $statusClass = 'status-rejected';
                                            $statusIcon = 'fas fa-times-circle';
                                            break;
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>">
                                        <i class="<?= $statusIcon ?> mr-1"></i>
                                        <?= ucfirst($submission['statut']) ?>
                                    </span>
                                    <?php if (!empty($submission['note'])): ?>
                                        <div class="mt-2">
                                            <span class="badge badge-warning">
                                                <i class="fas fa-star mr-1"></i>
                                                <?= htmlspecialchars($submission['note']) ?>/20
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column gap-2">
                                        <?php if ($submission['statut'] === 'soumis'): ?>
                                            <button class="btn btn-sm btn-teacher" onclick="toggleGradeForm('<?= $submission['id'] ?>')">
                                                <i class="fas fa-star mr-1"></i>
                                                Évaluer le Travail
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" onclick="toggleGradeForm('<?= $submission['id'] ?>')">
                                                <i class="fas fa-edit mr-1"></i>
                                                Modifier la Note
                                            </button>
                                        <?php endif; ?>
                                        
                                        <!-- Download Button -->
                                        <?php if ($submission['has_document']): ?>
                                            <a href="teacher_dashboard.php?download=true&submission_id=<?= $submission['id'] ?>" 
                                               class="btn btn-sm btn-info" 
                                               title="Télécharger: <?= htmlspecialchars($submission['document_info']['titre']) ?>.<?= htmlspecialchars($submission['document_info']['type_fichier']) ?>">
                                                <i class="fas fa-download mr-1"></i>
                                                Télécharger le Fichier
                                            </a>
                                        <?php else: ?>
                                            <span class="btn btn-sm btn-outline-secondary disabled">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Aucun Fichier
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Grading Form -->
                            <div id="gradeForm<?= $submission['id'] ?>" class="grade-form" style="display: none;">
                                <form method="POST">
                                    <input type="hidden" name="etudiant_sujet_id" value="<?= $submission['id'] ?>">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">État:</label>
                                            <select name="nouveau_statut" class="form-control" required>
                                                <option value="approuve" <?= $submission['statut'] === 'approuve' ? 'selected' : '' ?>>Approuvé</option>
                                                <option value="rejete" <?= $submission['statut'] === 'rejete' ? 'selected' : '' ?>>Rejeté</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Note (sur 20):</label>
                                            <input type="number" name="note" class="form-control" min="0" max="20" 
                                                   value="<?= htmlspecialchars($submission['note'] ?? '') ?>" 
                                                   placeholder="Entrer la note">
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" name="grade_work" class="btn btn-teacher">
                                                <i class="fas fa-save mr-1"></i>
                                                Sauvegarder la Note
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Add New Project Tab -->
            <div class="tab-pane fade" id="add-project">
                <div class="teacher-card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-plus mr-2"></i>
                            Créer un Nouveau Projet
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-heading mr-2"></i>
                                    Titre du Projet
                                </label>
                                <input type="text" name="titre" class="form-control" 
                                       placeholder="Entrer le titre du projet" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-align-left mr-2"></i>
                                    Description du Projet
                                </label>
                                <textarea name="description" class="form-control" rows="5" 
                                          placeholder="Décrire les objectifs du projet, les exigences et les livrables..." required></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="add_project" class="btn btn-teacher btn-lg">
                                    <i class="fas fa-plus mr-2"></i>
                                    Créer le Projet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleGradeForm(submissionId) {
    const form = document.getElementById('gradeForm' + submissionId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
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
</script>

<?php include_once 'partials/footer.php'; ?>

<!-- Bootstrap JS for tab functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>