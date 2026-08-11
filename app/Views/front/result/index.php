<?= $this->extend('front/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
<div class="col-lg-9">

    <!-- Result Banner -->
    <div class="card mb-4 border-0 <?= $passed ? 'bg-success' : 'bg-danger' ?> text-white text-center p-4">
        <div style="font-size:4rem;"><?= $passed ? '🎉' : '😔' ?></div>
        <h3 class="fw-bold mb-1"><?= $passed ? 'Congratulations! You Passed!' : 'Better Luck Next Time' ?></h3>
        <p class="mb-0 opacity-75"><?= esc($attempt['quiz_title']) ?></p>
    </div>

    <!-- Score Summary -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="fs-2 fw-bold text-primary"><?= count($answers) ?></div>
                <div class="text-muted small">Total Questions</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="fs-2 fw-bold text-success"><?= array_sum(array_column($answers, 'is_correct') === '1' ? array_column($answers, 'is_correct') : []) ?>
                    <?php
                    $correctCount = 0;
                    $wrongCount   = 0;
                    foreach ($answers as $a) {
                        if ($a['is_correct'] === '1') $correctCount++;
                        else $wrongCount++;
                    }
                    echo $correctCount;
                    ?>
                </div>
                <div class="text-muted small">Correct</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="fs-2 fw-bold text-danger"><?= $wrongCount ?></div>
                <div class="text-muted small">Wrong</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="fs-2 fw-bold text-info"><?= $attempt['percentage'] ?>%</div>
                <div class="text-muted small">Score</div>
            </div>
        </div>
    </div>

    <!-- Detail row -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-2 text-center">
                <div class="col-md-4">
                    <div class="text-muted small">Marks Obtained</div>
                    <div class="fw-bold"><?= $attempt['score'] ?> / <?= $attempt['total_marks'] ?></div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Passing Percentage</div>
                    <div class="fw-bold"><?= $quiz['passing_percentage'] ?>%</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Time Taken</div>
                    <div class="fw-bold">
                        <?php
                        if ($attempt['started_at'] && $attempt['completed_at']) {
                            $diff = strtotime($attempt['completed_at']) - strtotime($attempt['started_at']);
                            echo floor($diff / 60) . 'm ' . ($diff % 60) . 's';
                        } else {
                            echo '—';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Answer Analysis -->
    <div class="card">
        <div class="card-header bg-white border-0 pt-3">
            <h6 class="fw-semibold mb-0">Answer Analysis</h6>
        </div>
        <div class="card-body p-0">
            <?php foreach ($answers as $i => $ans): ?>
            <div class="border-bottom p-3 <?= $ans['is_correct'] === '1' ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' ?>">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-semibold small">Q<?= $i + 1 ?>. <?= esc($ans['question_text']) ?></span>
                    <span class="ms-3 flex-shrink-0">
                        <?php if ($ans['is_correct'] === '1'): ?>
                            <span class="badge bg-success">+<?= $ans['marks_obtained'] ?> mark<?= $ans['marks_obtained'] > 1 ? 's' : '' ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger">0 marks</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="row g-2 small">
                    <div class="col-md-6">
                        <span class="text-muted">Your answer: </span>
                        <span class="<?= $ans['is_correct'] === '1' ? 'text-success fw-semibold' : 'text-danger fw-semibold' ?>">
                            <?= $ans['selected_option_text'] ? esc($ans['selected_option_text']) : '<em>Not answered</em>' ?>
                        </span>
                    </div>
                    <?php if ($ans['is_correct'] !== '1'): ?>
                    <div class="col-md-6">
                        <span class="text-muted">Correct answer: </span>
                        <span class="text-success fw-semibold"><?= esc($ans['correct_option_text'] ?? '—') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-2 mt-4 justify-content-center">
        <a href="<?= site_url('user/quizzes') ?>" class="btn btn-primary"><i class="bi bi-journals me-1"></i>Browse More Quizzes</a>
        <a href="<?= site_url('user/attempts') ?>" class="btn btn-outline-secondary"><i class="bi bi-bar-chart me-1"></i>My Attempts</a>
    </div>

</div>
</div>

<?= $this->endSection() ?>
