<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header bg-white border-0 pt-3 d-flex align-items-center gap-2">
        <a href="<?= site_url('admin/questions/' . $quiz['id']) ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h6 class="fw-semibold mb-0">
            <?= isset($question) ? 'Edit Question' : 'Add Question' ?> — <?= esc($quiz['title']) ?>
        </h6>
    </div>
    <div class="card-body">
        <form method="post" action="<?= isset($question) ? site_url('admin/questions/update/' . $question['id']) : site_url('admin/questions/store') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                <textarea name="question_text" class="form-control" rows="3" required><?= esc(old('question_text', $question['question_text'] ?? '')) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Marks</label>
                <input type="number" name="marks" class="form-control"
                       value="<?= old('marks', $question['marks'] ?? 1) ?>" min="1" style="width:120px;">
            </div>

            <label class="form-label fw-semibold">
                Options <span class="text-danger">*</span>
                <small class="text-muted fw-normal">(select the radio button next to the correct answer)</small>
            </label>

            <?php
            $opts = $options ?? [
                ['option_text' => '', 'is_correct' => '0'],
                ['option_text' => '', 'is_correct' => '0'],
                ['option_text' => '', 'is_correct' => '0'],
                ['option_text' => '', 'is_correct' => '0'],
            ];
            $correctIdx = 0;
            foreach ($opts as $k => $o) {
                if (($o['is_correct'] ?? '0') === '1') { $correctIdx = $k; break; }
            }
            $labels = ['A', 'B', 'C', 'D'];
            ?>

            <?php foreach ($opts as $k => $o): ?>
            <div class="input-group mb-2">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="radio" name="correct_option"
                           value="<?= $k ?>" <?= $correctIdx === $k ? 'checked' : '' ?> required>
                </div>
                <span class="input-group-text fw-bold"><?= $labels[$k] ?></span>
                <input type="text" name="options[]" class="form-control"
                       placeholder="Option <?= $labels[$k] ?>"
                       value="<?= esc(old('options.' . $k, $o['option_text'] ?? '')) ?>" required>
            </div>
            <?php endforeach; ?>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <?= isset($question) ? 'Update Question' : 'Save Question' ?>
                </button>
                <a href="<?= site_url('admin/questions/' . $quiz['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?= $this->endSection() ?>
