<?php
// Quick database test for document submission
include_once 'config.php';

echo "<h1>🔧 Document Database Fix</h1>";

try {
    $db = config::getConnexion();
    echo "<p>✅ Database connection successful</p>";
    
    // Check documents table structure
    echo "<h2>📊 Documents Table Structure:</h2>";
    $stmt = $db->query("DESCRIBE documents");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test document insertion
    echo "<h2>🧪 Test Document Insertion:</h2>";
    
    // First, let's check if we have any etudiant_sujets
    $stmt = $db->query("SELECT id, etudiant_id, sujet_id, statut FROM etudiant_sujets LIMIT 5");
    $etudiantSujets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($etudiantSujets)) {
        echo "<p>❌ No etudiant_sujets found. Please add a subject first.</p>";
    } else {
        echo "<p>✅ Found " . count($etudiantSujets) . " etudiant_sujets:</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Etudiant ID</th><th>Sujet ID</th><th>Status</th></tr>";
        foreach ($etudiantSujets as $es) {
            echo "<tr>";
            echo "<td>" . $es['id'] . "</td>";
            echo "<td>" . $es['etudiant_id'] . "</td>";
            echo "<td>" . $es['sujet_id'] . "</td>";
            echo "<td>" . $es['statut'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test inserting a document
        $testEtudiantSujetId = $etudiantSujets[0]['id'];
        echo "<h3>🧪 Testing Insert with etudiant_sujet_id: $testEtudiantSujetId</h3>";
        
        try {
            $sql = "INSERT INTO documents (etudiant_sujet_id, titre, type_fichier, statut) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([$testEtudiantSujetId, 'Test Document', 'pdf', 'en_attente']);
            
            if ($result) {
                $insertId = $db->lastInsertId();
                echo "<p>✅ Test document inserted successfully with ID: $insertId</p>";
                
                // Clean up test data
                $db->prepare("DELETE FROM documents WHERE id = ?")->execute([$insertId]);
                echo "<p>🧹 Test document cleaned up</p>";
            } else {
                echo "<p>❌ Failed to insert test document</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Error inserting test document: " . $e->getMessage() . "</p>";
        }
    }
    
    // Check existing documents
    echo "<h2>📄 Existing Documents:</h2>";
    $stmt = $db->query("SELECT * FROM documents ORDER BY id DESC LIMIT 10");
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($documents)) {
        echo "<p>No documents found in database.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Etudiant Sujet ID</th><th>Title</th><th>Type</th><th>Status</th></tr>";
        foreach ($documents as $doc) {
            echo "<tr>";
            echo "<td>" . $doc['id'] . "</td>";
            echo "<td>" . $doc['etudiant_sujet_id'] . "</td>";
            echo "<td>" . htmlspecialchars($doc['titre']) . "</td>";
            echo "<td>" . $doc['type_fichier'] . "</td>";
            echo "<td>" . $doc['statut'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<h2>🔗 Quick Links:</h2>";
echo "<p><a href='View/frontend/mes_sujets.php'>← Back to Main Page</a></p>";
echo "<p><a href='test_submission_debug.php'>← Debug Page</a></p>";
?>