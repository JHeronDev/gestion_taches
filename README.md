# gestion_taches
 
 J'ai modifié la BDD initiale:

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    mot_de_passe VARCHAR(255)
);

CREATE TABLE taches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    titre VARCHAR(100),
    description TEXT,
    commentaire TEXT,
    date_limite DATE,
    statut ENUM('En attente', 'Terminée') DEFAULT 'En attente',
    prioritaire ENUM('Normale','Prioritaire') DEFAULT 'Normale',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE categories (
    tache_id INT,
    categorie ENUM('Loisir','Travail','Transport'),
    FOREIGN KEY (tache_id) REFERENCES taches(id) ON DELETE CASCADE
);