<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tampilkan form login dengan toggle User / Bank Sampah.
     * GET /login
     */
    public function login()
    {
        // Kalau sudah login, redirect ke dashboard sesuai role
        if (session()->get('user_id')) {
            return $this->redirectToDashboard(session()->get('role'));
        }

        return view('auth/login');
    }

    /**
     * Proses login.
     * POST /login/attempt
     *
     * @field email         string
     * @field password      string
     * @field role_toggle   'user' | 'banksampah'  (dari radio/select di view)
     */
    public function attemptLogin()
    {
        $rules = [
            'email'      => 'required|valid_email',
            'password'   => 'required|min_length[6]',
            'role_toggle' => 'required|in_list[user,banksampah]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Input tidak valid. Periksa email, password, dan pilihan role.');
        }

        $email      = trim((string) $this->request->getPost('email'));
        $password   = (string) $this->request->getPost('password');
        $roleToggle = (string) $this->request->getPost('role_toggle');

        // Cari user by email
        $user = $this->userModel->where('email', $email)->first();

        if (! $user) {
            return redirect()->back()->withInput()
                ->with('error', 'Email atau password salah.');
        }

        // Cek password hash
        if (! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Email atau password salah.');
        }
        // Cek role: user yang login harus sesuai toggle
        // (admin TIDAK bisa login lewat form publik, sesuai Q1 Opsi B)
        if ($user['role'] !== $roleToggle) {
            return redirect()->back()->withInput()
                ->with('error', 'Akun ini bukan ' . ($roleToggle === 'banksampah' ? 'Bank Sampah' : 'User') . '. Periksa pilihan role Anda.');
        }
        session()->set([
            'user_id' => $user['id'],
            'role'    => $user['role'],
            'nama'    => $user['name'],
        ]);

        return $this->redirectToDashboard($user['role']);
    }

    /**
     * Tampilkan form register (role user saja).
     * GET /register
     */
    public function register()
    {
        if (session()->get('user_id')) {
            return $this->redirectToDashboard(session()->get('role'));
        }

        return view('auth/register');
    }

    /**
     * Proses register. Role hardcode 'user' (Q1 keputusan final).
     * POST /register/attempt
     *
     * @field name          string
     * @field email         string
     * @field password      string
     * @field password_confirm string
     */
    public function attemptRegister()
    {
        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|max_length[100]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'password' => [
                'min_length' => 'Password minimal 8 karakter.',
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi password tidak cocok.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()
                ->with('error', 'Validasi gagal. Periksa input Anda.')
                ->with('errors', $this->validator->getErrors());
        }

        $name     = trim((string) $this->request->getPost('name'));
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        // Cek email unik — defense in depth (DB juga unique)
        $existing = $this->userModel->where('email', $email)->first();
        if ($existing) {
            return redirect()->back()->withInput()
                ->with('error', 'Email sudah terdaftar. Silakan login.');
        }

        // Insert user baru, role hardcode 'user'
        $newId = $this->userModel->insert([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'user',
        ], false); // false = return insertID, jangan return row

        if (! $newId) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal membuat akun. Coba lagi.');
        }

        // Auto-login: set session
        session()->set([
            'user_id' => $newId,
            'role'    => 'user',
            'nama'    => $name,
        ]);

        return redirect()->to('/dashboard/user')
            ->with('success', 'Selamat datang, ' . $name . '! Akun EcoFriend Anda sudah aktif.');
    }

    /**
     * Logout: destroy session, redirect ke login.
     * GET /logout (atau POST — dibuat GET untuk simplicity, bisa diubah)
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Helper: redirect ke dashboard sesuai role.
     * @param string $role 'admin' | 'banksampah' | 'user'
     */
    private function redirectToDashboard(string $role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->to('/dashboard/admin');
            case 'banksampah':
                return redirect()->to('/dashboard/banksampah');
            case 'user':
            default:
                return redirect()->to('/dashboard/user');
        }
    }
}
