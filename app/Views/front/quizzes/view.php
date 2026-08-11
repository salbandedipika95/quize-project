<?= $this->extend('front/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
<div class="col-lg-7">

    <a href="<?= site_url('user/quizzes') ?>" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Quizzes
    </a>

    <div class="card">
        <div class="card-body p-4">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= esc($quiz['category_name']) ?></span>
            <h4 class="fw-bold mb-1"><?= esc($quiz['title']) ?></h4>
            <p class="text-muted"><?= esc($quiz['description'] ?? '') ?></p>

            <hr>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3 text-center">
                    <div class="fs-4 fw-bold text-primary"><?= $quiz['total_questions'] ?></div>
                    <div class="text-muted small">Questions</div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="fs-4 fw-bold text-warning"><?= $quiz['duration'] ?></div>
                    <div class="text-muted small">Minutes</div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="fs-4 fw-bold text-success"><?= $quiz['passing_percentage'] ?>%</div>
                    <div class="text-muted small">To Pass</div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="fs-4 fw-bold text-info"><?= $quiz['total_questions'] ?></div>
                    <div class="text-muted small">Total Marks</div>
                </div>
            </div>

            <?php if ($existing_attempt): ?>
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    You have an <strong>in-progress</strong> attempt for this quiz.
                </div>
                <form method="post" action="<?= site_url('user/quizzes/start/' . $quiz['id']) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold">
                        <i class="bi bi-play-fill me-2"></i>Resume Attempt
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Once started, the timer cannot be paused. Make sure you have <strong><?= $quiz['duration'] ?> minutes</strong> available.
                </div>
                <form method="post" action="<?= site_url('user/quizzes/start/' . $quiz['id']) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-play-fill me-2"></i>Start Quiz
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>

<?= $this->endSection() ?>
