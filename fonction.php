<?php

// Connectez-vous à votre base de données MySQL
function connexionBD()
{
    $connexion = mysqli_connect("localhost", "root", "", "bdcodif");
    // Vérifiez la connexion
    if ($connexion === false) {
        die("Erreur : Impossible de se connecter. " . mysqli_connect_error());
    }
    return $connexion;
}
$conn = connexionBD();

function dateFormat($date) {
    return date('Y-m-d', strtotime($date));
}

/* ********************************************************************************* 
Fonction pour calculer le nombre de mois total à payer par l'etudiant
********************************************************************************* */
function getNbreMois($numEtudiant)
{
    $dateDepart = getAllDelai("depart", info($numEtudiant)[5]);
	if($dateDepart != NULL){
    $date_debut = DateTime::createFromFormat('Y-m-d', dateFormat($dateDepart['data_limite']));
    $date_sys = DateTime::createFromFormat('Y-m-d', dateFormat(date("Y-n-j")));
    $nbr_mois = $date_debut->diff($date_sys);
    $nbr_mois = $nbr_mois->format('%m');
    return $nbr_mois;}
}



function getAllDelai($nature, $faculte)
{
    global $conn;
    $requete =  "SELECT * FROM codif_delai where nature ='$nature' AND faculte ='$faculte'";
    $resultRequete = mysqli_query($conn, $requete);
    return $resultRequete->fetch_assoc();
}




//Fonction permettant de recuperer toustes les infos de la table etudiant


function info($login)
{
    //Recherche des infos de l'etudiant
    global $conn;
    $rr = "select * from codif_etudiant where num_etu='$login'";
    $ee = mysqli_query($conn, $rr);
    $ss = mysqli_fetch_array($ee);

    $numIdentite = $ss['numIdentite'];
    $dateNaissance = $ss['dateNaissance'];
    $lieuNaissance = $ss['lieuNaissance'];
    $nom = $ss['nom'];
    $prenoms = $ss['prenoms'];
    $etablissement = $ss['etablissement'];
    $departement = $ss['departement'];
    $typeEtudiant = $ss['typeEtudiant'];
    $sessionId = $ss['sessionId'];
    $niveauFormation = $ss['niveauFormation'];
    $moyenne = $ss['moyenne'];
    $sexe = $ss['sexe'];
    $email = $ss['email_ucad'];
    $email2 = $ss['email_perso'];
	$id_etu = $ss['id_etu'];
    //$email="moulaye.camara@ucad.edu.sn";

    ///////////Recuperer le 1er caractere de la cni pour determiner le sexe	
    $sexeL = "";
    if ($sexe == "G" or $sexe == "M") {
        $sexeL = "Garçons";
    }
    if ($sexe == "F") {
        $sexeL = "Filles";
    }
    ////////////Fin

    return array($numIdentite, $dateNaissance, $lieuNaissance, $nom, $prenoms, $etablissement, $departement, $niveauFormation, $moyenne, $typeEtudiant, $sessionId, $sexe, $sexeL, $email, $email2,$id_etu);
    //fin
	
}

// Fonction pour envoyer Rappel et mettre à jour la base de données
function rappel($message, $etudiant_id,$conn) {

    // Mettre à jour l'attribut 'rappel_envoye' pour cet étudiant dans la table 'affectation'
    $query = "UPDATE codif_affectation SET rappel_envoye = NOW() WHERE id_etu = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $etudiant_id); // "i" pour integer
    $stmt->execute();

    // Vérifier si la mise à jour a été effectuée avec succès
    if ($stmt->affected_rows > 0) {
        // Afficher l'alerte en JavaScript
        echo "<script type='text/javascript'>alert('$message');</script>";
    } else {
        echo "<script type='text/javascript'>alert('Erreur lors de la mise à jour de la base de données.');</script>";
    }
}


