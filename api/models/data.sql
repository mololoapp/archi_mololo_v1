CREATE TABLE artistes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_complet VARCHAR(255) NOT NULL,
    nom_artiste VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    whatsapp VARCHAR(20) NOT NULL,
    genres TEXT,
    photo_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);