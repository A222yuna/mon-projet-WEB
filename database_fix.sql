-- Database structure verification and creation script for documents table
-- Based on the structure you showed: id, etudiant_sujet_id, titre, type_fichier, statut

-- Check if documents table exists and create if needed
CREATE TABLE IF NOT EXISTS `documents` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `etudiant_sujet_id` int(11) NOT NULL,
    `titre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `type_fichier` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    `statut` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'en_attente',
    PRIMARY KEY (`id`),
    KEY `etudiant_sujet_id` (`etudiant_sujet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraint if it doesn't exist
-- ALTER TABLE `documents` 
-- ADD CONSTRAINT `fk_documents_etudiant_sujets` 
-- FOREIGN KEY (`etudiant_sujet_id`) REFERENCES `etudiant_sujets`(`id`) 
-- ON DELETE CASCADE ON UPDATE CASCADE;

-- Sample data to test the structure
-- INSERT INTO `documents` (`etudiant_sujet_id`, `titre`, `type_fichier`, `statut`) 
-- VALUES (1, 'Test Document', 'pdf', 'en_attente');

-- Verify the structure
DESCRIBE `documents`;