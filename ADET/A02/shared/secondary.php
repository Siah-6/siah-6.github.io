<div class="container">
    <h2 class="fw-bold mb-4 text-center" style="letter-spacing:1px;">All Secondary Weapons</h2>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php
        $secondaries = [
            "Classic", "Shorty", "Frenzy", "Ghost", "Sheriff"
        ];
        foreach ($secondaries as $weapon):
            $imgPath = "assests/images/secondary/" . strtolower($weapon) . ".jpg";
            if (!file_exists($imgPath)) {
                $imgPath = "assests/images/secondary/" . strtolower($weapon) . ".png";
            }
        ?>
            <div class="col">
                <div class="weapon-card card h-100 text-center border-0 d-flex align-items-center justify-content-center" style="background: linear-gradient(180deg, #23242b 60%, #181a20 100%);">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center p-0 w-100">
                        <div class="weapon-image-container" style="height:260px;">
                            <?php if (file_exists($imgPath)): ?>
                                <img src="<?= $imgPath ?>" alt="<?= $weapon ?>" class="weapon-image" style="max-height:220px;max-width:98%;box-shadow:0 8px 32px #ff465544;border-radius:18px;transition:transform 0.3s,box-shadow 0.3s;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center" style="height:180px;width:180px;background:rgba(255,70,85,0.08);border-radius:16px;">
                                    <i class="bi bi-question-circle" style="font-size:3rem;color:#ff4655;opacity:0.7;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="weapon-info">
                            <span class="weapon-name"><?= $weapon ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
