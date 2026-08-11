<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    // -------------------------------------------------------
    // TABLE NAME CONSTANTS
    // -------------------------------------------------------
    private const TBL_ADMIN   = 'tbl_admin_users';
    private const TBL_USERS   = 'tbl_user_data';
    private const TBL_CATS    = 'tbl_categories';
    private const TBL_QUIZZES = 'tbl_quizzes';
    private const TBL_QUEST   = 'tbl_questions';
    private const TBL_OPTIONS = 'tbl_options';
    private const TBL_ATTEMPT = 'tbl_quiz_attempts';
    private const TBL_ANSWERS = 'tbl_attempt_answers';

    // -------------------------------------------------------
    // SECTION 1: ADMIN AUTH
    // -------------------------------------------------------

    /**
     * Find an admin record by email address.
     */
    public function findAdminByEmail(string $email): ?array
    {
        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->where('email', $email);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Find an admin record by primary key.
     */
    public function findAdminById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->where('id', $id);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Stamp the last_login datetime for an admin.
     */
    public function updateLastLogin(int $id): bool
    {
        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->where('id', $id);
        return $builder->update(['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Update admin profile fields (name, email).
     */
    public function updateAdminProfile(int $id, array $data): bool
    {
        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->where('id', $id);
        return $builder->update($data);
    }

    /**
     * Update the admin's hashed password.
     */
    public function updateAdminPassword(int $id, string $hashedPassword): bool
    {
        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->where('id', $id);
        return $builder->update(['password' => $hashedPassword]);
    }

    // -------------------------------------------------------
    // SECTION 2: ADMIN USER MANAGEMENT (super_admin only)
    // -------------------------------------------------------

    /**
     * Paginated list of all admin users with optional search.
     */
    public function getAdminUsers(string $search = '', int $perPage = 10): array
    {
        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->select('id, name, email, password, role, status, last_login, created_on');
        $builder->where('is_deleted', '0');

        if ($search !== '') {
            $builder->groupStart();
            $builder->like('name', $search);
            $builder->orLike('email', $search);
            $builder->groupEnd();
        }

        $builder->orderBy('created_on', 'DESC');

        $total = $builder->countAllResults(false);

        $pager  = \Config\Services::pager();
        $page   = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $perPage;
        $pager->makeLinks($page, $perPage, $total);

        $builder->limit($perPage, $offset);
        $query = $builder->get();

        return [
            'data'  => $query->getResultArray(),
            'pager' => $pager,
            'total' => $total,
        ];
    }

    /**
     * Fetch a single admin user by ID.
     */
    public function getAdminUserById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->where('id', $id);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Create a new admin user with bcrypt password.
     * role: 'admin' or 'super_admin'
     */
    public function createAdminUser(array $data): int
    {
        $data['password']   = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['is_deleted'] = '0';
        $data['status']     = $data['status'] ?? '1';
        $data['role']       = $data['role'] ?? 'admin';

        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->insert($data);
        return (int) $this->db->insertID();
    }

    /**
     * Update an admin user. Password re-hashed only when provided.
     */
    public function updateAdminUser(int $id, array $data): bool
    {
        $builder = $this->db->table(self::TBL_ADMIN);
        $builder->where('id', $id);
        return $builder->update($data);
    }

    /**
     * Toggle an admin user's status between '0' and '1'.
     */
    public function toggleAdminUserStatus(int $id): bool
    {
        $user = $this->getAdminUserById($id);
        if (! $user) {
            return false;
        }
        $newStatus = ($user['status'] === '1') ? '0' : '1';
        $builder   = $this->db->table(self::TBL_ADMIN);
        $builder->where('id', $id);
        return $builder->update(['status' => $newStatus]);
    }

    // -------------------------------------------------------
    // SECTION 3: FRONT USER MANAGEMENT (tbl_user_data — read only stats)
    // -------------------------------------------------------

    /**
     * Paginated list of front-end users with optional search.
     */
    public function getUsers(string $search = '', int $perPage = 10): array
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->select('id, name, email, password, status, last_login, created_on');
        $builder->where('is_deleted', '0');

        if ($search !== '') {
            $builder->groupStart();
            $builder->like('name', $search);
            $builder->orLike('email', $search);
            $builder->groupEnd();
        }

        $builder->orderBy('created_on', 'DESC');

        $total = $builder->countAllResults(false);

        $pager   = \Config\Services::pager();
        $page    = (int) ($_GET['page'] ?? 1);
        $offset  = ($page - 1) * $perPage;
        $pager->makeLinks($page, $perPage, $total);

        $builder->limit($perPage, $offset);
        $query = $builder->get();

        return [
            'data'  => $query->getResultArray(),
            'pager' => $pager,
            'total' => $total,
        ];
    }

    /**
     * Fetch a single user by ID.
     */
    public function getUserById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->where('id', $id);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Create a new user with a bcrypt-hashed password.
     * Returns the new record's ID.
     */
    public function createUser(array $data): int
    {
        $data['password']   = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['is_deleted'] = '0';
        $data['status']     = $data['status'] ?? '1';

        $builder = $this->db->table(self::TBL_USERS);
        $builder->insert($data);
        return (int) $this->db->insertID();
    }

    /**
     * Update user profile fields.
     */
    public function updateUser(int $id, array $data): bool
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->where('id', $id);
        return $builder->update($data);
    }

    /**
     * Toggle a user's status between '0' and '1'.
     */
    public function toggleUserStatus(int $id): bool
    {
        $user = $this->getUserById($id);
        if (! $user) {
            return false;
        }
        $newStatus = ($user['status'] === '1') ? '0' : '1';
        $builder   = $this->db->table(self::TBL_USERS);
        $builder->where('id', $id);
        return $builder->update(['status' => $newStatus]);
    }

    // -------------------------------------------------------
    // SECTION 4: CATEGORY MANAGEMENT
    // -------------------------------------------------------

    /**
     * Paginated category list with quiz_count subquery and optional search.
     */
    public function getCategories(string $search = '', int $perPage = 10): array
    {
        $builder = $this->db->table(self::TBL_CATS . ' c');
        $builder->select('c.*, (SELECT COUNT(*) FROM ' . self::TBL_QUIZZES . ' q WHERE q.category_id = c.id AND q.is_deleted = \'0\') AS quiz_count');
        $builder->where('c.is_deleted', '0');

        if ($search !== '') {
            $builder->like('c.name', $search);
        }

        $builder->orderBy('c.created_on', 'DESC');

        $total = $builder->countAllResults(false);

        $pager  = \Config\Services::pager();
        $page   = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $perPage;
        $pager->makeLinks($page, $perPage, $total);

        $builder->limit($perPage, $offset);
        $query = $builder->get();

        return [
            'data'  => $query->getResultArray(),
            'pager' => $pager,
            'total' => $total,
        ];
    }

    /**
     * All active, non-deleted categories (for dropdowns).
     */
    public function getActiveCategories(): array
    {
        $builder = $this->db->table(self::TBL_CATS);
        $builder->select('id, name');
        $builder->where('status', '1');
        $builder->where('is_deleted', '0');
        $builder->orderBy('name', 'ASC');
        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Fetch a single category by ID.
     */
    public function getCategoryById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_CATS);
        $builder->where('id', $id);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Create a new category. Returns the new ID.
     */
    public function createCategory(array $data): int
    {
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['is_deleted'] = '0';
        $data['status']     = $data['status'] ?? '1';

        $builder = $this->db->table(self::TBL_CATS);
        $builder->insert($data);
        return (int) $this->db->insertID();
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(int $id, array $data): bool
    {
        $builder = $this->db->table(self::TBL_CATS);
        $builder->where('id', $id);
        return $builder->update($data);
    }

    /**
     * Check whether a category name already exists (optionally excluding an ID for edit).
     */
    public function categoryNameExists(string $name, int $excludeId = 0): bool
    {
        $builder = $this->db->table(self::TBL_CATS);
        $builder->where('name', $name);
        $builder->where('is_deleted', '0');
        if ($excludeId > 0) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Toggle a category's status between '0' and '1'.
     */
    public function toggleCategoryStatus(int $id): bool
    {
        $cat = $this->getCategoryById($id);
        if (! $cat) {
            return false;
        }
        $newStatus = ($cat['status'] === '1') ? '0' : '1';
        $builder   = $this->db->table(self::TBL_CATS);
        $builder->where('id', $id);
        return $builder->update(['status' => $newStatus]);
    }

    // -------------------------------------------------------
    // SECTION 4: QUIZ MANAGEMENT
    // -------------------------------------------------------

    /**
     * Paginated quiz list joined with categories and admin users.
     */
    public function getQuizzes(string $search = '', int $categoryId = 0, string $status = '', int $perPage = 10): array
    {
        $builder = $this->db->table(self::TBL_QUIZZES . ' q');
        $builder->select('q.*, c.name AS category_name, a.name AS created_by_name');
        $builder->join(self::TBL_CATS    . ' c', 'c.id = q.category_id', 'left');
        $builder->join(self::TBL_ADMIN   . ' a', 'a.id = q.created_by',  'left');
        $builder->where('q.is_deleted', '0');

        if ($search !== '') {
            $builder->groupStart();
            $builder->like('q.title', $search);
            $builder->orLike('q.description', $search);
            $builder->groupEnd();
        }

        if ($categoryId > 0) {
            $builder->where('q.category_id', $categoryId);
        }

        if ($status !== '') {
            $builder->where('q.status', $status);
        }

        $builder->orderBy('q.created_on', 'DESC');

        $total = $builder->countAllResults(false);

        $pager  = \Config\Services::pager();
        $page   = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $perPage;
        $pager->makeLinks($page, $perPage, $total);

        $builder->limit($perPage, $offset);
        $query = $builder->get();

        return [
            'data'  => $query->getResultArray(),
            'pager' => $pager,
            'total' => $total,
        ];
    }

    /**
     * Fetch a single quiz by ID, joined with category and creator.
     */
    public function getQuizById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_QUIZZES . ' q');
        $builder->select('q.*, c.name AS category_name, a.name AS created_by_name');
        $builder->join(self::TBL_CATS  . ' c', 'c.id = q.category_id', 'left');
        $builder->join(self::TBL_ADMIN . ' a', 'a.id = q.created_by',  'left');
        $builder->where('q.id', $id);
        $builder->where('q.is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Create a new quiz. Returns the new ID.
     */
    public function createQuiz(array $data): int
    {
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['is_deleted'] = '0';
        $data['status']     = $data['status'] ?? '1';

        $builder = $this->db->table(self::TBL_QUIZZES);
        $builder->insert($data);
        return (int) $this->db->insertID();
    }

    /**
     * Update an existing quiz.
     */
    public function updateQuiz(int $id, array $data): bool
    {
        $builder = $this->db->table(self::TBL_QUIZZES);
        $builder->where('id', $id);
        return $builder->update($data);
    }

    /**
     * Toggle a quiz's status between '0' and '1'.
     */
    public function toggleQuizStatus(int $id): bool
    {
        $quiz = $this->getQuizById($id);
        if (! $quiz) {
            return false;
        }
        $newStatus = ($quiz['status'] === '1') ? '0' : '1';
        $builder   = $this->db->table(self::TBL_QUIZZES);
        $builder->where('id', $id);
        return $builder->update(['status' => $newStatus]);
    }

    /**
     * Re-sync total_questions for a quiz from the live question count.
     */
    public function syncQuizQuestionCount(int $quizId): bool
    {
        $builder = $this->db->table(self::TBL_QUEST);
        $builder->where('quiz_id', $quizId);
        $builder->where('is_deleted', '0');
        $count = $builder->countAllResults();

        $updater = $this->db->table(self::TBL_QUIZZES);
        $updater->where('id', $quizId);
        return $updater->update(['total_questions' => $count]);
    }

    // -------------------------------------------------------
    // SECTION 5: QUESTION MANAGEMENT
    // -------------------------------------------------------

    /**
     * Paginated question list for a quiz with option_count subquery.
     */
    public function getQuestions(int $quizId, string $search = '', int $perPage = 10): array
    {
        $builder = $this->db->table(self::TBL_QUEST . ' q');
        $builder->select('q.*, (SELECT COUNT(*) FROM ' . self::TBL_OPTIONS . ' o WHERE o.question_id = q.id AND o.is_deleted = \'0\') AS option_count');
        $builder->where('q.quiz_id', $quizId);
        $builder->where('q.is_deleted', '0');

        if ($search !== '') {
            $builder->like('q.question_text', $search);
        }

        $builder->orderBy('q.created_on', 'ASC');

        $total = $builder->countAllResults(false);

        $pager  = \Config\Services::pager();
        $page   = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $perPage;
        $pager->makeLinks($page, $perPage, $total);

        $builder->limit($perPage, $offset);
        $query = $builder->get();

        return [
            'data'  => $query->getResultArray(),
            'pager' => $pager,
            'total' => $total,
        ];
    }

    /**
     * Fetch a single question by ID.
     */
    public function getQuestionById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_QUEST);
        $builder->where('id', $id);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Create a question along with its options inside a transaction.
     * $options = [['option_text' => '...', 'is_correct' => '0'|'1'], ...]
     * Returns the new question ID, or 0 on failure.
     */
    public function createQuestion(array $questionData, array $options): int
    {
        $this->db->transStart();

        $questionData['created_on'] = date('Y-m-d H:i:s');
        $questionData['is_deleted'] = '0';
        $questionData['status']     = $questionData['status'] ?? '1';

        $qBuilder = $this->db->table(self::TBL_QUEST);
        $qBuilder->insert($questionData);
        $questionId = (int) $this->db->insertID();

        if ($questionId > 0 && ! empty($options)) {
            $now         = date('Y-m-d H:i:s');
            $optionBatch = [];
            foreach ($options as $opt) {
                $optionBatch[] = [
                    'question_id' => $questionId,
                    'option_text' => $opt['option_text'],
                    'is_correct'  => $opt['is_correct'] ?? '0',
                    'is_deleted'  => '0',
                    'created_on'  => $now,
                ];
            }
            $oBuilder = $this->db->table(self::TBL_OPTIONS);
            $oBuilder->insertBatch($optionBatch);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return 0;
        }

        return $questionId;
    }

    /**
     * Update a question and replace all its options inside a transaction.
     */
    public function updateQuestion(int $id, array $questionData, array $options): bool
    {
        $this->db->transStart();

        $qBuilder = $this->db->table(self::TBL_QUEST);
        $qBuilder->where('id', $id);
        $qBuilder->update($questionData);

        // Soft-delete existing options
        $delBuilder = $this->db->table(self::TBL_OPTIONS);
        $delBuilder->where('question_id', $id);
        $delBuilder->update(['is_deleted' => '1']);

        // Re-insert fresh options
        if (! empty($options)) {
            $now         = date('Y-m-d H:i:s');
            $optionBatch = [];
            foreach ($options as $opt) {
                $optionBatch[] = [
                    'question_id' => $id,
                    'option_text' => $opt['option_text'],
                    'is_correct'  => $opt['is_correct'] ?? '0',
                    'is_deleted'  => '0',
                    'created_on'  => $now,
                ];
            }
            $oBuilder = $this->db->table(self::TBL_OPTIONS);
            $oBuilder->insertBatch($optionBatch);
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }

    /**
     * Toggle a question's status between '0' and '1'.
     */
    public function toggleQuestionStatus(int $id): bool
    {
        $question = $this->getQuestionById($id);
        if (! $question) {
            return false;
        }
        $newStatus = ($question['status'] === '1') ? '0' : '1';
        $builder   = $this->db->table(self::TBL_QUEST);
        $builder->where('id', $id);
        return $builder->update(['status' => $newStatus]);
    }

    // -------------------------------------------------------
    // SECTION 6: OPTION MANAGEMENT
    // -------------------------------------------------------

    /**
     * All non-deleted options for a given question.
     */
    public function getOptionsByQuestion(int $questionId): array
    {
        $builder = $this->db->table(self::TBL_OPTIONS);
        $builder->where('question_id', $questionId);
        $builder->where('is_deleted', '0');
        $builder->orderBy('id', 'ASC');
        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * The correct option row for a given question.
     */
    public function getCorrectOption(int $questionId): ?array
    {
        $builder = $this->db->table(self::TBL_OPTIONS);
        $builder->where('question_id', $questionId);
        $builder->where('is_correct', '1');
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    // -------------------------------------------------------
    // SECTION 7: ATTEMPT MANAGEMENT
    // -------------------------------------------------------

    /**
     * Paginated list of all quiz attempts joined with quizzes and users.
     */
    public function getAllAttempts(string $search = '', int $perPage = 10): array
    {
        $builder = $this->db->table(self::TBL_ATTEMPT . ' a');
        $builder->select('a.*, q.title AS quiz_title, u.name AS user_name, u.email AS user_email');
        $builder->join(self::TBL_QUIZZES . ' q', 'q.id = a.quiz_id',  'left');
        $builder->join(self::TBL_USERS   . ' u', 'u.id = a.user_id',  'left');
        $builder->where('a.is_deleted', '0');

        if ($search !== '') {
            $builder->groupStart();
            $builder->like('u.name', $search);
            $builder->orLike('u.email', $search);
            $builder->orLike('q.title', $search);
            $builder->groupEnd();
        }

        $builder->orderBy('a.created_on', 'DESC');

        $total = $builder->countAllResults(false);

        $pager  = \Config\Services::pager();
        $page   = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $perPage;
        $pager->makeLinks($page, $perPage, $total);

        $builder->limit($perPage, $offset);
        $query = $builder->get();

        return [
            'data'  => $query->getResultArray(),
            'pager' => $pager,
            'total' => $total,
        ];
    }

    /**
     * Single attempt row with full joins (quiz, user).
     */
    public function getAttemptById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_ATTEMPT . ' a');
        $builder->select('a.*, q.title AS quiz_title, q.duration, q.passing_percentage, u.name AS user_name, u.email AS user_email');
        $builder->join(self::TBL_QUIZZES . ' q', 'q.id = a.quiz_id', 'left');
        $builder->join(self::TBL_USERS   . ' u', 'u.id = a.user_id', 'left');
        $builder->where('a.id', $id);
        $builder->where('a.is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Answer rows for an attempt, joined with question text,
     * selected option text, and correct option text.
     */
    public function getAttemptAnswers(int $attemptId): array
    {
        $builder = $this->db->table(self::TBL_ANSWERS . ' aa');
        $builder->select('aa.*, qst.question_text, sel.option_text AS selected_option_text, cor.option_text AS correct_option_text');
        $builder->join(self::TBL_QUEST   . ' qst', 'qst.id = aa.question_id',        'left');
        $builder->join(self::TBL_OPTIONS . ' sel', 'sel.id = aa.selected_option_id',  'left');
        $builder->join(self::TBL_OPTIONS . ' cor', 'cor.id = aa.correct_option_id',   'left');
        $builder->where('aa.attempt_id', $attemptId);
        $builder->where('aa.is_deleted', '0');
        $builder->orderBy('aa.id', 'ASC');
        $query = $builder->get();
        return $query->getResultArray();
    }

    // -------------------------------------------------------
    // SECTION 8: DASHBOARD STATS
    // -------------------------------------------------------

    /**
     * Returns an array of aggregate counts for the dashboard.
     */
    public function getDashboardStats(): array
    {
        $uBuilder = $this->db->table(self::TBL_USERS);
        $uBuilder->where('is_deleted', '0');
        $totalUsers = $uBuilder->countAllResults();

        $qBuilder = $this->db->table(self::TBL_QUIZZES);
        $qBuilder->where('is_deleted', '0');
        $totalQuizzes = $qBuilder->countAllResults();

        $cBuilder = $this->db->table(self::TBL_CATS);
        $cBuilder->where('is_deleted', '0');
        $totalCategories = $cBuilder->countAllResults();

        $aBuilder = $this->db->table(self::TBL_ATTEMPT);
        $aBuilder->where('is_deleted', '0');
        $totalAttempts = $aBuilder->countAllResults();

        $passBuilder = $this->db->table(self::TBL_ATTEMPT . ' a');
        $passBuilder->join(self::TBL_QUIZZES . ' q', 'q.id = a.quiz_id', 'left');
        $passBuilder->where('a.is_deleted', '0');
        $passBuilder->where('a.status', '1');
        $passBuilder->where('a.percentage >= q.passing_percentage', null, false);
        $totalPassed = $passBuilder->countAllResults();

        return [
            'total_users'      => $totalUsers,
            'total_quizzes'    => $totalQuizzes,
            'total_categories' => $totalCategories,
            'total_attempts'   => $totalAttempts,
            'total_passed'     => $totalPassed,
        ];
    }

    /**
     * Most recent N attempts with quiz and user info.
     */
    public function getRecentAttempts(int $limit = 5): array
    {
        $builder = $this->db->table(self::TBL_ATTEMPT . ' a');
        $builder->select('a.*, q.title AS quiz_title, u.name AS user_name');
        $builder->join(self::TBL_QUIZZES . ' q', 'q.id = a.quiz_id', 'left');
        $builder->join(self::TBL_USERS   . ' u', 'u.id = a.user_id', 'left');
        $builder->where('a.is_deleted', '0');
        $builder->orderBy('a.created_on', 'DESC');
        $builder->limit($limit);
        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Most recently registered N users.
     */
    public function getRecentUsers(int $limit = 5): array
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->select('id, name, email, status, created_on');
        $builder->where('is_deleted', '0');
        $builder->orderBy('created_on', 'DESC');
        $builder->limit($limit);
        $query = $builder->get();
        return $query->getResultArray();
    }
}
