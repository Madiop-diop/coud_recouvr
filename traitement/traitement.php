<?php
// ############ FONCTION POUR RECUPERER LES TITULAIRES ##############################
function getTitulaireByPavillon($pavillon, $conn) {
    $sql = "
        SELECT 
            l.pavillon,
            l.chambre,
            l.lit,
            e.id_etu AS etudiant_id,
            e.num_etu AS num_etu,
            lg.id_paie AS id_paie,
            CONCAT(e.nom, ' ', e.prenoms) AS titulaire_nom
        FROM 
            codif_lit l
        JOIN 
            codif_affectation a ON l.id_lit = a.id_lit
        JOIN 
            codif_etudiant e ON a.id_etu = e.id_etu
        LEFT JOIN 
            codif_loger lg ON lg.id_etu = e.id_etu
        WHERE 
            l.pavillon = ?
            AND lg.statut = 'Titulaire'
        GROUP BY 
            l.pavillon, l.chambre, l.lit, e.id_etu
        ORDER BY 
            l.pavillon, l.chambre, l.lit;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pavillon);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

// ###############    FONCTION POUR RECUPERER LES TITULAIRES ET SES VOISINS #######################3
function getEtudiantByLit($lit, $paie, $conn) {
    $sql = "
        SELECT 
            e.id_etu AS etudiant_id,
            e.num_etu AS num_etu,
            e.nom,
            e.prenoms,
            lg.statut AS statut_etudiant
        FROM 
            codif_lit l
        RIGHT JOIN 
            codif_affectation a ON l.id_lit = a.id_lit
        RIGHT JOIN 
            codif_etudiant e ON a.id_etu = e.id_etu
        LEFT JOIN 
            codif_loger lg ON lg.id_etu = e.id_etu
        WHERE 
            l.lit = ?
            OR lg.id_paie IN (
                SELECT id_paie
                FROM codif_loger
                WHERE id_paie = ?
                  AND statut = 'Titulaire'
            )
        ORDER BY 
            FIELD(lg.statut, 'Titulaire', 'Suppleant', 'Clando');
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $lit, $paie); // `s` pour une chaîne de caractères
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

