<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3">
    <!-- Add Form -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0">Add Category</h6>
            </div>
            <div class="card-body">
                <form id="formAddCategory">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="catName" class="form-control" placeholder="e.g. PHP" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="catDesc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" id="catStatus" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Category</button>
                </form>
            </div>
        </div>
    </div>

    <!-- List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Categories <span class="badge bg-secondary ms-1"><?= $total ?></span></h6>
                <form method="get" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search..." value="<?= esc($search) ?>">
                    <button class="btn btn-sm btn-outline-secondary">Go</button>
                    <?php if ($search): ?>
                        <a href="<?= site_url('admin/categories') ?>" class="btn btn-sm btn-outline-danger">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Quizzes</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categories as $i => $c): ?>
                        <tr>
                            <td class="text-muted small"><?= $i + 1 ?></td>
                            <td>
                                <?= esc($c['name']) ?><br>
                                <small class="text-muted"><?= esc(substr($c['description'] ?? '', 0, 50)) ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark"><?= $c['quiz_count'] ?></span></td>
                            <td>
                                <span class="badge <?= $c['status'] === '1' ? 'badge-active' : 'badge-inactive' ?> px-2 py-1">
                                    <?= $c['status'] === '1' ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary btn-edit-cat"
                                        data-id="<?= $c['id'] ?>"
                                        data-name="<?= esc($c['name']) ?>"
                                        data-desc="<?= esc($c['description'] ?? '') ?>"
                                        data-status="<?= $c['status'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-<?= $c['status'] === '1' ? 'warning' : 'success' ?> btn-toggle-cat"
                                        data-id="<?= $c['id'] ?>">
                                    <i class="bi bi-<?= $c['status'] === '1' ? 'toggle-on' : 'toggle-off' ?>"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No categories found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (isset($pager)): ?>
                <div class="card-footer bg-white border-0"><?= $pager->links() ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditCategory">
                    <?= csrf_field() ?>
                    <input type="hidden" id="editCatId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" id="editCatName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea id="editCatDesc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select id="editCatStatus" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
const csrfName = '<?= csrf_token() ?>';
let   csrfHash = '<?= csrf_hash() ?>';

const URL_STORE  = '<?= site_url('admin/categories/store') ?>';
const URL_UPDATE = '<?= site_url('admin/categories/update/') ?>';
const URL_TOGGLE = '<?= site_url('admin/categories/toggle/') ?>';

// Add category
$('#formAddCategory').on('submit', function (e) {
    e.preventDefault();
    $.post(URL_STORE, {
        name:        $('#catName').val(),
        description: $('#catDesc').val(),
        status:      $('#catStatus').val(),
        [csrfName]:  csrfHash
    }, function (res) {
        if (res.status) {
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#dc3545' });
        }
    }, 'json');
});

// Open edit modal
$(document).on('click', '.btn-edit-cat', function () {
    $('#editCatId').val($(this).data('id'));
    $('#editCatName').val($(this).data('name'));
    $('#editCatDesc').val($(this).data('desc'));
    $('#editCatStatus').val(String($(this).data('status')));
    new bootstrap.Modal(document.getElementById('editCatModal')).show();
});

// Submit edit
$('#formEditCategory').on('submit', function (e) {
    e.preventDefault();
    const id = $('#editCatId').val();
    $.post(URL_UPDATE + id, {
        name:        $('#editCatName').val(),
        description: $('#editCatDesc').val(),
        status:      $('#editCatStatus').val(),
        [csrfName]:  csrfHash
    }, function (res) {
        if (res.status) {
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#dc3545' });
        }
    }, 'json');
});

// Toggle status
$(document).on('click', '.btn-toggle-cat', function () {
    const id = $(this).data('id');
    $.post(URL_TOGGLE + id, { [csrfName]: csrfHash }, function (res) {
        if (res.status) location.reload();
    }, 'json');
});
</script>
<?= $this->endSection() ?>
