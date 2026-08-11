<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * UserController
 * All redirect() calls use site_url() so the subfolder baseURL is respected.
 */
class UserController extends BaseController
{
    protected UserModel $userModel;
    protected \CodeIgniter\Session\Session $session;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->userModel = new UserModel();
        $this->session   = \Config\Services::session();
    }

    // -------------------------------------------------------
    // AUTH GUARD
    // -------------------------------------------------------
    private function requireUser(): void
    {
        if (! $this->session->get('user_logged_in')) {
            redirect()->to(site_url('login'))->send();
            exit;
        }
    }

    private function userData(): array
    {
        return [
            'user_name'  => $this->session->get('user_name'),
            'user_email' => $this->session->get('user_email'),
            'user_id'    => $this->session->get('user_id'),
        ];
    }

    // -------------------------------------------------------
    // REGISTER
    // -------------------------------------------------------
    public function register()
    {
        if ($this->session->get('user_logged_in')) {
            return redirect()->to(site_url('user/dashboard'));
        }
        return view('front/auth/register');
    }

    public function registerProcess()
    {
        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|max_length[150]',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $email = trim($this->request->getPost('email'));

        if ($this->userModel->emailExists($email)) {
            return redirect()->back()->with('error', 'This email is already registered.')->withInput();
        }

        $userId = $this->userModel->register([
            'name'     => trim($this->request->getPost('name')),
            'email'    => $email,
            'password' => $this->request->getPost('password'),
        ]);

        if (! $userId) {
            return redirect()->back()->with('error', 'Registration failed. Please try again.')->withInput();
        }

        return redirect()->to(site_url('login'))->with('success', 'Registration successful. Please log in.');
    }

    // -------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------
    public function login()
    {
        if ($this->session->get('user_logged_in')) {
            return redirect()->to(site_url('user/dashboard'));
        }
        return view('front/auth/login');
    }

    public function loginProcess()
    {
        $email    = trim($this->request->getPost('email') ?? '');
        $password = trim($this->request->getPost('password') ?? '');

        if ($email === '' || $password === '') {
            return redirect()->back()->with('error', 'Email and password are required.')->withInput();
        }

        $user = $this->userModel->findByEmail($email);

        if (! $user || ! $this->userModel->verifyPassword($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password.')->withInput();
        }

        if ($user['status'] !== '1') {
            return redirect()->back()->with('error', 'Your account is inactive.')->withInput();
        }

        $this->userModel->updateLastLogin((int) $user['id']);

        $this->session->set([
            'user_logged_in' => true,
            'user_id'        => $user['id'],
            'user_name'      => $user['name'],
            'user_email'     => $user['email'],
        ]);

        return redirect()->to(site_url('user/dashboard'));
    }

    public function logout()
    {
        $this->session->remove(['user_logged_in', 'user_id', 'user_name', 'user_email']);
        return redirect()->to(site_url('login'));
    }

    // -------------------------------------------------------
    // DASHBOARD
    // -------------------------------------------------------
    public function dashboard()
    {
        $this->requireUser();

        $userId = (int) $this->session->get('user_id');
        $data   = $this->userData();

        $data['page_title']      = 'My Dashboard';
        $data['stats']           = $this->userModel->getUserDashboardStats($userId);
        $data['recent_attempts'] = $this->userModel->getRecentAttempts($userId, 5);

        return view('front/dashboard', $data);
    }

    // -------------------------------------------------------
    // QUIZ BROWSING
    // -------------------------------------------------------
    public function quizzes()
    {
        $this->requireUser();

        $search     = trim($this->request->getGet('search') ?? '');
        $categoryId = (int) ($this->request->getGet('category_id') ?? 0);
        $result     = $this->userModel->getActiveQuizzes($search, $categoryId, 12);

        $data                = $this->userData();
        $data['page_title']  = 'Available Quizzes';
        $data['search']      = $search;
        $data['category_id'] = $categoryId;
        $data['quizzes']     = $result['data'];
        $data['pager']       = $result['pager'];
        $data['total']       = $result['total'];
        $data['categories']  = $this->userModel->getActiveCategories();

        return view('front/quizzes/index', $data);
    }

    public function quizView(int $id)
    {
        $this->requireUser();

        $quiz = $this->userModel->getActiveQuizById($id);
        if (! $quiz) {
            return redirect()->to(site_url('user/quizzes'))->with('error', 'Quiz not found.');
        }

        $userId          = (int) $this->session->get('user_id');
        $existingAttempt = $this->userModel->getInProgressAttempt($userId, $id);

        $data                     = $this->userData();
        $data['page_title']       = $quiz['title'];
        $data['quiz']             = $quiz;
        $data['existing_attempt'] = $existingAttempt;

        return view('front/quizzes/view', $data);
    }

    public function quizStart(int $id)
    {
        $this->requireUser();

        $quiz = $this->userModel->getActiveQuizById($id);
        if (! $quiz) {
            return redirect()->to(site_url('user/quizzes'))->with('error', 'Quiz not found.');
        }

        $userId          = (int) $this->session->get('user_id');
        $existingAttempt = $this->userModel->getInProgressAttempt($userId, $id);

        if ($existingAttempt) {
            return redirect()->to(site_url('user/attempt/' . $existingAttempt['id']));
        }

        $attemptId = $this->userModel->startAttempt($userId, $id);

        if (! $attemptId) {
            return redirect()->back()->with('error', 'Could not start quiz. Please try again.');
        }

        return redirect()->to(site_url('user/attempt/' . $attemptId));
    }

    // -------------------------------------------------------
    // QUIZ ATTEMPT
    // -------------------------------------------------------
    public function attempt(int $attemptId)
    {
        $this->requireUser();

        $userId  = (int) $this->session->get('user_id');
        $attempt = $this->userModel->getAttemptForUser($attemptId, $userId);

        if (! $attempt) {
            return redirect()->to(site_url('user/quizzes'))->with('error', 'Attempt not found.');
        }

        if ($attempt['status'] === '1') {
            return redirect()->to(site_url('user/result/' . $attemptId));
        }

        $quiz = $this->userModel->getActiveQuizById((int) $attempt['quiz_id']);
        if (! $quiz) {
            return redirect()->to(site_url('user/quizzes'))->with('error', 'Quiz not available.');
        }

        if ($this->userModel->isAttemptExpired($attempt, (int) $quiz['duration'])) {
            $this->userModel->submitAndScore($attemptId, $userId);
            return redirect()->to(site_url('user/result/' . $attemptId))->with('info', 'Time expired. Quiz auto-submitted.');
        }

        $questions        = $this->userModel->getQuestionsForAttempt((int) $attempt['quiz_id']);
        $savedAnswers     = $this->userModel->getSavedAnswers($attemptId);
        $startedAt        = strtotime($attempt['started_at']);
        $totalSeconds     = (int) $quiz['duration'] * 60;
        $elapsed          = time() - $startedAt;
        $remainingSeconds = max(0, $totalSeconds - $elapsed);

        $data                      = $this->userData();
        $data['page_title']        = 'Quiz: ' . $quiz['title'];
        $data['quiz']              = $quiz;
        $data['attempt']           = $attempt;
        $data['questions']         = $questions;
        $data['saved_answers']     = $savedAnswers;
        $data['remaining_seconds'] = $remainingSeconds;

        return view('front/attempt/index', $data);
    }

    public function attemptSaveAnswer()
    {
        $this->requireUser();

        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(405)
                                  ->setJSON(['status' => false, 'message' => 'Method not allowed.']);
        }

        $attemptId        = (int) $this->request->getPost('attempt_id');
        $questionId       = (int) $this->request->getPost('question_id');
        $selectedOptionId = (int) $this->request->getPost('selected_option_id');
        $userId           = (int) $this->session->get('user_id');

        $attempt = $this->userModel->getAttemptForUser($attemptId, $userId);
        if (! $attempt) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid attempt.']);
        }

        if ($attempt['status'] === '1') {
            return $this->response->setJSON(['status' => false, 'message' => 'Already submitted.']);
        }

        $quiz = $this->userModel->getActiveQuizById((int) $attempt['quiz_id']);
        if ($quiz && $this->userModel->isAttemptExpired($attempt, (int) $quiz['duration'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Time expired.', 'expired' => true]);
        }

        $saved = $this->userModel->saveAnswer($attemptId, $questionId, $selectedOptionId);

        if ($saved) {
            return $this->response->setJSON(['status' => true, 'message' => 'Answer saved.']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Could not save answer.']);
    }

    public function attemptSubmit(int $attemptId)
    {
        $this->requireUser();

        $userId  = (int) $this->session->get('user_id');
        $attempt = $this->userModel->getAttemptForUser($attemptId, $userId);

        if (! $attempt) {
            return redirect()->to(site_url('user/quizzes'))->with('error', 'Invalid attempt.');
        }

        if ($attempt['status'] === '1') {
            return redirect()->to(site_url('user/result/' . $attemptId));
        }

        $result = $this->userModel->submitAndScore($attemptId, $userId);

        if ($result < 0) {
            return redirect()->back()->with('error', 'Submission failed. Please try again.');
        }

        return redirect()->to(site_url('user/result/' . $attemptId))->with('success', 'Quiz submitted successfully.');
    }

    // -------------------------------------------------------
    // RESULTS
    // -------------------------------------------------------
    public function result(int $attemptId)
    {
        $this->requireUser();

        $userId  = (int) $this->session->get('user_id');
        $attempt = $this->userModel->getAttemptResult($attemptId, $userId);

        if (! $attempt) {
            return redirect()->to(site_url('user/attempts'))->with('error', 'Result not found.');
        }

        $answers = $this->userModel->getAnswerAnalysis($attemptId);
        $quiz    = $this->userModel->getActiveQuizById((int) $attempt['quiz_id']);
        $passed  = (float) $attempt['percentage'] >= (float) ($quiz['passing_percentage'] ?? 50);

        $data               = $this->userData();
        $data['page_title'] = 'Result — ' . $attempt['quiz_title'];
        $data['attempt']    = $attempt;
        $data['answers']    = $answers;
        $data['quiz']       = $quiz;
        $data['passed']     = $passed;

        return view('front/result/index', $data);
    }

    public function attempts()
    {
        $this->requireUser();

        $userId = (int) $this->session->get('user_id');
        $result = $this->userModel->getUserAttempts($userId, 10);

        $data               = $this->userData();
        $data['page_title'] = 'My Attempts';
        $data['attempts']   = $result['data'];
        $data['pager']      = $result['pager'];
        $data['total']      = $result['total'];

        return view('front/attempts/index', $data);
    }

    // -------------------------------------------------------
    // PROFILE
    // -------------------------------------------------------
    public function profile()
    {
        $this->requireUser();

        $userId = (int) $this->session->get('user_id');
        $user   = $this->userModel->findById($userId);

        $data               = $this->userData();
        $data['page_title'] = 'My Profile';
        $data['user']       = $user;

        return view('front/profile', $data);
    }

    public function profileUpdate()
    {
        $this->requireUser();

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $userId = (int) $this->session->get('user_id');

        $this->userModel->updateProfile($userId, [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ]);

        $this->session->set('user_name',  $this->request->getPost('name'));
        $this->session->set('user_email', $this->request->getPost('email'));

        return redirect()->to(site_url('user/profile'))->with('success', 'Profile updated.');
    }

    public function profilePassword()
    {
        $this->requireUser();

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

        $userId = (int) $this->session->get('user_id');
        $user   = $this->userModel->findById($userId);

        if (! password_verify($current, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $this->userModel->updatePassword($userId, $new);

        return redirect()->to(site_url('user/profile'))->with('success', 'Password changed.');
    }
}
