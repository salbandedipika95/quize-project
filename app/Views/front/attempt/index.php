<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($quiz['title']) ?> — Quiz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background:#f0f4f8; }
        .quiz-topbar { background:#1e2a3a; color:#fff; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:100; }
        .timer { font-size:1.5rem; font-weight:700; font-family:monospace; }
        .timer.warning { color:#fbbf24; }
        .timer.danger  { color:#f87171; }
        .question-card { background:#fff; border-radius:14px; padding:28px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
        .option-label { display:block; padding:14px 18px; border:2px solid #e2e8f0; border-radius:10px; cursor:pointer; transition:.15s; margin-bottom:10px; }
        .option-label:hover { border-color:#6366f1; background:#f5f3ff; }
        input[type=radio]:checked + .option-label { border-color:#6366f1; background:#ede9fe; color:#4f46e5; font-weight:600; }
        .q-nav-btn { width:38px; height:38px; border-radius:50%; border:2px solid #e2e8f0; background:#fff; font-size:.85rem; cursor:pointer; }
        .q-nav-btn.answered { background:#6366f1; border-color:#6366f1; color:#fff; }
        .q-nav-btn.current  { border-color:#f59e0b; color:#f59e0b; font-weight:700; }
    </style>
</head>
<body>

<!-- Top bar with timer -->
<div class="quiz-topbar">
    <div>
        <span class="fw-semibold"><?= esc($quiz['title']) ?></span>
        <span class="text-white-50 ms-3 small"><?= count($questions) ?> Questions</span>
    </div>
    <div class="timer" id="timer">--:--</div>
    <form method="post" action="<?= site_url('user/attempt/submit/' . $attempt['id']) ?>" id="submitForm">
        <?= csrf_field() ?>
        <button type="button" class="btn btn-success btn-sm fw-semibold" onclick="confirmSubmit()">
            <i class="bi bi-check-circle me-1"></i>Submit Quiz
        </button>
    </form>
</div>

<div class="container py-4">
<div class="row g-3">

    <!-- Question panel -->
    <div class="col-lg-8">
        <?php foreach ($questions as $i => $q): ?>
        <div class="question-card mb-3 question-block" id="question-<?= $i ?>" style="display:<?= $i === 0 ? 'block' : 'none' ?>;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-primary">Q<?= $i + 1 ?> / <?= count($questions) ?></span>
                <span class="badge bg-light text-dark"><?= $q['marks'] ?> Mark<?= $q['marks'] > 1 ? 's' : '' ?></span>
            </div>
            <p class="fw-semibold fs-5 mb-4"><?= esc($q['question_text']) ?></p>

            <?php foreach ($q['options'] as $opt): ?>
            <div>
                <input type="radio" name="answer_<?= $q['id'] ?>" id="opt_<?= $opt['id'] ?>"
                       value="<?= $opt['id'] ?>"
                       class="d-none option-radio"
                       data-attempt="<?= $attempt['id'] ?>"
                       data-question="<?= $q['id'] ?>"
                       data-qindex="<?= $i ?>"
                       <?= (isset($saved_answers[$q['id']]) && $saved_answers[$q['id']] == $opt['id']) ? 'checked' : '' ?>>
                <label for="opt_<?= $opt['id'] ?>" class="option-label"><?= esc($opt['option_text']) ?></label>
            </div>
            <?php endforeach; ?>

            <!-- Prev / Next -->
            <div class="d-flex justify-content-between mt-4">
                <?php if ($i > 0): ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="showQuestion(<?= $i - 1 ?>)"><i class="bi bi-arrow-left me-1"></i>Previous</button>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>

                <?php if ($i < count($questions) - 1): ?>
                    <button type="button" class="btn btn-primary btn-sm" onclick="showQuestion(<?= $i + 1 ?>)">Next<i class="bi bi-arrow-right ms-1"></i></button>
                <?php else: ?>
                    <button type="button" class="btn btn-success btn-sm fw-semibold" onclick="confirmSubmit()"><i class="bi bi-check-circle me-1"></i>Submit</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Sidebar: question navigator -->
    <div class="col-lg-4">
        <div class="card p-3">
            <h6 class="fw-semibold mb-3">Question Navigator</h6>
            <div class="d-flex flex-wrap gap-2 mb-3" id="qNav">
                <?php foreach ($questions as $i => $q): ?>
                    <button class="q-nav-btn <?= isset($saved_answers[$q['id']]) ? 'answered' : '' ?> <?= $i === 0 ? 'current' : '' ?>"
                            id="nav-<?= $i ?>" onclick="showQuestion(<?= $i ?>)"><?= $i + 1 ?></button>
                <?php endforeach; ?>
            </div>
            <div class="d-flex gap-2 flex-wrap small text-muted">
                <span><span class="q-nav-btn answered d-inline-flex align-items-center justify-content-center" style="width:20px;height:20px;font-size:.7rem;">✓</span> Answered</span>
                <span><span class="q-nav-btn d-inline-flex align-items-center justify-content-center" style="width:20px;height:20px;font-size:.7rem;">?</span> Not answered</span>
            </div>
        </div>
        <div class="card p-3 mt-3 text-center">
            <div class="text-muted small mb-1">Time Remaining</div>
            <div class="timer text-dark" id="timer2">--:--</div>
        </div>
    </div>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// -------------------------------------------------------
// Timer
// -------------------------------------------------------
let remaining = <?= (int) $remaining_seconds ?>;
const timerEls = [document.getElementById('timer'), document.getElementById('timer2')];

function updateTimer() {
    if (remaining <= 0) {
        timerEls.forEach(el => { el.textContent = '00:00'; el.classList.add('danger'); });
        autoSubmit();
        return;
    }
    const m = String(Math.floor(remaining / 60)).padStart(2, '0');
    const s = String(remaining % 60).padStart(2, '0');
    const display = m + ':' + s;
    timerEls.forEach(el => {
        el.textContent = display;
        el.classList.remove('warning', 'danger');
        if (remaining <= 60)       el.classList.add('danger');
        else if (remaining <= 300) el.classList.add('warning');
    });
    remaining--;
}
updateTimer();
const timerInterval = setInterval(updateTimer, 1000);

function autoSubmit() {
    clearInterval(timerInterval);
    document.getElementById('submitForm').submit();
}

// -------------------------------------------------------
// Question navigation
// -------------------------------------------------------
let currentQ = 0;

function showQuestion(index) {
    document.querySelectorAll('.question-block').forEach(el => el.style.display = 'none');
    document.getElementById('question-' + index).style.display = 'block';

    document.querySelectorAll('.q-nav-btn').forEach(btn => btn.classList.remove('current'));
    document.getElementById('nav-' + index).classList.add('current');

    currentQ = index;
}

// -------------------------------------------------------
// AJAX save answer
// -------------------------------------------------------
const csrfName = '<?= csrf_token() ?>';
let   csrfHash = '<?= csrf_hash() ?>';

$(document).on('change', '.option-radio', function () {
    const attemptId  = $(this).data('attempt');
    const questionId = $(this).data('question');
    const qIndex     = $(this).data('qindex');
    const optionId   = $(this).val();

    $.post('<?= site_url('user/attempt/save-answer') ?>', {
        attempt_id:         attemptId,
        question_id:        questionId,
        selected_option_id: optionId,
        [csrfName]:         csrfHash
    }, function (res) {
        if (res.status) {
            const navBtn = document.getElementById('nav-' + qIndex);
            if (navBtn) navBtn.classList.add('answered');
            // refresh CSRF hash from response if present
            if (res.csrf_hash) csrfHash = res.csrf_hash;
        }
        if (res.expired) {
            autoSubmit();
        }
    }, 'json').fail(function () {
        console.warn('Failed to save answer');
    });
});

// -------------------------------------------------------
// Confirm submit
// -------------------------------------------------------
function confirmSubmit() {
    const answered   = document.querySelectorAll('.q-nav-btn.answered').length;
    const total      = <?= count($questions) ?>;
    const unanswered = total - answered;

    const title = unanswered > 0
        ? 'You have ' + unanswered + ' unanswered question(s)!'
        : 'Submit Quiz?';

    const text = unanswered > 0
        ? 'Unanswered questions will be marked as 0. Submit anyway?'
        : 'This cannot be undone.';

    Swal.fire({
        title:              title,
        text:               text,
        icon:               unanswered > 0 ? 'warning' : 'question',
        showCancelButton:   true,
        confirmButtonColor: '#198754',
        cancelButtonColor:  '#6c757d',
        confirmButtonText:  'Yes, Submit',
        cancelButtonText:   'Go Back',
    }).then((result) => {
        if (result.isConfirmed) {
            clearInterval(timerInterval);
            document.getElementById('submitForm').submit();
        }
    });
}
</script>
</body>
</html>
