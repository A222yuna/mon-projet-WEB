<?php
session_start();
include_once '../../../Controller/UtilisateurC.php';
include_once '../../../Model/Utilisateur.php';

$successMsg = '';
$errMsg = '';
$passwordErr = '';
$emailErr = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Handle account creation success message
if (isset($_GET['accountCreated']) && $_GET['accountCreated'] == 'true') {
    $successMsg = 'Your Account is Successfully Created! Please login.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email)) {
        $emailErr = 'Email is required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = 'Please enter a valid email address!';
    }
    
    if (empty($password)) {
        $passwordErr = 'Password is required!';
    }
    
    // If no validation errors, attempt login
    if (empty($emailErr) && empty($passwordErr)) {
        try {
            $utilisateurC = new UtilisateurC();
            $user = $utilisateurC->authenticateUser($email, $password);
            
            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['fullname'] = $user['prenom'] . ' ' . $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: ../../backend/index.php');
                } elseif ($user['role'] === 'enseignant') {
                    header('Location: ../teacher_dashboard.php');
                } else {
                    // etudiant role
                    header('Location: ../mes_sujets.php');
                }
                exit();
            } else {
                $errMsg = 'Invalid email or password. Please try again.';
            }
        } catch (Exception $e) {
            $errMsg = 'An error occurred. Please try again later.';
            error_log('Login error: ' . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cultrify</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-color: #28a745;
            --error-color: #dc3545;
            --warning-color: #ffc107;
            --dark-color: #2c3e50;
            --light-gray: #f8f9fa;
            --border-radius: 15px;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
            --shadow-hover: 0 20px 40px rgba(0,0,0,0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.3;
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            margin: 20px;
        }

        .login-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-5px);
        }

        .login-header {
            background: var(--primary-gradient);
            color: white;
            text-align: center;
            padding: 40px 30px;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            border-radius: 20px 20px 0 0;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
        }

        .login-logo i {
            font-size: 2rem;
            color: white;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            font-family: 'Montserrat', sans-serif;
        }

        .login-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .login-body {
            padding: 40px 30px;
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f1b0b7);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .form-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.1rem;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .form-control:focus + .form-icon {
            color: #667eea;
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.875rem;
            margin-top: 8px;
            padding-left: 20px;
            font-weight: 500;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            background: white;
            padding: 0 20px;
            color: #6c757d;
            font-weight: 500;
        }

        .signup-link {
            text-align: center;
            color: #6c757d;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .signup-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .back-to-home {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 3;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-weight: 500;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .login-container {
                margin: 10px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .login-title {
                font-size: 1.8rem;
            }
        }

        .loading {
            display: none;
        }

        .btn-login.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-login.loading .loading {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Back to Home Button -->
    <div class="back-to-home">
        <a href="../index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to your Cultrify account</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                <!-- Success Message -->
                <?php if (!empty($successMsg)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($successMsg) ?>
                    </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if (!empty($errMsg)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errMsg) ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="" id="loginForm">
                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="position-relative">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control <?= !empty($emailErr) ? 'is-invalid' : '' ?>" 
                                   placeholder="Enter your email address"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   required>
                            <i class="fas fa-envelope form-icon"></i>
                        </div>
                        <?php if (!empty($emailErr)): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($emailErr) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control <?= !empty($passwordErr) ? 'is-invalid' : '' ?>" 
                                   placeholder="Enter your password"
                                   required>
                            <i class="fas fa-lock form-icon"></i>
                        </div>
                        <?php if (!empty($passwordErr)): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($passwordErr) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="btn-text">Sign In</span>
                        <span class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Signing In...
                        </span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider">
                    <span>or</span>
                </div>

                <!-- Signup Link -->
                <div class="signup-link">
                    Don't have an account? <a href="signup.php">Create Account</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
        });

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

        // Focus effects
        document.querySelectorAll('.form-control').forEach(function(input) {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>