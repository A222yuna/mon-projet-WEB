<?php
include_once '../../Controller/SujetC.php';
$sujetC = new SujetC();

// Handle add sujet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre']) && !isset($_POST['edit_id']) && !isset($_POST['delete_id'])) {
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $propose_par = $_POST['propose_par'] ?? '';
    $statut = $_POST['statut'] ?? '';

    $sujet = new Sujet(null, $titre, $description, $propose_par, $statut);
    $sujetC->addSujet($sujet);
    echo '<script>window.location.href = "sujets.php";</script>';
    exit;
}

$sujets = $sujetC->listSujets();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cultrify - Gestion des Sujets</title>
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
                    <a href="sujets.php" class="nav-item nav-link active"><i class="fa fa-book me-2"></i>Sujets</a>
                    <a href="documents.php" class="nav-item nav-link"><i class="fa fa-file me-2"></i>Documents</a>
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
                        <h6 class="mb-0">Gestion des Sujets</h6>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSujetModal">Ajouter Sujet</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th scope="col">ID</th>
                                    <th scope="col">Titre</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Proposé par</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sujets as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['id']) ?></td>
                                    <td><?= htmlspecialchars($s['titre']) ?></td>
                                    <td><?= htmlspecialchars($s['description']) ?></td>
                                    <td><?= htmlspecialchars($s['propose_par']) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= $s['statut'] == 'ouvert' ? 'bg-success' : 
                                               ($s['statut'] == 'ferme' ? 'bg-danger' : 'bg-warning') ?>">
                                            <?= htmlspecialchars($s['statut']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editSujetModal<?= $s['id'] ?>">Modifier</button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSujetModal<?= $s['id'] ?>">Supprimer</button>
                                                <!-- Edit Sujet Modal -->
                                                <?php foreach ($sujets as $s): ?>
                                                <div class="modal fade" id="editSujetModal<?= $s['id'] ?>" tabindex="-1" aria-labelledby="editSujetModalLabel<?= $s['id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editSujetModalLabel<?= $s['id'] ?>">Modifier Sujet</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="post" action="">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="edit_id" value="<?= $s['id'] ?>">
                                                                    <div class="mb-3">
                                                                        <label for="edit_titre<?= $s['id'] ?>" class="form-label">Titre</label>
                                                                        <input type="text" class="form-control bg-white" id="edit_titre<?= $s['id'] ?>" name="edit_titre" value="<?= htmlspecialchars($s['titre']) ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="edit_description<?= $s['id'] ?>" class="form-label">Description</label>
                                                                        <input type="text" class="form-control bg-white" id="edit_description<?= $s['id'] ?>" name="edit_description" value="<?= htmlspecialchars($s['description']) ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="edit_propose_par<?= $s['id'] ?>" class="form-label">Proposé par</label>
                                                                        <input type="text" class="form-control bg-white" id="edit_propose_par<?= $s['id'] ?>" name="edit_propose_par" value="<?= htmlspecialchars($s['propose_par']) ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="edit_statut<?= $s['id'] ?>" class="form-label">Statut</label>
                                                                        <select class="form-select bg-white" id="edit_statut<?= $s['id'] ?>" name="edit_statut" required>
                                                                            <option value="ouvert" <?= $s['statut'] == 'ouvert' ? 'selected' : '' ?>>Ouvert</option>
                                                                            <option value="ferme" <?= $s['statut'] == 'ferme' ? 'selected' : '' ?>>Fermé</option>
                                                                            <option value="en_attente" <?= $s['statut'] == 'en_attente' ? 'selected' : '' ?>>En Attente</option>
                                                                        </select>
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
                                                <!-- Delete Sujet Modal -->
                                                <div class="modal fade" id="deleteSujetModal<?= $s['id'] ?>" tabindex="-1" aria-labelledby="deleteSujetModalLabel<?= $s['id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteSujetModalLabel<?= $s['id'] ?>">Supprimer Sujet</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="post" action="">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="delete_id" value="<?= $s['id'] ?>">
                                                                    <p>Êtes-vous sûr de vouloir supprimer ce sujet ?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
<?php
// Handle edit sujet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $titre = $_POST['edit_titre'] ?? '';
    $description = $_POST['edit_description'] ?? '';
    $propose_par = $_POST['edit_propose_par'] ?? '';
    $statut = $_POST['edit_statut'] ?? '';

    $sujet = new Sujet($id, $titre, $description, $propose_par, $statut);
    $sujetC->updateSujet($sujet, $id);
    echo '<script>window.location.href = "sujets.php";</script>';
    exit;
}

// Handle delete sujet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $sujetC->deleteSujet($id);
    echo '<script>window.location.href = "sujets.php";</script>';
    exit;
}
?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Users Table End -->

                        <!-- Add Sujet Modal -->
                        <div class="modal fade" id="addSujetModal" tabindex="-1" aria-labelledby="addSujetModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addSujetModalLabel">Ajouter un Sujet</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="post" action="">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="titre" class="form-label">Titre</label>
                                                <input type="text" class="form-control bg-white" id="titre" name="titre" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <input type="text" class="form-control bg-white" id="description" name="description" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="propose_par" class="form-label">Proposé par</label>
                                                <input type="text" class="form-control bg-white" id="propose_par" name="propose_par" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="statut" class="form-label">Statut</label>
                                                <select class="form-select bg-white" id="statut" name="statut" required>
                                                    <option value="en_attente">En attente</option>
                                                    <option value="valide">Validé</option>
                                                    <option value="refuse">Refusé</option>
                                                </select>
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre'])) {
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $propose_par = $_POST['propose_par'] ?? '';
    $statut = $_POST['statut'] ?? '';

    $sujet = new Sujet(null, $titre, $description, $propose_par, $statut);
    $sujetC = new SujetC();
    $sujetC->addSujet($sujet);
    echo '<script>window.location.href = "sujets.php";</script>';
    exit;
}
?>


            <!-- Footer Start -->
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