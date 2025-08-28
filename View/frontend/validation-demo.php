<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - Validation JavaScript</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="assets/js/form-validation.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .demo-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .validation-demo {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .code-snippet {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:before {
            content: "✅";
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <div class="text-center mb-4">
            <h1 class="text-primary">
                <i class="fas fa-shield-alt"></i> 
                Démonstration - Validation JavaScript Avancée
            </h1>
            <p class="lead text-muted">
                Système de validation de formulaires complet pour le portail étudiant
            </p>
        </div>

        <!-- Features Overview -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h3><i class="fas fa-cogs text-info"></i> Fonctionnalités Implémentées</h3>
                <ul class="feature-list">
                    <li>Validation en temps réel des champs</li>
                    <li>Validation de fichiers (type, taille)</li>
                    <li>Messages d'erreur personnalisés</li>
                    <li>Validation côté client avancée</li>
                    <li>Support des règles personnalisées</li>
                    <li>Interface Bootstrap intégrée</li>
                    <li>Logging détaillé pour débogage</li>
                    <li>Support multilingue (français)</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3><i class="fas fa-code text-warning"></i> Technologies Utilisées</h3>
                <ul class="feature-list">
                    <li>JavaScript ES6+ (Classes, Modules)</li>
                    <li>DOM Manipulation avancée</li>
                    <li>Event Listeners & Delegation</li>
                    <li>File API pour validation fichiers</li>
                    <li>Regular Expressions (RegEx)</li>
                    <li>Bootstrap 4 pour styling</li>
                    <li>Font Awesome pour icônes</li>
                    <li>Console API pour logging</li>
                </ul>
            </div>
        </div>

        <!-- Code Implementation Example -->
        <div class="validation-demo">
            <h4><i class="fas fa-terminal text-success"></i> Exemple d'Implémentation</h4>
            <p>Voici comment le système de validation est intégré dans le projet :</p>
            
            <div class="code-snippet">
<pre>// Initialisation du validateur pour soumission de documents
const validator = new FormValidator('form[enctype="multipart/form-data"]');

// Configuration des règles de validation
validator
    .addRule('titre', {
        required: true,
        minLength: 5,
        maxLength: 100,
        alphanumeric: true,
        custom: function(value) {
            const words = value.trim().split(/\s+/);
            if (words.length < 2) {
                return 'Le titre doit contenir au moins 2 mots';
            }
            return true;
        }
    })
    .addRule('document', {
        required: true,
        maxFileSize: 10, // 10MB
        allowedTypes: ['pdf', 'doc', 'docx', 'txt'],
        custom: function(value, field) {
            const file = field.files[0];
            if (file.name.length < 5) {
                return 'Le nom du fichier doit être plus descriptif';
            }
            return true;
        }
    });</pre>
            </div>
        </div>

        <!-- Live Demo Form -->
        <div class="validation-demo">
            <h4><i class="fas fa-play text-primary"></i> Démonstration en Direct</h4>
            <p>Testez le système de validation ci-dessous :</p>
            
            <form method="POST" enctype="multipart/form-data" class="mt-3">
                <div class="form-group">
                    <label for="demo-titre">
                        <i class="fas fa-file-alt mr-2"></i>
                        Titre du Document (minimum 5 caractères, 2 mots)
                    </label>
                    <input type="text" 
                           name="titre" 
                           id="demo-titre"
                           class="form-control" 
                           placeholder="Entrez un titre descriptif">
                </div>
                
                <div class="form-group">
                    <label for="demo-document">
                        <i class="fas fa-upload mr-2"></i>
                        Fichier (PDF, DOC, DOCX, TXT - max 10MB)
                    </label>
                    <input type="file" 
                           name="document" 
                           id="demo-document"
                           class="form-control" 
                           accept=".pdf,.doc,.docx,.txt">
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Tester la Validation
                    </button>
                </div>
            </form>
        </div>

        <!-- Console Output Display -->
        <div class="validation-demo">
            <h4><i class="fas fa-bug text-danger"></i> Logs de Validation (Console)</h4>
            <p>Ouvrez la console du navigateur (F12) pour voir les logs détaillés de validation :</p>
            
            <div class="code-snippet">
<pre id="console-output">
✅ Form validation library loaded and ready
🔄 Initializing document form validation...
📝 Setting up validation for document form 1
✨ Document form 1 validation setup complete
🎯 All form validations initialized successfully
</pre>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i>
                <strong>Instructions :</strong> 
                Essayez de soumettre le formulaire avec des données invalides pour voir 
                les messages d'erreur en action. Tous les événements sont loggés dans 
                la console pour démontrer le fonctionnement du système.
            </div>
        </div>

        <!-- File Integration Info -->
        <div class="validation-demo">
            <h4><i class="fas fa-file-code text-secondary"></i> Intégration dans le Projet</h4>
            <p>Le système de validation est intégré dans les fichiers suivants :</p>
            
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-js text-warning"></i> Fichier JavaScript Principal</h6>
                    <div class="code-snippet">
<pre>📁 assets/js/form-validation.js
├── 📋 Classe FormValidator
├── 🔧 Méthodes de validation
├── 🎨 Interface Bootstrap
└── 🌐 Support multilingue</pre>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-code text-info"></i> Intégration PHP</h6>
                    <div class="code-snippet">
<pre>📁 partials/header.php
├── 📦 Inclusion script validation
├── 🔗 Liens CDN Bootstrap/FontAwesome
└── 📱 Configuration responsive

📁 mes_sujets.php
├── 🎯 Initialisation validation docs
├── ⚙️ Configuration règles custom
└── 📊 Logging événements</pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="mes_sujets.php" class="btn btn-success btn-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au Portail Étudiant
            </a>
        </div>
    </div>

    <script>
        // Demo-specific initialization
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎬 Demo page loaded - initializing validation showcase');
            
            // Override console.log to also display in demo
            const originalLog = console.log;
            const outputElement = document.getElementById('console-output');
            
            console.log = function(...args) {
                originalLog.apply(console, args);
                if (outputElement) {
                    outputElement.textContent += args.join(' ') + '\n';
                }
            };
            
            // Initialize validation for demo form
            if (typeof FormValidator !== 'undefined') {
                const demoValidator = new FormValidator('form[method="POST"]');
                
                demoValidator
                    .addRule('titre', {
                        required: true,
                        minLength: 5,
                        maxLength: 100,
                        custom: function(value) {
                            const words = value.trim().split(/\s+/);
                            if (words.length < 2) {
                                return 'Le titre doit contenir au moins 2 mots';
                            }
                            return true;
                        }
                    })
                    .addRule('document', {
                        required: true,
                        maxFileSize: 10,
                        allowedTypes: ['pdf', 'doc', 'docx', 'txt']
                    });
                    
                console.log('✅ Demo form validation initialized successfully');
            } else {
                console.error('❌ FormValidator class not found - check script loading');
            }
        });
    </script>
</body>
</html>