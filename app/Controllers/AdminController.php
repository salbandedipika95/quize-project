<?php

namespace App\Controllers;

use App\Models\AdminModel;

/**
 * AdminController
 * All redirect() calls use site_url() so the subfolder baseURL is respected.
 */
class AdminController extends BaseController
{
    protected AdminModel $adminModel;
    protected \CodeIgniter\Session\Session $session;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->adminModel = new AdminModel();
        $this->session    = \Config\Services::session();
    }

    // -------------------------------------------------------
    // AUTH GUARD
    // -------------------------------------------------------
    private function requireAdmin(): void
    {
        if (! $this->session->get('admin_logged_in')) {
            redirect()->to(site_url('admin/login'))->send();
            exit;
        }
    }

    private function adminData(): array
    {
        return [
            'admin_name'  => $this->session->get('admin_name'),
            'admin_email' => $this->session->get('admin_email'),
            'admin_id'    => $this->session->get('admin_id'),
            'admin_role'  => $this->session->get('admin_role'),
        ];
    }

    // Guard: only super_admin can access Users section
    private function requireSuperAdmin(): void
    {
        $this->requireAdmin();
        if ($this->session->get('admin_role') !== 'super_admin') {
            redirect()->to(site_url('admin/dashboard'))->send();
            exit;
        }
    }

    // -------------------------------------------------------
    // AUTH
    // -------------------------------------------------------
    public function login()
    {
        if ($this->session->get('admin_logged_in')) {
            return redirect()->to(site_url('admin/dashboard'));
        }
        return view('admin/auth/login');
    }

    public function loginProcess()
    {
        $email    = trim($this->request->getPost('email') ?? '');
        $password = trim($this->request->getPost('password') ?? '');

        if ($email === '' || $password === '') {
            return redirect()->back()->with('error', 'Email and password are required.')->withInput();
        }

        $admin = $this->adminModel->findAdminByEmail($email);
       

        if (! $admin || ! password_verify($password, $admin['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password.')->withInput();
        }

        if ($admin['status'] !== '1') {
            return redirect()->back()->with('error', 'Your account is inactive.')->withInput();
        }

        $this->adminModel->updateLastLogin((int) $admin['id']);

        $this->session->set([
            'admin_logged_in' => true,
            'admin_id'        => $admin['id'],
            'admin_name'      => $admin['name'],
            'admin_email'     => $admin['email'],
            'admin_role'      => $admin['role'] ?? 'admin', // 'admin' or 'super_admin'
        ]);

        return redirect()->to(site_url('admin/dashboard'));
    }

    public function logout()
    {
        $this->session->remove(['admin_logged_in', 'admin_id', 'admin_name', 'admin_email']);
        return redirect()->to(site_url('admin/login'));
    }

    // -------------------------------------------------------
    // DASHBOARD
    // -------------------------------------------------------
    public function dashboard()
    {
        $this->requireAdmin();

        $data                    = $this->adminData();
        $data['page_title']      = 'Dashboard';
        $data['stats']           = $this->adminModel->getDashboardStats();
        $data['recent_users']    = $this->adminModel->getRecentUsers(5);
        $data['recent_attempts'] = $this->adminModel->getRecentAttempts(5);

        return view('admin/dashboard', $data);
    }

    // -------------------------------------------------------
    // USERS  (super_admin only — manages tbl_admin_users)
    // -------------------------------------------------------
    public function users()
    {
        $this->requireSuperAdmin();

        $search = trim($this->request->getGet('search') ?? '');
        $result = $this->adminModel->getAdminUsers($search, 10);

        $data               = $this->adminData();
        $data['page_title'] = 'Manage Admin Users';
        $data['search']     = $search;
        $data['users']      = $result['data'];
        $data['pager']      = $result['pager'];
        $data['total']      = $result['total'];

        return view('admin/users/index', $data);
    }

    public function userCreate()
    {
        $this->requireSuperAdmin();

        $data               = $this->adminData();
        $data['page_title'] = 'Create Admin User';

        return view('admin/users/form', $data);
    }

    public function userStore()
    {
        $this->requireSuperAdmin();

        $rules = [
            'name'     => 'required|min_length[2]|max_length[100]',
            'email'    => 'required|valid_email|max_length[150]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,super_admin]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $this->adminModel->createAdminUser([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role'     => $this->request->getPost('role'),
            'status'   => $this->request->getPost('status') ?? '1',
        ]);

        return redirect()->to(site_url('admin/users'))->with('success', 'Admin user created successfully.');
    }

    public function userEdit(int $id)
    {
        $this->requireSuperAdmin();

        $user = $this->adminModel->getAdminUserById($id);
        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User not found.');
        }

        $data               = $this->adminData();
        $data['page_title'] = 'Edit Admin User';
        $data['user']       = $user;

        return view('admin/users/form', $data);
    }

    public function userUpdate(int $id)
    {
        $this->requireSuperAdmin();

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]',
            'role'  => 'required|in_list[admin,super_admin]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $updateData = [
            'name'   => $this->request->getPost('name'),
            'email'  => $this->request->getPost('email'),
            'role'   => $this->request->getPost('role'),
            'status' => $this->request->getPost('status') ?? '1',
        ];

        $newPassword = $this->request->getPost('password');
        if (! empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $this->adminModel->updateAdminUser($id, $updateData);

        return redirect()->to(site_url('admin/users'))->with('success', 'Admin user updated successfully.');
    }

    public function userToggle(int $id)
    {
        $this->requireSuperAdmin();

        $this->adminModel->toggleAdminUserStatus($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Status updated.']);
        }

        return redirect()->to(site_url('admin/users'))->with('success', 'Status updated.');
    }

    // -------------------------------------------------------
    // CATEGORIES
    // -------------------------------------------------------
    public function categories()
    {
        $this->requireAdmin();

        $search = trim($this->request->getGet('search') ?? '');
        $result = $this->adminModel->getCategories($search, 10);

        $data               = $this->adminData();
        $data['page_title'] = 'Manage Categories';
        $data['search']     = $search;
        $data['categories'] = $result['data'];
        $data['pager']      = $result['pager'];
        $data['total']      = $result['total'];

        return view('admin/categories/index', $data);
    }

    public function categoryStore()
    {
        $this->requireAdmin();

        $name = trim($this->request->getPost('name') ?? '');

        if ($name === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => false, 'message' => 'Name is required.']);
            }
            return redirect()->back()->with('error', 'Category name is required.')->withInput();
        }

        if ($this->adminModel->categoryNameExists($name)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => false, 'message' => 'Category already exists.']);
            }
            return redirect()->back()->with('error', 'Category already exists.')->withInput();
        }

        $this->adminModel->createCategory([
            'name'        => $name,
            'description' => $this->request->getPost('description') ?? '',
            'status'      => $this->request->getPost('status') ?? '1',
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Category created successfully.']);
        }

        return redirect()->to(site_url('admin/categories'))->with('success', 'Category created.');
    }

    public function categoryEdit(int $id)
    {
        $this->requireAdmin();

        $category = $this->adminModel->getCategoryById($id);
        if (! $category) {
            return redirect()->to(site_url('admin/categories'))->with('error', 'Category not found.');
        }

        $data               = $this->adminData();
        $data['page_title'] = 'Edit Category';
        $data['category']   = $category;

        return view('admin/categories/form', $data);
    }

    public function categoryUpdate(int $id)
    {
        $this->requireAdmin();

        $name = trim($this->request->getPost('name') ?? '');

        if ($name === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => false, 'message' => 'Name is required.']);
            }
            return redirect()->back()->with('error', 'Name is required.')->withInput();
        }

        if ($this->adminModel->categoryNameExists($name, $id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => false, 'message' => 'Name already taken.']);
            }
            return redirect()->back()->with('error', 'Name already taken.')->withInput();
        }

        $this->adminModel->updateCategory($id, [
            'name'        => $name,
            'description' => $this->request->getPost('description') ?? '',
            'status'      => $this->request->getPost('status') ?? '1',
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Category updated.']);
        }

        return redirect()->to(site_url('admin/categories'))->with('success', 'Category updated.');
    }

    public function categoryToggle(int $id)
    {
        $this->requireAdmin();

        $this->adminModel->toggleCategoryStatus($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Status updated.']);
        }

        return redirect()->to(site_url('admin/categories'))->with('success', 'Status updated.');
    }

    // -------------------------------------------------------
    // QUIZZES
    // -------------------------------------------------------
    public function quizzes()
    {
        $this->requireAdmin();

        $search     = trim($this->request->getGet('search') ?? '');
        $categoryId = (int) ($this->request->getGet('category_id') ?? 0);
        $status     = $this->request->getGet('status') ?? '';
        $result     = $this->adminModel->getQuizzes($search, $categoryId, $status, 10);

        $data               = $this->adminData();
        $data['page_title'] = 'Manage Quizzes';
        $data['search']     = $search;
        $data['quizzes']    = $result['data'];
        $data['pager']      = $result['pager'];
        $data['total']      = $result['total'];
        $data['categories'] = $this->adminModel->getActiveCategories();

        return view('admin/quizzes/index', $data);
    }

    public function quizCreate()
    {
        $this->requireAdmin();

        $data               = $this->adminData();
        $data['page_title'] = 'Create Quiz';
        $data['categories'] = $this->adminModel->getActiveCategories();

        return view('admin/quizzes/form', $data);
    }

    public function quizStore()
    {
        $this->requireAdmin();

        $rules = [
            'title'              => 'required|min_length[3]|max_length[200]',
            'category_id'        => 'required|integer',
            'duration'           => 'required|integer|greater_than[0]',
            'passing_percentage' => 'required|decimal|greater_than[0]|less_than_equal_to[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $this->adminModel->createQuiz([
            'category_id'        => $this->request->getPost('category_id'),
            'title'              => $this->request->getPost('title'),
            'description'        => $this->request->getPost('description') ?? '',
            'duration'           => $this->request->getPost('duration'),
            'passing_percentage' => $this->request->getPost('passing_percentage'),
            'status'             => $this->request->getPost('status') ?? '1',
            'created_by'         => $this->session->get('admin_id'),
        ]);

        return redirect()->to(site_url('admin/quizzes'))->with('success', 'Quiz created successfully.');
    }

    public function quizEdit(int $id)
    {
        $this->requireAdmin();

        $quiz = $this->adminModel->getQuizById($id);
        if (! $quiz) {
            return redirect()->to(site_url('admin/quizzes'))->with('error', 'Quiz not found.');
        }

        $data               = $this->adminData();
        $data['page_title'] = 'Edit Quiz';
        $data['quiz']       = $quiz;
        $data['categories'] = $this->adminModel->getActiveCategories();

        return view('admin/quizzes/form', $data);
    }

    public function quizUpdate(int $id)
    {
        $this->requireAdmin();

        $rules = [
            'title'              => 'required|min_length[3]|max_length[200]',
            'category_id'        => 'required|integer',
            'duration'           => 'required|integer|greater_than[0]',
            'passing_percentage' => 'required|decimal|greater_than[0]|less_than_equal_to[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $this->adminModel->updateQuiz($id, [
            'category_id'        => $this->request->getPost('category_id'),
            'title'              => $this->request->getPost('title'),
            'description'        => $this->request->getPost('description') ?? '',
            'duration'           => $this->request->getPost('duration'),
            'passing_percentage' => $this->request->getPost('passing_percentage'),
            'status'             => $this->request->getPost('status') ?? '1',
        ]);

        return redirect()->to(site_url('admin/quizzes'))->with('success', 'Quiz updated.');
    }

    public function quizToggle(int $id)
    {
        $this->requireAdmin();

        $this->adminModel->toggleQuizStatus($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Status updated.']);
        }

        return redirect()->to(site_url('admin/quizzes'))->with('success', 'Status updated.');
    }

    // -------------------------------------------------------
    // QUESTIONS
    // -------------------------------------------------------
    public function questions(int $quizId)
    {
        $this->requireAdmin();

        $quiz = $this->adminModel->getQuizById($quizId);
        if (! $quiz) {
            return redirect()->to(site_url('admin/quizzes'))->with('error', 'Quiz not found.');
        }

        $search = trim($this->request->getGet('search') ?? '');
        $result = $this->adminModel->getQuestions($quizId, $search, 10);

        $data               = $this->adminData();
        $data['page_title'] = 'Questions — ' . $quiz['title'];
        $data['quiz']       = $quiz;
        $data['search']     = $search;
        $data['questions']  = $result['data'];
        $data['pager']      = $result['pager'];
        $data['total']      = $result['total'];

        return view('admin/questions/index', $data);
    }

    public function questionCreate(int $quizId)
    {
        $this->requireAdmin();

        $quiz = $this->adminModel->getQuizById($quizId);
        if (! $quiz) {
            return redirect()->to(site_url('admin/quizzes'))->with('error', 'Quiz not found.');
        }

        $data               = $this->adminData();
        $data['page_title'] = 'Add Question';
        $data['quiz']       = $quiz;

        return view('admin/questions/form', $data);
    }

    public function questionStore()
    {
        $this->requireAdmin();

        $quizId       = (int) $this->request->getPost('quiz_id');
        $questionText = trim($this->request->getPost('question_text') ?? '');
        $marks        = (int) $this->request->getPost('marks');
        $options      = $this->request->getPost('options') ?? [];
        $correctIndex = (int) $this->request->getPost('correct_option');

        if ($questionText === '' || count($options) < 2) {
            return redirect()->back()->with('error', 'Question text and at least 2 options are required.')->withInput();
        }

        $optionRows = [];
        foreach ($options as $i => $optText) {
            $optText = trim($optText);
            if ($optText === '') {
                continue;
            }
            $optionRows[] = [
                'option_text' => $optText,
                'is_correct'  => ($i === $correctIndex) ? '1' : '0',
            ];
        }

        $questionId = $this->adminModel->createQuestion([
            'quiz_id'       => $quizId,
            'question_text' => $questionText,
            'marks'         => $marks > 0 ? $marks : 1,
            'status'        => '1',
        ], $optionRows);

        if (! $questionId) {
            return redirect()->back()->with('error', 'Failed to save question.')->withInput();
        }

        $this->adminModel->syncQuizQuestionCount($quizId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Question added.', 'id' => $questionId]);
        }

        return redirect()->to(site_url('admin/questions/' . $quizId))->with('success', 'Question added.');
    }

    public function questionEdit(int $id)
    {
        $this->requireAdmin();

        $question = $this->adminModel->getQuestionById($id);
        if (! $question) {
            return redirect()->back()->with('error', 'Question not found.');
        }

        $quiz    = $this->adminModel->getQuizById((int) $question['quiz_id']);
        $options = $this->adminModel->getOptionsByQuestion($id);

        $data               = $this->adminData();
        $data['page_title'] = 'Edit Question';
        $data['question']   = $question;
        $data['quiz']       = $quiz;
        $data['options']    = $options;

        return view('admin/questions/form', $data);
    }

    public function questionUpdate(int $id)
    {
        $this->requireAdmin();

        $question = $this->adminModel->getQuestionById($id);
        if (! $question) {
            return redirect()->back()->with('error', 'Question not found.');
        }

        $questionText = trim($this->request->getPost('question_text') ?? '');
        $marks        = (int) $this->request->getPost('marks');
        $options      = $this->request->getPost('options') ?? [];
        $correctIndex = (int) $this->request->getPost('correct_option');

        if ($questionText === '') {
            return redirect()->back()->with('error', 'Question text is required.')->withInput();
        }

        $optionRows = [];
        foreach ($options as $i => $optText) {
            $optText = trim($optText);
            if ($optText === '') {
                continue;
            }
            $optionRows[] = [
                'option_text' => $optText,
                'is_correct'  => ($i === $correctIndex) ? '1' : '0',
            ];
        }

        $this->adminModel->updateQuestion($id, [
            'question_text' => $questionText,
            'marks'         => $marks > 0 ? $marks : 1,
            'status'        => $this->request->getPost('status') ?? '1',
        ], $optionRows);

        return redirect()->to(site_url('admin/questions/' . $question['quiz_id']))->with('success', 'Question updated.');
    }

    public function questionToggle(int $id)
    {
        $this->requireAdmin();

        $question = $this->adminModel->getQuestionById($id);
        if (! $question) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => false, 'message' => 'Not found.']);
            }
            return redirect()->back()->with('error', 'Question not found.');
        }

        $this->adminModel->toggleQuestionStatus($id);
        $this->adminModel->syncQuizQuestionCount((int) $question['quiz_id']);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => true, 'message' => 'Status updated.']);
        }

        return redirect()->back()->with('success', 'Status updated.');
    }

    // -------------------------------------------------------
    // ATTEMPTS
    // -------------------------------------------------------
    public function attempts()
    {
        $this->requireAdmin();

        $search = trim($this->request->getGet('search') ?? '');
        $result = $this->adminModel->getAllAttempts($search, 10);

        $data               = $this->adminData();
        $data['page_title'] = 'Quiz Attempts';
        $data['search']     = $search;
        $data['attempts']   = $result['data'];
        $data['pager']      = $result['pager'];
        $data['total']      = $result['total'];

        return view('admin/attempts/index', $data);
    }

    public function attemptView(int $id)
    {
        $this->requireAdmin();

        $attempt = $this->adminModel->getAttemptById($id);
        if (! $attempt) {
            return redirect()->to(site_url('admin/attempts'))->with('error', 'Attempt not found.');
        }

        $answers = $this->adminModel->getAttemptAnswers($id);

        $data               = $this->adminData();
        $data['page_title'] = 'Attempt Detail';
        $data['attempt']    = $attempt;
        $data['answers']    = $answers;

        return view('admin/attempts/view', $data);
    }

    // -------------------------------------------------------
    // PROFILE
    // -------------------------------------------------------
    public function profile()
    {
        $this->requireAdmin();

        $admin = $this->adminModel->findAdminById((int) $this->session->get('admin_id'));

        $data               = $this->adminData();
        $data['page_title'] = 'My Profile';
        $data['admin']      = $admin;

        return view('admin/profile', $data);
    }

    public function profileUpdate()
    {
        $this->requireAdmin();

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $adminId = (int) $this->session->get('admin_id');

        $this->adminModel->updateAdminProfile($adminId, [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ]);

        $this->session->set('admin_name',  $this->request->getPost('name'));
        $this->session->set('admin_email', $this->request->getPost('email'));

        return redirect()->to(site_url('admin/profile'))->with('success', 'Profile updated.');
    }

    public function profilePassword()
    {
        $this->requireAdmin();

        $current = $this->request->getPost('current_password') ?? '';
        $new     = $this->request->getPost('new_password') ?? '';
        $confirm = $this->request->getPost('confirm_password') ?? '';

        if ($current === '' || $new === '' || $confirm === '') {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        if ($new !== $confirm) {
            return redirect()->back()->with('error', 'New passwords do not match.');
        }

        if (strlen($new) < 6) {
            return redirect()->back()->with('error', 'Password must be at least 6 characters.');
        }

        $adminId = (int) $this->session->get('admin_id');
        $admin   = $this->adminModel->findAdminById($adminId);

        if (! password_verify($current, $admin['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $this->adminModel->updateAdminPassword($adminId, password_hash($new, PASSWORD_BCRYPT));

        return redirect()->to(site_url('admin/profile'))->with('success', 'Password changed.');
    }
}
