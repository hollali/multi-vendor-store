<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Middleware;
use App\Models\User;
use App\Models\Category;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        Middleware::guest();
        $this->renderView('auth/login');
    }

    public function login(): void
    {
        Middleware::guest();
        Middleware::csrf();

        $email = Validator::sanitizeEmail($this->getParam('email', ''));
        $password = $this->getParam('password', '');
        $remember = (bool)$this->getParam('remember', false);

        $this->validator->validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required|min:6']
        );

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/login', 'Please check your input', 'error');
            return;
        }

        $rateKey = 'login_' . $email;
        if (!Middleware::rateLimit($rateKey, 5, 15)) {
            $this->redirectWith('/login', 'Too many login attempts. Please try again in 15 minutes.', 'error');
            return;
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user->password)) {
            Middleware::incrementRateLimit($rateKey);
            $this->redirectWith('/login', 'Invalid email or password.', 'error');
            return;
        }

        if (isset($user->status) && $user->status === 'inactive') {
            $this->redirectWith('/login', 'Your account has been deactivated.', 'error');
            return;
        }

        $this->session->setUser($user);
        $this->session->regenerate();
        Middleware::clearRateLimit($rateKey);

        $intended = $this->session->get('intended_url', '');
        if ($intended) {
            $this->session->remove('intended_url');
            $this->redirect($intended);
        }

        $role = $user->role ?? 'customer';
        $redirectMap = [
            'admin' => '/admin/dashboard',
            'vendor' => '/vendor/dashboard',
            'customer' => '/dashboard',
        ];
        $this->redirect($redirectMap[$role] ?? '/dashboard');
    }

    public function registerForm(): void
    {
        Middleware::guest();
        $categories = Category::where('is_active', 1)->orderBy('name', 'ASC')->get();
        $this->renderView('auth/register', ['categories' => $categories]);
    }

    public function register(): void
    {
        Middleware::guest();
        Middleware::csrf();

        $fullName = Validator::sanitizeString($this->getParam('name', ''));
        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => Validator::sanitizeEmail($this->getParam('email', '')),
            'phone' => Validator::sanitizeString($this->getParam('phone', '')),
            'password' => $this->getParam('password', ''),
            'password_confirmation' => $this->getParam('password_confirmation', ''),
            'role' => in_array($this->getParam('role', 'customer'), ['customer', 'vendor']) ? $this->getParam('role') : 'customer',
        ];

        $this->validator->validate($data, [
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'email' => 'required|email',
            'phone' => 'required|min:10|max:15',
            'password' => 'required|min:8|confirmed',
            'role' => 'required',
        ]);

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/register', 'Please fix the errors below.', 'error');
            return;
        }

        $existing = User::findByEmail($data['email']);
        if ($existing) {
            $this->redirectWith('/register', 'An account with this email already exists.', 'error');
            return;
        }

        $userId = User::create([
            'uuid' => sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'],
            'status' => 'active',
        ]);

        if (!$userId) {
            $this->redirectWith('/register', 'Registration failed. Please try again.', 'error');
            return;
        }

        $user = User::find($userId);
        $this->session->setUser($user);
        $this->session->regenerate();

        $this->session->setFlash('success', 'Welcome to Celer Market! Your account has been created.');

        if ($data['role'] === 'vendor') {
            $this->redirect('/vendor/store');
        }

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Middleware::auth();
        $this->session->destroy();
        $this->redirect('/');
    }

    public function forgotPasswordForm(): void
    {
        Middleware::guest();
        $this->renderView('auth/forgot');
    }

    public function forgotPassword(): void
    {
        Middleware::guest();
        Middleware::csrf();

        $email = Validator::sanitizeEmail($this->getParam('email', ''));

        $this->validator->validate(['email' => $email], ['email' => 'required|email']);

        if ($this->validator->fails()) {
            $this->redirectWith('/forgot-password', 'Please provide a valid email address.', 'error');
            return;
        }

        $user = User::findByEmail($email);
        if (!$user) {
            $this->redirectWith('/forgot-password', 'If that email exists, a reset link has been sent.', 'success');
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $db = Database::getInstance();
        $db->query(
            "DELETE FROM password_resets WHERE user_id = :user_id",
            ['user_id' => $user->id]
        );
        $db->insert('password_resets', [
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        $this->session->setFlash(
            'success',
            'Password reset link sent to your email. (In production, an email would be sent. For testing, use: /reset-password/' . $token . ')'
        );
        $this->redirect('/login');
    }

    public function resetPasswordForm(string $token): void
    {
        Middleware::guest();

        $db = Database::getInstance();
        $reset = $db->fetch(
            "SELECT pr.*, u.email FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = :token AND pr.expires_at > NOW() AND pr.used_at IS NULL",
            ['token' => $token]
        );

        if (!$reset) {
            $this->redirectWith('/login', 'Invalid or expired password reset token.', 'error');
            return;
        }

        $this->renderView('auth/reset', ['token' => $token, 'email' => $reset->email]);
    }

    public function resetPassword(): void
    {
        Middleware::guest();
        Middleware::csrf();

        $token = $this->getParam('token', '');
        $password = $this->getParam('password', '');
        $passwordConfirmation = $this->getParam('password_confirmation', '');

        $this->validator->validate(
            ['token' => $token, 'password' => $password, 'password_confirmation' => $passwordConfirmation],
            ['token' => 'required', 'password' => 'required|min:8|confirmed']
        );

        if ($this->validator->fails()) {
            $this->redirectWith('/reset-password/' . $token, 'Please fix the errors below.', 'error');
            return;
        }

        $db = Database::getInstance();
        $reset = $db->fetch(
            "SELECT pr.*, u.email FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = :token AND pr.expires_at > NOW() AND pr.used_at IS NULL",
            ['token' => $token]
        );

        if (!$reset) {
            $this->redirectWith('/login', 'Invalid or expired password reset token.', 'error');
            return;
        }

        $db->update('users', [
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ], 'id = :id', ['id' => $reset->user_id]);

        $db->update('password_resets', [
            'used_at' => date('Y-m-d H:i:s'),
        ], 'token = :token', ['token' => $token]);

        $this->redirectWith('/login', 'Your password has been reset successfully. Please login with your new password.', 'success');
    }

    public function redirectToGoogle(): void
    {
        Middleware::guest();

        $clientId = getenv('GOOGLE_CLIENT_ID');
        $redirectUri = getenv('GOOGLE_REDIRECT_URI') ?: (getenv('APP_URL') ?: 'http://localhost/multi-vendor-store') . '/auth/google/callback';

        if (!$clientId) {
            $this->redirectWith('/login', 'Google login is not configured.', 'error');
            return;
        }

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'state' => bin2hex(random_bytes(16)),
        ];

        $this->session->set('google_oauth_state', $params['state']);
        $this->redirect('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
    }

    public function handleGoogleCallback(): void
    {
        Middleware::guest();

        $code = $this->getParam('code');
        $state = $this->getParam('state');
        $storedState = $this->session->get('google_oauth_state');
        $error = $this->getParam('error');

        $this->session->remove('google_oauth_state');

        if ($error) {
            $this->redirectWith('/login', 'Google login was cancelled.', 'error');
            return;
        }

        if (!$code || !$state || $state !== $storedState) {
            $this->redirectWith('/login', 'Invalid request. Please try again.', 'error');
            return;
        }

        $clientId = getenv('GOOGLE_CLIENT_ID');
        $clientSecret = getenv('GOOGLE_CLIENT_SECRET');
        $redirectUri = getenv('GOOGLE_REDIRECT_URI') ?: (getenv('APP_URL') ?: 'http://localhost/multi-vendor-store') . '/auth/google/callback';

        if (!$clientId || !$clientSecret) {
            $this->redirectWith('/login', 'Google login is not configured.', 'error');
            return;
        }

        $tokenData = $this->exchangeCodeForToken($code, $clientId, $clientSecret, $redirectUri);
        if (!$tokenData || !isset($tokenData['id_token'])) {
            $this->redirectWith('/login', 'Failed to authenticate with Google.', 'error');
            return;
        }

        $payload = $this->decodeIdToken($tokenData['id_token']);
        if (!$payload) {
            $this->redirectWith('/login', 'Failed to verify Google identity.', 'error');
            return;
        }

        $googleId = $payload['sub'];
        $email = $payload['email'] ?? '';
        $firstName = $payload['given_name'] ?? explode('@', $email)[0];
        $lastName = $payload['family_name'] ?? '';
        $avatar = $payload['picture'] ?? '';

        if (!$email) {
            $this->redirectWith('/login', 'Google account has no email address.', 'error');
            return;
        }

        $user = User::findBySocialId('google', $googleId);

        if (!$user) {
            $existing = User::findByEmail($email);
            if ($existing) {
                $db = Database::getInstance();
                $db->update('users', [
                    'provider' => 'google',
                    'social_id' => $googleId,
                    'avatar' => $avatar ?: $existing->avatar,
                ], 'id = :id', ['id' => $existing->id]);
                $user = User::find($existing->id);
            } else {
                $userId = User::create([
                    'uuid' => sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                        mt_rand(0, 0x0fff) | 0x4000,
                        mt_rand(0, 0x3fff) | 0x8000,
                        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'provider' => 'google',
                    'social_id' => $googleId,
                    'avatar' => $avatar,
                    'role' => 'customer',
                    'status' => 'active',
                ]);
                if (!$userId) {
                    $this->redirectWith('/login', 'Account creation failed.', 'error');
                    return;
                }
                $user = User::find($userId);
            }
        }

        $this->session->setUser($user);
        $this->session->regenerate();
        $this->session->setFlash('success', 'Logged in with Google successfully.');

        $intended = $this->session->get('intended_url', '');
        if ($intended) {
            $this->session->remove('intended_url');
            $this->redirect($intended);
        }

        $this->redirect('/dashboard');
    }

    private function exchangeCodeForToken(string $code, string $clientId, string $clientSecret, string $redirectUri): ?array
    {
        $url = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        return json_decode($response, true);
    }

    private function decodeIdToken(string $idToken): ?array
    {
        $parts = explode('.', $idToken);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!$payload || !isset($payload['sub'])) {
            return null;
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }
}
