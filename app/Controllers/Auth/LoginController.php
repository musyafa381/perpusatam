<?php

namespace App\Controllers\Auth;

use CodeIgniter\Shield\Controllers\LoginController as ShieldLoginController;
use CodeIgniter\HTTP\RedirectResponse;

class LoginController extends ShieldLoginController
{
    /**
     * Maximum failed login attempts allowed before lockout.
     */
    protected int $maxAttempts = 4;

    /**
     * Lockout duration in seconds (1 minute).
     */
    protected int $lockoutSeconds = 60;

    /**
     * Display the login view with rate-limiting status.
     */
    public function loginView()
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        $ip = $this->request->getIPAddress();
        $cacheKey = 'login_attempts_' . md5($ip);
        $lockoutKey = 'login_lockout_' . md5($ip);

        $lockoutTime = (int) (cache($lockoutKey) ?? session()->get('login_lockout_until') ?? 0);
        $currentTime = time();
        $remainingLockout = max(0, $lockoutTime - $currentTime);

        $attempts = (int) (cache($cacheKey) ?? session()->get('login_failed_attempts') ?? 0);

        return $this->view(setting('Auth.views')['login'], [
            'isLockedOut' => $remainingLockout > 0,
            'remainingLockout' => $remainingLockout,
            'failedAttempts' => $attempts,
            'maxAttempts' => $this->maxAttempts,
        ]);
    }

    /**
     * Attempt login with strict attempt limits (4 attempts -> 1 minute lockout).
     */
    public function loginAction(): RedirectResponse
    {
        $ip = $this->request->getIPAddress();
        $cacheKey = 'login_attempts_' . md5($ip);
        $lockoutKey = 'login_lockout_' . md5($ip);

        $lockoutTime = (int) (cache($lockoutKey) ?? session()->get('login_lockout_until') ?? 0);
        $currentTime = time();

        // 1. Check if currently locked out
        if ($lockoutTime > $currentTime) {
            $remaining = $lockoutTime - $currentTime;
            return redirect()->back()->withInput()->with('error', "Terlalu banyak percobaan login gagal ({$this->maxAttempts}x). Silakan tunggu {$remaining} detik lagi sebelum mencoba kembali.");
        }

        // 2. Perform validation first
        $rules = $this->getValidationRules();
        if (! $this->validateData($this->request->getPost(), $rules, [], config('Auth')->DBGroup)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        /** @var array $credentials */
        $credentials             = $this->request->getPost(setting('Auth.validFields')) ?? [];
        $credentials             = array_filter($credentials);
        $credentials['password'] = $this->request->getPost('password');
        $remember                = (bool) $this->request->getPost('remember');

        /** @var \CodeIgniter\Shield\Authentication\Authenticators\Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        // 3. Attempt authentication
        $result = $authenticator->remember($remember)->attempt($credentials);

        if (! $result->isOK()) {
            // Increment failed attempts
            $attempts = (int) (cache($cacheKey) ?? session()->get('login_failed_attempts') ?? 0) + 1;
            
            if ($attempts >= $this->maxAttempts) {
                // Trigger 1 minute lockout
                $newLockout = time() + $this->lockoutSeconds;
                
                cache()->save($lockoutKey, $newLockout, $this->lockoutSeconds);
                session()->set('login_lockout_until', $newLockout);
                
                // Reset failed attempt counter during lockout
                cache()->delete($cacheKey);
                session()->remove('login_failed_attempts');

                return redirect()->back()->withInput()->with('error', "Gagal login. Anda telah salah memasukkan data sebanyak 4 kali. Silakan tunggu 1 menit (60 detik) sebelum mencoba kembali.");
            }

            // Save incremented attempts
            cache()->save($cacheKey, $attempts, 300); // 5 mins TTL
            session()->set('login_failed_attempts', $attempts);

            $sisa = $this->maxAttempts - $attempts;
            $reason = $result->reason() ?: 'Email atau password yang Anda masukkan salah.';
            
            return redirect()->back()->withInput()->with('error', "{$reason} (Sisa percobaan: {$sisa}x lagi sebelum terkunci 1 menit).");
        }

        // 4. Reset counter on successful login
        cache()->delete($cacheKey);
        cache()->delete($lockoutKey);
        session()->remove(['login_failed_attempts', 'login_lockout_until']);

        if ($authenticator->hasAction()) {
            return redirect()->route('auth-action-show')->withCookies();
        }

        return redirect()->to(config('Auth')->loginRedirect())->withCookies();
    }
}
