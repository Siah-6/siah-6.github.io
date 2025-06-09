<?php
$page = "weapons";
if (isset($_GET['page'])) {
    $page = strtolower($_GET['page']);
    switch ($page) {
        case "weapons":
            $page = "weapons";
            break;
        case "primary":
            $page = "primary";
            break;
        case "secondary":
            $page = "secondary";
            break;
        case "melee":
            $page = "melee";
            break;
        default:
            header("Location: ?page=weapons");
            exit;
    }
} else {
    header("Location: ?page=weapons");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Weapons Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assests/style.css">
</head>
<body>
<div class="container-fluid mt-4">
    <div class="row flex-nowrap">
        <div class="col-12 col-md-3 mb-3 mb-md-0">
            <div class="card menu glass shadow p-4 d-flex flex-column align-items-center" style="height: 90vh;">
                <img src="assests/images/valorant-logo.png" alt="Valorant Logo" style="height:165px;" class="mb-3">
                <a href="?page=weapons" class="valorant-button my-2<?= $page == 'weapons' ? ' active' : '' ?>"><i class="bi bi-people-fill me-2"></i>Weapons</a>
                <a href="?page=primary" class="valorant-button my-2<?= $page == 'primary' ? ' active' : '' ?>"><i class="bi bi-bullseye me-2"></i>Primary Weapon</a>
                <a href="?page=secondary" class="valorant-button my-2<?= $page == 'secondary' ? ' active' : '' ?>"><i class="bi bi-shield me-2"></i>Secondary Weapon</a>
                <a href="?page=melee" class="valorant-button my-2<?= $page == 'melee' ? ' active' : '' ?>"><i class="bi bi-lightning me-2"></i>Melee</a>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="card glass parent-content shadow p-4" style="height: 90vh; max-height: 90vh; overflow-y: auto;">
                <?php include("shared/" . $page . ".php"); ?>
            </div>
        </div>
    </div>
</div>
<footer class="bg-dark text-light text-center py-3 mt-auto fixed-bottom">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> Valorant Weapons Hub | 2025</p>
    </div>
</footer>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
