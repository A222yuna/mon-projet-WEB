<?php
session_start();
include_once '../../../Controller/UtilisateurC.php';
include_once '../../../Model/Utilisateur.php';

$errMsg = '';
$prenomErr = $nomErr = $passwordErr = $emailErr = $confirmPasswordErr = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($prenom)) $prenomErr = 'First name is required!';
    if (empty($nom)) $nomErr = 'Last name is required!';
    if (empty($email)) {
        $emailErr = 'Email is required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = 'Please enter a valid email address!';
    }
    if (empty($password)) {
        $passwordErr = 'Password is required!';
    } elseif (strlen($password) < 8) {
        $passwordErr = 'Password must be at least 8 characters long!';
    }
    if ($password !== $confirmPassword) {
        $confirmPasswordErr = 'Passwords do not match!';
    }
    
    // If no validation errors, attempt registration
    if (empty($prenomErr) && empty($nomErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        try {
            $utilisateurC = new UtilisateurC();
            
            // Check if email already exists
            if ($utilisateurC->getUserByEmail($email)) {
                $emailErr = 'This email address is already registered!';
            } else {
                // Create new user
                $utilisateur = new Utilisateur(null, $email, $password, $prenom, $nom, 'etudiant');
                $utilisateurC->addUtilisateur($utilisateur);
                
                // Redirect to login with success message
                header('Location: login.php?accountCreated=true');
                exit();
            }
        } catch (Exception $e) {
            $errMsg = 'An error occurred during registration. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Cultrify</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Lato', sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .signup-container {
            width: 100%;
            max-width: 500px;
            margin: 20px;
        }
        .signup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .signup-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
        }
        .signup-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .signup-body {
            padding: 40px 30px;
        }
        .form-control {
            padding: 15px 20px 15px 50px;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            background: #f8f9fa;
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: #f5576c;
            background: white;
            box-shadow: 0 5px 15px rgba(245, 87, 108, 0.2);
        }
        .form-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .btn-signup {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: -15px;
            margin-bottom: 10px;
            padding-left: 20px;
        }
        .back-to-home {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="back-to-home">
        <a href="../index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>

    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <div class="signup-logo">
                    <i class="fas fa-user-plus fa-2x"></i>
                </div>
                <h1>Join Cultrify</h1>
                <p>Create your account to get started</p>
            </div>

            <div class="signup-body">
                <?php if (!empty($errMsg)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errMsg) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="position-relative">
                                <input type="text" name="prenom" class="form-control" placeholder="First Name" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                                <i class="fas fa-user form-icon"></i>
                            </div>
                            <?php if (!empty($prenomErr)): ?>
                                <div class="error-message"><?= htmlspecialchars($prenomErr) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative">
                                <input type="text" name="nom" class="form-control" placeholder="Last Name" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                                <i class="fas fa-user form-icon"></i>
                            </div>
                            <?php if (!empty($nomErr)): ?>
                                <div class="error-message"><?= htmlspecialchars($nomErr) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="position-relative">
                        <input type="email" name="email" class="form-control" placeholder="Email Address" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        <i class="fas fa-envelope form-icon"></i>
                    </div>
                    <?php if (!empty($emailErr)): ?>
                        <div class="error-message"><?= htmlspecialchars($emailErr) ?></div>
                    <?php endif; ?>

                    <div class="position-relative">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <i class="fas fa-lock form-icon"></i>
                    </div>
                    <?php if (!empty($passwordErr)): ?>
                        <div class="error-message"><?= htmlspecialchars($passwordErr) ?></div>
                    <?php endif; ?>

                    <div class="position-relative">
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                        <i class="fas fa-lock form-icon"></i>
                    </div>
                    <?php if (!empty($confirmPasswordErr)): ?>
                        <div class="error-message"><?= htmlspecialchars($confirmPasswordErr) ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn-signup">Create Account</button>
                </form>

                <div class="text-center">
                    Already have an account? <a href="login.php" style="color: #f5576c;">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>