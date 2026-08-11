<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // -------------------------------------------------------
    // TABLE NAME CONSTANTS
    // -------------------------------------------------------
    private const TBL_USERS   = 'tbl_user_data';
    private const TBL_CATS    = 'tbl_categories';
    private const TBL_QUIZZES = 'tbl_quizzes';
    private const TBL_QUEST   = 'tbl_questions';
    private const TBL_OPTIONS = 'tbl_options';
    private const TBL_ATTEMPT = 'tbl_quiz_attempts';
    private const TBL_ANSWERS = 'tbl_attempt_answers';

    // -------------------------------------------------------
    // SECTION 1: AUTH
    // -------------------------------------------------------

    /**
     * Find a user by email address.
     */
    public function findByEmail(string $email): ?array
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->where('email', $email);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Find a user by primary key.
     */
    public function findById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->where('id', $id);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Register a new user with a bcrypt-hashed password.
     * Returns the new user ID, or 0 on failure.
     */
    public function register(array $data): int
    {
        $data['password']   = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['is_deleted'] = '0';
        $data['status']     = '1';

        $builder = $this->db->table(self::TBL_USERS);
        $builder->insert($data);
        return (int) $this->db->insertID();
    }

    /**
     * Verify a plain-text password against the stored hash.
     */
    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    /**
     * Check whether an email is already registered.
     */
    public function emailExists(string $email): bool
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->where('email', $email);
        $builder->where('is_deleted', '0');
        return $builder->countAllResults() > 0;
    }

    /**
     * Stamp the last_login datetime for a user.
     */
    public function updateLastLogin(int $id): bool
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->where('id', $id);
        return $builder->update(['last_login' => date('Y-m-d H:i:s')]);
    }

    // -------------------------------------------------------
    // SECTION 2: PROFILE
    // -------------------------------------------------------

    /**
     * Update user profile fields (name, email, etc.).
     */
    public function updateProfile(int $id, array $data): bool
    {
        $builder = $this->db->table(self::TBL_USERS);
        $builder->where('id', $id);
        return $builder->update($data);
    }

    /**
     * Update the user's password with bcrypt hashing.
     */
    public function updatePassword(int $id, string $newPassword): bool
    {
        $hashed  = password_hash($newPassword, PASSWORD_BCRYPT);
        $builder = $this->db->table(self::TBL_USERS);
        $builder->where('id', $id);
        return $builder->update(['password' => $hashed]);
    }

    // -------------------------------------------------------
    // SECTION 3: QUIZ BROWSING
    // -------------------------------------------------------

    /**
     * Paginated active quizzes joined with categories.
     */
    public function getActiveQuizzes(string $search = '', int $categoryId = 0, int $perPage = 10): array
    {
        $builder = $this->db->table(self::TBL_QUIZZES . ' q');
        $builder->select('q.*, c.name AS category_name');
        $builder->join(self::TBL_CATS . ' c', 'c.id = q.category_id', 'left');
        $builder->where('q.is_deleted', '0');
        $builder->where('q.status', '1');
        $builder->where('q.total_questions >', 0); // only quizzes that have at least one question

        if ($search !== '') {
            $builder->groupStart();
            $builder->like('q.title', $search);
            $builder->orLike('q.description', $search);
            $builder->groupEnd();
        }

        if ($categoryId > 0) {
            $builder->where('q.category_id', $categoryId);
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
     * Single active quiz by ID joined with its category.
     */
    public function getActiveQuizById(int $id): ?array
    {
        $builder = $this->db->table(self::TBL_QUIZZES . ' q');
        $builder->select('q.*, c.name AS category_name');
        $builder->join(self::TBL_CATS . ' c', 'c.id = q.category_id', 'left');
        $builder->where('q.id', $id);
        $builder->where('q.is_deleted', '0');
        $builder->where('q.status', '1');
        $builder->where('q.total_questions >', 0); // block direct URL access to empty quizzes
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * All active, non-deleted categories (for filter dropdowns).
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

    // -------------------------------------------------------
    // SECTION 4: ATTEMPT START
    // -------------------------------------------------------

    /**
     * Return an in-progress (status = '0') attempt for a user+quiz pair, if any.
     */
    public function getInProgressAttempt(int $userId, int $quizId): ?array
    {
        $builder = $this->db->table(self::TBL_ATTEMPT);
        $builder->where('user_id', $userId);
        $builder->where('quiz_id', $quizId);
        $builder->where('status', '0');
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Insert a new attempt row and return the new ID.
     */
    public function startAttempt(int $userId, int $quizId): int
    {
        $builder = $this->db->table(self::TBL_ATTEMPT);
        $builder->insert([
            'user_id'    => $userId,
            'quiz_id'    => $quizId,
            'started_at' => date('Y-m-d H:i:s'),
            'status'     => '0',
            'is_deleted' => '0',
            'created_on' => date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->insertID();
    }

    /**
     * Load all active questions for a quiz, each with their options.
     * Options deliberately exclude the is_correct field to prevent cheating.
     */
    public function getQuestionsForAttempt(int $quizId): array
    {
        $qBuilder = $this->db->table(self::TBL_QUEST);
        $qBuilder->select('id, question_text, marks');
        $qBuilder->where('quiz_id', $quizId);
        $qBuilder->where('status', '1');
        $qBuilder->where('is_deleted', '0');
        $qBuilder->orderBy('id', 'ASC');
        $qQuery     = $qBuilder->get();
        $questions  = $qQuery->getResultArray();

        foreach ($questions as &$question) {
            $oBuilder = $this->db->table(self::TBL_OPTIONS);
            $oBuilder->select('id, option_text');
            $oBuilder->where('question_id', $question['id']);
            $oBuilder->where('is_deleted', '0');
            $oBuilder->orderBy('id', 'ASC');
            $oQuery             = $oBuilder->get();
            $question['options'] = $oQuery->getResultArray();
        }
        unset($question);

        return $questions;
    }

    /**
     * Sum of marks for all active questions in a quiz.
     */
    public function getTotalMarksForQuiz(int $quizId): int
    {
        $builder = $this->db->table(self::TBL_QUEST);
        $builder->selectSum('marks', 'total');
        $builder->where('quiz_id', $quizId);
        $builder->where('status', '1');
        $builder->where('is_deleted', '0');
        $query  = $builder->get();
        $row    = $query->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    // -------------------------------------------------------
    // SECTION 5: SAVE ANSWERS
    // -------------------------------------------------------

    /**
     * Upsert a single answer: update if a row already exists, otherwise insert.
     */
    public function saveAnswer(int $attemptId, int $questionId, int $selectedOptionId): bool
    {
        $checkBuilder = $this->db->table(self::TBL_ANSWERS);
        $checkBuilder->where('attempt_id', $attemptId);
        $checkBuilder->where('question_id', $questionId);
        $checkBuilder->where('is_deleted', '0');
        $existing = $checkBuilder->get()->getRowArray();

        if ($existing) {
            $updBuilder = $this->db->table(self::TBL_ANSWERS);
            $updBuilder->where('id', $existing['id']);
            return $updBuilder->update(['selected_option_id' => $selectedOptionId]);
        }

        $insBuilder = $this->db->table(self::TBL_ANSWERS);
        return $insBuilder->insert([
            'attempt_id'         => $attemptId,
            'question_id'        => $questionId,
            'selected_option_id' => $selectedOptionId,
            'is_correct'         => '0',
            'marks_obtained'     => 0,
            'is_deleted'         => '0',
            'created_on'         => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Returns a map of [question_id => selected_option_id] for the given attempt.
     */
    public function getSavedAnswers(int $attemptId): array
    {
        $builder = $this->db->table(self::TBL_ANSWERS);
        $builder->select('question_id, selected_option_id');
        $builder->where('attempt_id', $attemptId);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $rows  = $query->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['question_id']] = (int) $row['selected_option_id'];
        }
        return $map;
    }

    // -------------------------------------------------------
    // SECTION 6: SUBMIT AND SCORE
    // -------------------------------------------------------

    /**
     * Return an attempt only if it belongs to the given user (ownership check).
     */
    public function getAttemptForUser(int $attemptId, int $userId): ?array
    {
        $builder = $this->db->table(self::TBL_ATTEMPT);
        $builder->where('id', $attemptId);
        $builder->where('user_id', $userId);
        $builder->where('is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Server-side check: has the quiz duration window already expired?
     */
    public function isAttemptExpired(array $attempt, int $durationMinutes): bool
    {
        if (empty($attempt['started_at'])) {
            return false;
        }
        $startedAt = strtotime($attempt['started_at']);
        $expiresAt = $startedAt + ($durationMinutes * 60);
        return time() > $expiresAt;
    }

    /**
     * Score an attempt fully on the server side:
     *  - Fetches all active questions for the quiz
     *  - For each question, fetches the correct option
     *  - Compares against the user's saved answer
     *  - Upserts the attempt_answers row with scoring data
     *  - Updates quiz_attempts with final score, total_marks, percentage, status
     *
     * Returns the final percentage as a float, or -1.0 on failure.
     */
    public function submitAndScore(int $attemptId, int $userId): float
    {
        // Ownership check
        $attempt = $this->getAttemptForUser($attemptId, $userId);
        if (! $attempt) {
            return -1.0;
        }

        // Already submitted
        if ($attempt['status'] === '1') {
            return (float) $attempt['percentage'];
        }

        $quizId = (int) $attempt['quiz_id'];

        // Load all active questions
        $qBuilder = $this->db->table(self::TBL_QUEST);
        $qBuilder->select('id, marks');
        $qBuilder->where('quiz_id', $quizId);
        $qBuilder->where('status', '1');
        $qBuilder->where('is_deleted', '0');
        $qQuery    = $qBuilder->get();
        $questions = $qQuery->getResultArray();

        // Load user's saved answers into a map
        $savedAnswers = $this->getSavedAnswers($attemptId);

        $this->db->transStart();

        $totalMarks   = 0;
        $earnedMarks  = 0;

        foreach ($questions as $question) {
            $questionId = (int) $question['id'];
            $marks      = (int) $question['marks'];
            $totalMarks += $marks;

            // Fetch correct option
            $corBuilder = $this->db->table(self::TBL_OPTIONS);
            $corBuilder->select('id');
            $corBuilder->where('question_id', $questionId);
            $corBuilder->where('is_correct', '1');
            $corBuilder->where('is_deleted', '0');
            $corQuery       = $corBuilder->get();
            $correctOption  = $corQuery->getRowArray();
            $correctOptionId = $correctOption ? (int) $correctOption['id'] : null;

            $selectedOptionId = $savedAnswers[$questionId] ?? null;
            $isCorrect        = ($selectedOptionId !== null && $selectedOptionId === $correctOptionId) ? '1' : '0';
            $marksObtained    = ($isCorrect === '1') ? $marks : 0;
            $earnedMarks     += $marksObtained;

            // Upsert attempt_answers
            $chkBuilder = $this->db->table(self::TBL_ANSWERS);
            $chkBuilder->where('attempt_id', $attemptId);
            $chkBuilder->where('question_id', $questionId);
            $chkBuilder->where('is_deleted', '0');
            $existingAnswer = $chkBuilder->get()->getRowArray();

            if ($existingAnswer) {
                $updAns = $this->db->table(self::TBL_ANSWERS);
                $updAns->where('id', $existingAnswer['id']);
                $updAns->update([
                    'selected_option_id' => $selectedOptionId,
                    'correct_option_id'  => $correctOptionId,
                    'is_correct'         => $isCorrect,
                    'marks_obtained'     => $marksObtained,
                ]);
            } else {
                $insAns = $this->db->table(self::TBL_ANSWERS);
                $insAns->insert([
                    'attempt_id'         => $attemptId,
                    'question_id'        => $questionId,
                    'selected_option_id' => $selectedOptionId,
                    'correct_option_id'  => $correctOptionId,
                    'is_correct'         => $isCorrect,
                    'marks_obtained'     => $marksObtained,
                    'is_deleted'         => '0',
                    'created_on'         => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $percentage = ($totalMarks > 0) ? round(($earnedMarks / $totalMarks) * 100, 2) : 0.00;

        // Finalise the attempt row
        $finalBuilder = $this->db->table(self::TBL_ATTEMPT);
        $finalBuilder->where('id', $attemptId);
        $finalBuilder->update([
            'completed_at' => date('Y-m-d H:i:s'),
            'score'        => $earnedMarks,
            'total_marks'  => $totalMarks,
            'percentage'   => $percentage,
            'status'       => '1',
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return -1.0;
        }

        return (float) $percentage;
    }

    // -------------------------------------------------------
    // SECTION 7: RESULTS
    // -------------------------------------------------------

    /**
     * Return a completed attempt that belongs to the given user.
     * Returns null if not found, not owned, or not yet completed.
     */
    public function getAttemptResult(int $attemptId, int $userId): ?array
    {
        $builder = $this->db->table(self::TBL_ATTEMPT . ' a');
        $builder->select('a.*, q.title AS quiz_title, q.passing_percentage, q.duration, c.name AS category_name');
        $builder->join(self::TBL_QUIZZES . ' q', 'q.id = a.quiz_id',       'left');
        $builder->join(self::TBL_CATS    . ' c', 'c.id = q.category_id',   'left');
        $builder->where('a.id', $attemptId);
        $builder->where('a.user_id', $userId);
        $builder->where('a.status', '1');
        $builder->where('a.is_deleted', '0');
        $query = $builder->get();
        $row   = $query->getRowArray();
        return $row ?: null;
    }

    /**
     * Detailed answer analysis for a result page.
     * Joins questions, the selected option, and the correct option separately.
     */
    public function getAnswerAnalysis(int $attemptId): array
    {
        $builder = $this->db->table(self::TBL_ANSWERS . ' aa');
        $builder->select('aa.*, qst.question_text, qst.marks AS question_marks, sel.option_text AS selected_option_text, cor.option_text AS correct_option_text');
        $builder->join(self::TBL_QUEST   . ' qst', 'qst.id = aa.question_id',       'left');
        $builder->join(self::TBL_OPTIONS . ' sel', 'sel.id = aa.selected_option_id', 'left');
        $builder->join(self::TBL_OPTIONS . ' cor', 'cor.id = aa.correct_option_id',  'left');
        $builder->where('aa.attempt_id', $attemptId);
        $builder->where('aa.is_deleted', '0');
        $builder->orderBy('aa.id', 'ASC');
        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Paginated list of attempts for a given user.
     */
    public function getUserAttempts(int $userId, int $perPage = 10): array
    {
        $builder = $this->db->table(self::TBL_ATTEMPT . ' a');
        $builder->select('a.*, q.title AS quiz_title, q.passing_percentage, c.name AS category_name');
        $builder->join(self::TBL_QUIZZES . ' q', 'q.id = a.quiz_id',     'left');
        $builder->join(self::TBL_CATS    . ' c', 'c.id = q.category_id', 'left');
        $builder->where('a.user_id', $userId);
        $builder->where('a.is_deleted', '0');
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

    // -------------------------------------------------------
    // SECTION 8: DASHBOARD STATS
    // -------------------------------------------------------

    /**
     * Aggregate stats for a user's personal dashboard.
     * Returns total, passed, failed, average percentage, and max percentage.
     */
    public function getUserDashboardStats(int $userId): array
    {
        $totalBuilder = $this->db->table(self::TBL_ATTEMPT);
        $totalBuilder->where('user_id', $userId);
        $totalBuilder->where('status', '1');
        $totalBuilder->where('is_deleted', '0');
        $total = $totalBuilder->countAllResults();

        $passBuilder = $this->db->table(self::TBL_ATTEMPT . ' a');
        $passBuilder->join(self::TBL_QUIZZES . ' q', 'q.id = a.quiz_id', 'left');
        $passBuilder->where('a.user_id', $userId);
        $passBuilder->where('a.status', '1');
        $passBuilder->where('a.is_deleted', '0');
        $passBuilder->where('a.percentage >= q.passing_percentage', null, false);
        $passed = $passBuilder->countAllResults();

        $failed = $total - $passed;

        $avgBuilder = $this->db->table(self::TBL_ATTEMPT);
        $avgBuilder->selectAvg('percentage', 'avg_percentage');
        $avgBuilder->selectMax('percentage', 'max_percentage');
        $avgBuilder->where('user_id', $userId);
        $avgBuilder->where('status', '1');
        $avgBuilder->where('is_deleted', '0');
        $avgQuery = $avgBuilder->get();
        $avgRow   = $avgQuery->getRowArray();

        return [
            'total'          => $total,
            'passed'         => $passed,
            'failed'         => $failed,
            'avg_percentage' => round((float) ($avgRow['avg_percentage'] ?? 0), 2),
            'max_percentage' => round((float) ($avgRow['max_percentage'] ?? 0), 2),
        ];
    }

    /**
     * Most recent N completed attempts for a user.
     */
    public function getRecentAttempts(int $userId, int $limit = 5): array
    {
        $builder = $this->db->table(self::TBL_ATTEMPT . ' a');
        $builder->select('a.*, q.title AS quiz_title, q.passing_percentage');
        $builder->join(self::TBL_QUIZZES . ' q', 'q.id = a.quiz_id', 'left');
        $builder->where('a.user_id', $userId);
        $builder->where('a.status', '1');
        $builder->where('a.is_deleted', '0');
        $builder->orderBy('a.completed_at', 'DESC');
        $builder->limit($limit);
        $query = $builder->get();
        return $query->getResultArray();
    }
}
