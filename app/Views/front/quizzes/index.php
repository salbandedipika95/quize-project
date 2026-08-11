<?= $this->extend('front/layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="page-heading"><i class="bi bi-journals me-2 text-primary"></i>Available Quizzes</h4>

<!-- Search & Filter -->
<div class="search-wrap">
    <form method="get" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control border-start-0 ps-0"
                       placeholder="Search quizzes..." value="<?= esc($search) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="category_id" class="form-select">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category_id ? 'selected' : '' ?>>
                        <?= esc($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn w-100 fw-semibold"
                    style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:10px;">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
        </div>
        <?php if ($search || $category_id): ?>
        <div class="col-md-1">
            <a href="<?= site_url('user/quizzes') ?>" class="btn btn-outline-danger w-100" style="border-radius:10px;">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- Quiz Cards -->
<?php
// Cycle through colour classes for category badges
$catColors = ['cat-0','cat-1','cat-2','cat-3','cat-4','cat-5','cat-6','cat-7'];
// Card accent colors for the top border strip
$cardAccents = [
    'border-top:4px solid #6366f1;',
    'border-top:4px solid #10b981;',
    'border-top:4px solid #f59e0b;',
    'border-top:4px solid #ef4444;',
    'border-top:4px solid #06b6d4;',
    'border-top:4px solid #8b5cf6;',
    'border-top:4px solid #ec4899;',
    'border-top:4px solid #14b8a6;',
];
?>

<div class="row g-4">
<?php if (! empty($quizzes)): ?>
    <?php foreach ($quizzes as $i => $q): ?>
    <?php $colorIdx = $i % 8; ?>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="<?= $cardAccents[$colorIdx] ?>">
            <div class="card-body d-flex flex-column p-4">
                <!-- Category badge -->
                <span class="cat-badge <?= $catColors[$colorIdx] ?>">
                    <?= esc($q['category_name']) ?>
                </span>

                <!-- Title -->
                <h6 class="fw-bold fs-5 mb-2" style="color:#0f172a;">
                    <?= esc($q['title']) ?>
                </h6>

                <!-- Description -->
                <p class="text-muted small mb-3 flex-grow-1" style="line-height:1.5;">
                    <?= esc(substr($q['description'] ?? 'Test your knowledge with this quiz.', 0, 90)) ?>
                    <?= strlen($q['description'] ?? '') > 90 ? '…' : '' ?>
                </p>

                <!-- Meta info -->
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <span class="d-flex align-items-center gap-1 text-muted small">
                        <i class="bi bi-clock text-primary"></i>
                        <strong><?= $q['duration'] ?></strong> min
                    </span>
                    <span class="d-flex align-items-center gap-1 text-muted small">
                        <i class="bi bi-question-circle text-success"></i>
                        <strong><?= $q['total_questions'] ?></strong> Qs
                    </span>
                    <span class="d-flex align-items-center gap-1 text-muted small">
                        <i class="bi bi-bar-chart text-warning"></i>
                        Pass <strong><?= $q['passing_percentage'] ?>%</strong>
                    </span>
                </div>

                <!-- CTA Button -->
                <a href="<?= site_url('user/quizzes/view/' . $q['id']) ?>"
                   class="btn btn-view-quiz w-100">
                    <i class="bi bi-play-fill me-1"></i>View Quiz
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-12">
        <div class="text-center py-5">
            <i class="bi bi-journals" style="font-size:3.5rem;color:#cbd5e1;"></i>
            <p class="text-muted mt-3 mb-0">No quizzes found matching your search.</p>
            <a href="<?= site_url('user/quizzes') ?>" class="btn btn-primary mt-3">Clear Filters</a>
        </div>
    </div>
<?php endif; ?>
</div>

<!-- Pagination -->
<?php if (isset($pager) && $total > 10): ?>
    <div class="mt-4 d-flex justify-content-center">
        <?= $pager->links() ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
