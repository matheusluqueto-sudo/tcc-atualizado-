<?php
$videos = [
    ['id' => 'y1fcizXBEZ0', 'title' => 'Vídeo 01'],
    ['id' => 'deDoYvqOMHE', 'title' => 'Vídeo 02'],
    ['id' => 'eGjZlTE55c8', 'title' => 'Vídeo 03'],
    ['id' => 'ng4v6ilbyCo', 'title' => 'Vídeo 04'],
    ['id' => '8X-gIXkRVeA', 'title' => 'Vídeo 05'],
];
?>
<section class="awareness-banner">
    <div class="awareness-banner-copy">
        <span class="eyebrow">CONHECIMENTO QUE PROTEGE</span>
        <h2>Segurança começa com informação.</h2>
        <p>Confira os materiais de conscientização e fortaleça os cuidados no dia a dia. Pequenas atitudes fazem a diferença para proteger você e toda a equipe.</p>
    </div>
    <div class="awareness-total" aria-label="Cinco vídeos disponíveis">
        <strong><?= count($videos) ?></strong>
        <span>vídeos disponíveis</span>
    </div>
</section>

<section class="awareness-section" aria-labelledby="awareness-videos-title">
    <div class="section-heading awareness-section-heading">
        <div>
            <h2 id="awareness-videos-title">Biblioteca de vídeos</h2>
            <p>Assista aos conteúdos diretamente nesta página.</p>
        </div>
        <span class="badge info">Conteúdo educativo</span>
    </div>

    <div class="awareness-grid">
        <?php foreach ($videos as $index => $video): ?>
            <article class="video-card">
                <div class="video-card-heading">
                    <span class="video-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div>
                        <h3><?= e($video['title']) ?></h3>
                        <span>Conscientização e segurança no trabalho</span>
                    </div>
                </div>
                <div class="video-frame">
                    <iframe
                        src="https://www.youtube-nocookie.com/embed/<?= e($video['id']) ?>"
                        title="<?= e($video['title']) ?> — Conscientização e segurança no trabalho"
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>
                </div>
                <div class="video-card-footer">
                    <span>Material de conscientização</span>
                    <a href="https://www.youtube.com/watch?v=<?= e($video['id']) ?>" target="_blank" rel="noopener noreferrer">Assistir no YouTube ↗</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
