<?php
include('fonction.php');
$pavillon = "B1";

$data = getTitulaireByPavillon($pavillon, $conn);


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
            <h2> GESTION DES RECOUVREMENTS</h2>
            <h2> PAVILLON : <?= htmlspecialchars($pavillon) ?></h2>
        </center>
        <center>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Chambre</th>
                        <th scope="col">Lit</th>
                        <th scope="col">Num Etudiant</th>
                        <th scope="col">Nom Titulaire</th>
                        <th scope="col">Voisins</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)) : ?>
                    <?php foreach ($data as $index => $row) : ?>

                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['chambre']) ?></td>
                        <td><?= htmlspecialchars($row['lit']) ?></td>
                        <td><?= htmlspecialchars($row['num_etu']) ?></td>
                        <td><?= htmlspecialchars($row['titulaire_nom']) ?></td>
                        <td>
                            <form action="lit.php" method="GET">
                                <input type="hidden" name="id_paie"
                                    value="<?= htmlspecialchars($row['id_paie'], ENT_QUOTES) ?>">
                                <input type="hidden" name="lit"
                                    value="<?= htmlspecialchars($row['lit'], ENT_QUOTES) ?>">
                                <button type="submit" class="btn btn-info">Voir détails</button>
                            </form>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="6">Aucun étudiant trouvé pour ce pavillon.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </center>
    </div>
    <?php include('footer.php'); ?>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>