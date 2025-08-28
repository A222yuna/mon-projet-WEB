<!--
Author: W3layouts
Author URL: http://w3layouts.com
-->
<!doctype html>
<html lang="zxx">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo $pageTitle ?></title>
  <!-- google fonts -->
  <link href="//fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="//fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&display=swap"
    rel="stylesheet">
  <!-- google fonts -->
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style-starter.css">
  <!-- Dashboard Analytics Styles -->
  <link rel="stylesheet" href="assets/css/dashboard-analytics.css">
  <!-- Form Validation Library -->
  <script src="assets/js/form-validation.js" defer></script>
  <!-- Dashboard Analytics Library -->
  <script src="assets/js/dashboard-analytics.js" defer></script>
  <!-- Template CSS -->
</head>

<body>
  <!--header-->
  <header id="site-header" class="fixed-top">
    <div class="container">
      <nav class="navbar navbar-expand-lg stroke">
 
      <a class="navbar-brand" href="./">
          <img src="assets/images/cultrify-logo.png" alt="Your logo" style="height: 70px;" />
      </a>
        <button class="navbar-toggler  collapsed bg-gradient" type="button" data-toggle="collapse"
          data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
          aria-label="Toggle navigation">
          <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
          <span class="navbar-toggler-icon fa icon-close fa-times"></span>
          </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="nav-link" href="./">Accueil <span class="sr-only">(actuel)</span></a>
            </li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'etudiant'): ?>
              <li class="nav-item">
                <a class="nav-link" href="mes_sujets.php">
                  <i class="fas fa-book mr-1"></i>Mes Sujets
                </a>
              </li>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'enseignant'): ?>
              <li class="nav-item">
                <a class="nav-link" href="teacher_dashboard.php">
                  <i class="fas fa-chalkboard-teacher mr-1"></i>Tableau de Bord Enseignant
                </a>
              </li>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="../backend/index.php">
                  <i class="fas fa-cog mr-1"></i>Panneau Administrateur
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
        <?php 
          if(!$loggedin) {
            echo ('
              <div class="d-lg-block d-none">
                <a href="./auth/signup.php" style="left: 30px;" class="btn btn-style btn-secondary">
                  <i class="fas fa-user-plus mr-1"></i>S\'inscrire
                </a>
              </div>
              <div class="d-lg-block d-none">
                <a href="./auth/login.php" style="left: 30px;" class="btn btn-style btn-secondary">
                  <i class="fas fa-sign-in-alt mr-1"></i>Connexion
                </a>
              </div>
            ');
          } else {
            echo '
            <div>
              <span style="left: 30px;" class="btn btn-style btn-link text-white">
                <i class="fas fa-user mr-1"></i>Bonjour '.$fullname.'! 
              </span>
              <a href="auth/logout.php" style="left: 30px;" class="btn btn-style btn-secondary">
                <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
              </a>
            </div>
            ';
          }
        ?>

        <!-- toggle switch for light and dark theme -->
        <div class="mobile-position">
          <nav class="navigation">
            <div class="theme-switch-wrapper">
              <label class="theme-switch" for="checkbox">
                <input type="checkbox" id="checkbox">
                <div class="mode-container">
                  <i class="gg-sun"></i>
                  <i class="gg-moon"></i>
                </div>
              </label>
            </div>
          </nav>
        </div>
        <!-- //toggle switch for light and dark theme -->
      </nav>
    </div>
  </header>
  <!-- //header -->