function getPaymentDetailsByPavillon($pavillonDonne, $conn) {
    $sql = "
   SELECT 
    l.pavillon,
    l.chambre,
    l.lit,
    e.id_etu AS etudiant_id,
    e.num_etu AS num_etu,
    e.nom AS etudiant_nom,
    e.prenoms AS etudiant_prenoms,
    l.indiv AS type_chambre,
    lg.id_log AS log_id,
    lg.id_val AS validation_id,
    lg.id_paie AS paiement_id,
    lg.username_user AS utilisateur,
    a.rappel_envoye,
    lg.datetime_loger AS date_log,
    COALESCE(
        (SELECT SUM(p.montant)
         FROM codif_paiement p
         WHERE p.id_val = v.id_val), 0) AS montant_paye_total
FROM 
    codif_lit l
JOIN 
    codif_affectation a ON l.id_lit = a.id_lit
JOIN 
    codif_etudiant e ON a.id_etu = e.id_etu
JOIN 
    codif_validation v ON a.id_aff = v.id_aff
LEFT JOIN 
    codif_loger lg ON lg.id_etu = e.id_etu  
WHERE 
    (l.pavillon = ? AND lg.statut = 'Attributaire')
GROUP BY 
    l.pavillon, l.chambre, l.lit, e.id_etu, lg.id_log
ORDER BY 
    l.pavillon, l.chambre, l.lit, e.id_etu, lg.datetime_loger DESC;
";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pavillonDonne);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $etudiantId = $row['etudiant_id'];
        $etudiant_num = $row['num_etu'];
        
        // Calculer le nombre de mois pour l'étudiant
        $nombreMois = getNbreMois($etudiant_num);

        // Déterminer le prix du lit en fonction du type de chambre
        $prixLit = ($row['type_chambre'] === 1) ? 4000 : 3000;
        
        // Calculer le montant facturé en fonction du nombre de mois
        $montantFacture = $nombreMois * $prixLit;
        $montantFacture = $montantFacture + 5000; // 5 000 CAUTION

        // Vérifier que le montant payé n'est pas vide
        $montantPaye = isset($row['montant_paye_total']) ? $row['montant_paye_total'] : 0;

        // Calculer le reste à payer
        $resteAPayer = $montantFacture - $montantPaye;

        // Ajouter les informations uniquement si le reste à payer est supérieur à zéro
        //if ($resteAPayer > 0) {
            $data[] = [
                'pavillon' => $row['pavillon'],
                'chambre' => $row['chambre'],
                'lit' => $row['lit'],
                'etudiant_id' => $row['etudiant_id'],
                'etudiant_nom' => $row['etudiant_nom'],
                'etudiant_prenoms' => $row['etudiant_prenoms'],
                'num_etu' => $row['num_etu'],
                'montant_facture' => $montantFacture,
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'log_id' => $row['log_id'],
                'validation_id' => $row['validation_id'],
                'paiement_id' => $row['paiement_id'],
                'utilisateur' => $row['utilisateur'],
                'rappel_envoye'  => $row['rappel_envoye'],
                'date_log' => $row['date_log']
            ];
       // }
    }

    $stmt->close();
    return $data;
}

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
            AND lg.statut = 'Attributaire'
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

function details($id_etu, $conn) {
    // Requête SQL pour récupérer les paiements d'un étudiant en fonction de id_etu
    $sql = "
        SELECT
            e.num_etu AS num_etu,
            e.nom,
            e.prenoms,
            p.dateTime_paie,
            p.montant,
            p.libelle,
            p.id_paie
        FROM 
            codif_paiement p
        JOIN codif_validation v ON v.id_val = p.id_val
        JOIN codif_affectation a ON v.id_aff = a.id_aff
        JOIN codif_etudiant e ON e.id_etu = a.id_etu
        WHERE 
            e.id_etu = ?;  -- Filtrer par l'identifiant de l'étudiant
    ";

    // Préparation de la requête
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        // Gestion des erreurs si la préparation échoue
        die('Erreur dans la préparation de la requête : ' . $conn->error);
    }

    // Lier le paramètre id_etu
    $stmt->bind_param("i", $id_etu);

    // Exécuter la requête
    $stmt->execute();
    $result = $stmt->get_result();

    // Récupérer les résultats
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Fermer la préparation
    $stmt->close();

    // Si aucun résultat n'est trouvé, retourner un message d'information
    if (empty($data)) {
        return "Aucun paiement trouvé pour cet étudiant.";
    }

    return $data;
}

// ############  POUR RECUPERER LES PAVILLONS  #################
function getAllPavillons($conn)
{
    $query = "SELECT DISTINCT pavillon FROM codif_lit";
    $result = mysqli_query($conn, $query);

    // Vérification de la requête
    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
    }

    // Tableau pour stocker les pavillons
    $pavillons = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pavillons[] = $row['pavillon'];
    }

    return $pavillons; // Retourne un tableau des pavillons
}







?>