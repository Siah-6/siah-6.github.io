<div class="container">
    <h2 class="fw-bold mb-4 text-center" style="letter-spacing:1px;">All Primary Weapons</h2>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php
        $primaries = [
            "Bulldog", "Guardian", "Phantom", "Vandal", "Marshal", "Operator", "Bucky", "Judge", "Ares", "Odin", "Spectre", "Stinger"
        ];
        foreach ($primaries as $weapon):
            $imgPath = "assests/images/primary/" . strtolower($weapon) . ".jpg";
            if (!file_exists($imgPath)) {
                $imgPath = "assests/images/primary/" . strtolower($weapon) . ".png";
            }
            if (strtolower($weapon) == 'vandal' && !file_exists($imgPath)) {
                $imgPath = "assests/images/primary/vandal.jpg";
            }
        ?>
            <div class="col">
                <div class="weapon-card card h-100 text-center border-0 d-flex align-items-center justify-content-center" style="background: linear-gradient(180deg, #23242b 60%, #181a20 100%);">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center p-0 w-100">
                        <div class="weapon-image-container" style="height:260px; position:relative; overflow:hidden;">
                            <img src="assests/images/valorant-logo.png"
                                 alt="Valorant Logo"
                                 style="position:absolute;z-index:0;opacity:0.13;left:50%;top:50%;transform:translate(-50%,-50%) scale(8);pointer-events:none;filter:blur(2px);width:1000px;height:1000px;">
                            <?php if (file_exists($imgPath)): ?>
                                <img src="<?= $imgPath ?>" alt="<?= $weapon ?>" class="weapon-image" style="max-height:220px;max-width:98%;box-shadow:0 8px 32px #ff465544;border-radius:18px;transition:transform 0.3s,box-shadow 0.3s;position:relative;z-index:1;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center" style="height:180px;width:180px;background:rgba(255,70,85,0.08);border-radius:16px;position:relative;z-index:1;">
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

<style>
.weapon-card {
    background: linear-gradient(180deg, #23242b 60%, #181a20 100%);
    border-radius: 18px;
    transition: transform 0.3s, box-shadow 0.3s;
    box-shadow: 0 2px 12px #00000033;
    overflow: hidden;
}
.weapon-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 8px 32px #ff465577;
}
.weapon-image-container {
    width: 100%;
    height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #181a20 60%, #ff4655 100%);
    border-radius: 18px 18px 0 0;
    margin-bottom: 0.5rem;
    transition: background 0.3s;
    position: relative;
}
.weapon-card:hover .weapon-image-container {
    background: linear-gradient(135deg, #ff4655 0%, #23242b 100%);
}
.weapon-image {
    max-height: 220px;
    max-width: 98%;
    object-fit: contain;
    display: block;
    border-radius: 18px;
    box-shadow: 0 4px 24px #ff465544;
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
    z-index: 1;
}
.weapon-card:hover .weapon-image {
    transform: scale(1.08);
    box-shadow: 0 8px 32px #ff4655cc;
}
.weapon-info {
    margin-top: 0.5rem;
}
.weapon-name {
    font-weight: bold;
    font-size: 1.25rem;
    color: #fff;
    letter-spacing: 0.5px;
    text-shadow: 0 2px 8px #00000044;
}
</style>

