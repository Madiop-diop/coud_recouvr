<?php
include('fonction.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_paie'])  && isset($_GET['lit'])) {
    $paie = $_GET['id_paie'] ?? null;
    $lit = $_GET['lit'] ?? null;
}

$occupants = getEtudiantByLit($lit,$paie, $conn); ;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <title>GESCOUD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/base.css" />
    <link rel="stylesheet" href="assets/css/vendor.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="assets/js/modernizr.js"></script>
    <script src="assets/js/pace.min.js"></script>
    <style>
    td,
    th,
    tr {
        font-size: 15px;
        text-align: center;
        vertical-align: middle;
    }
    </style>
</head>

<body>
    <header class="s-header">
        <div class="header-logo">
            <a class="site-logo" href="#"><img src="/codif(1)/assets/images/logo.png" alt="Homepage" /></a>
            Centre des Oeuvres universitaires de Dakar
        </div>
    </header>
    <section id="homedesigne" class="s-homedesigne">
        <p class="lead">Bienvenue dans l'espace de connexion !</p>
    </section>
    <div class="container-fluid">
        <center>
            <h3> GESTION DES RECOUVREMENTS</h2><br>
                <h2> Occupant du Lit <?= htmlspecialchars($lit) ?></h2>
                <br><br>
        </center>
        <center>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Num Étudiant</th>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($occupants)) : ?>
                    <?php foreach ($occupants as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['num_etu']) ?></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['prenoms']) ?></td>
                        <td><?= htmlspecialchars($row['statut_etudiant']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="5">Aucun étudiant trouvé pour ce pavillon.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <br><br>
            <button class="btn btn-success" onclick="goBack()">Retour</button>

            <script>
            function goBack() {
                window.history.back();
            }
            </script>
        </center>
    </div>
    <?php include('footer.php'); ?>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>