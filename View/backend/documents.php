<?php
include_once '../../Controller/DocumentC.php';
include_once '../../Model/Document.php';
// ...existing code...
$documentC = new DocumentC();
$documents = $documentC->listDocuments();
// Fetch etudiant_sujet options
include_once '../../Controller/EtudiantSujetC.php';
$etudiantSujetC = new EtudiantSujetC();
$etudiantSujets = $etudiantSujetC->listEtudiantSujets();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cultrify - Gestion des Documents</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">Cultrify</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">Jhon Doe</h6>
                        <span>Admin</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="utilisateurs.php" class="nav-item nav-link"><i class="fa fa-users"></i>  Utilisateurs</a>
                    <a href="sujets.php" class="nav-item nav-link"><i class="fa fa-book me-2"></i>Sujets</a>
                    <a href="documents.php" class="nav-item nav-link active"><i class="fa fa-file me-2"></i>Documents</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control bg-dark border-0" type="search" placeholder="Search">
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-envelope me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Message</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item text-center">See all message</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notificatin</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">
                                <h6 class="fw-normal mb-0">Profile updated</h6>
                                <small>15 minutes ago</small>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <h6 class="fw-normal mb-0">New user added</h6>
                                <small>15 minutes ago</small>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <h6 class="fw-normal mb-0">Password changed</h6>
                                <small>15 minutes ago</small>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item text-center">See all notifications</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">John Doe</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">My Profile</a>
                            <a href="#" class="dropdown-item">Settings</a>
                            <a href="#" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->


            <!-- Users Table Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Gestion des Documents</h6>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">Ajouter Document</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th scope="col">ID</th>
                                    <th scope="col">Etudiant Sujet ID</th>
                                    <th scope="col">Titre</th>
                                    <th scope="col">Type Fichier</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars($d['id']) ?></td>
                                    <td><?= htmlspecialchars($d['etudiant_sujet_id']) ?></td>
                                    <td><?= htmlspecialchars($d['titre']) ?></td>
                                    <td><?= htmlspecialchars($d['type_fichier']) ?></td>
                                    <td><?= htmlspecialchars($d['statut']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editDocumentModal<?= $d['id'] ?>">Modifier</button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDocumentModal<?= $d['id'] ?>">Supprimer</button>
                                        <!-- Edit Document Modal -->
                                        <div class="modal fade" id="editDocumentModal<?= $d['id'] ?>" tabindex="-1" aria-labelledby="editDocumentModalLabel<?= $d['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editDocumentModalLabel<?= $d['id'] ?>">Modifier Document</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form method="post" action="">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="edit_id" value="<?= $d['id'] ?>">
                                                            <div class="mb-3">
                                                                <label for="edit_etudiant_sujet_id<?= $d['id'] ?>" class="form-label">Etudiant Sujet ID</label>
                                                                <input type="text" class="form-control bg-white" id="edit_etudiant_sujet_id<?= $d['id'] ?>" name="edit_etudiant_sujet_id" value="<?= htmlspecialchars($d['etudiant_sujet_id']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit_titre<?= $d['id'] ?>" class="form-label">Titre</label>
                                                                <input type="text" class="form-control bg-white" id="edit_titre<?= $d['id'] ?>" name="edit_titre" value="<?= htmlspecialchars($d['titre']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit_type_fichier<?= $d['id'] ?>" class="form-label">Type Fichier</label>
                                                                <input type="text" class="form-control bg-white" id="edit_type_fichier<?= $d['id'] ?>" name="edit_type_fichier" value="<?= htmlspecialchars($d['type_fichier']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="edit_statut<?= $d['id'] ?>" class="form-label">Statut</label>
                                                                <input type="text" class="form-control bg-white" id="edit_statut<?= $d['id'] ?>" name="edit_statut" value="<?= htmlspecialchars($d['statut']) ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Delete Document Modal -->
                                        <div class="modal fade" id="deleteDocumentModal<?= $d['id'] ?>" tabindex="-1" aria-labelledby="deleteDocumentModalLabel<?= $d['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteDocumentModalLabel<?= $d['id'] ?>">Supprimer Document</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form method="post" action="">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="delete_id" value="<?= $d['id'] ?>">
                                                            <p>Êtes-vous sûr de vouloir supprimer ce document ?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Users Table End -->

            <!-- Add Document Modal -->
            <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDocumentModalLabel">Ajouter un Document</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="etudiant_sujet_id" class="form-label">Etudiant Sujet</label>
                                    <select class="form-select bg-white" id="etudiant_sujet_id" name="etudiant_sujet_id" required>
                                        <option value="">Sélectionner...</option>
                                        <?php foreach ($etudiantSujets as $es): ?>
                                            <option value="<?= htmlspecialchars($es['id']) ?>">
                                                <?= htmlspecialchars($es['id']) ?> - <?= htmlspecialchars($es['nom'] ?? $es['id']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre</label>
                                    <input type="text" class="form-control bg-white" id="titre" name="titre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="type_fichier" class="form-label">Type Fichier</label>
                                    <input type="text" class="form-control bg-white" id="type_fichier" name="type_fichier" required>
                                </div>
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut</label>
                                    <input type="text" class="form-control bg-white" id="statut" name="statut" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<?php
// Handle add document
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etudiant_sujet_id']) && !isset($_POST['edit_id']) && !isset($_POST['delete_id'])) {
    $etudiant_sujet_id = $_POST['etudiant_sujet_id'] ?? '';
    $titre = $_POST['titre'] ?? '';
    $type_fichier = $_POST['type_fichier'] ?? '';
    $statut = $_POST['statut'] ?? '';

    $document = new Document(null, $etudiant_sujet_id, $titre, $type_fichier, $statut);
    $documentC->addDocument($document);
    echo '<script>window.location.href = "documents.php";</script>';
    exit;
}

// Handle edit document
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $etudiant_sujet_id = $_POST['edit_etudiant_sujet_id'] ?? '';
    $titre = $_POST['edit_titre'] ?? '';
    $type_fichier = $_POST['edit_type_fichier'] ?? '';
    $statut = $_POST['edit_statut'] ?? '';

    $document = new Document($id, $etudiant_sujet_id, $titre, $type_fichier, $statut);
    $documentC->updateDocument($document, $id);
    echo '<script>window.location.href = "documents.php";</script>';
    exit;
}

// Handle delete document
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $documentC->deleteDocument($id);
    echo '<script>window.location.href = "documents.php";</script>';
    exit;
}
?>
<?php
// Handle add user (already present)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $role = $_POST['role'] ?? '';

    $utilisateur = new Utilisateur(null, $email, $mot_de_passe, $prenom, $nom, $role);
    $utilisateurC->addUtilisateur($utilisateur);
    echo '<script>window.location.href = "utilisateurs.php";</script>';
    exit;
}

// Handle edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $email = $_POST['edit_email'] ?? '';
    $mot_de_passe = $_POST['edit_mot_de_passe'] ?? '';
    $prenom = $_POST['edit_prenom'] ?? '';
    $nom = $_POST['edit_nom'] ?? '';
    $role = $_POST['edit_role'] ?? '';

    $utilisateur = new Utilisateur($id, $email, $mot_de_passe, $prenom, $nom, $role);
    $utilisateurC->updateUtilisateur($utilisateur, $id);
    echo '<script>window.location.href = "utilisateurs.php";</script>';
    exit;
}

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $utilisateurC->deleteUtilisateur($id);
    echo '<script>window.location.href = "utilisateurs.php";</script>';
    exit;
}
?>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Users Table End -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Cultrify</a>, All Right Reserved. 
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>