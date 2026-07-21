<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * PasswordController — handle Ubah Password untuk user yang sedang login.
 *
 * Method:
 *   - GET  /password/change        : tampilkan form ubah password
 *   - POST /password/change        : validasi + update password
 *
 * Route filter: 'auth' (harus login).
 */
class PasswordController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * GET /password/change
     */
    public function change()
    {
        return view('password/change');
    }

    /**
     * POST /password/change
     * Validasi:
     *   - password_lama harus cocok dengan hash di DB
     *   - password_baru minimal 8 karakter
     *   - password_baru harus sama dengan password_baru_konfirmasi
     * Update hanya password (kolom name/email/role tidak diubah).
     */
    public function attemptChange()
    {
        $userId    = (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $rules = [
            'password_lama'        => 'required',
            'password_baru'        => 'required|min_length[8]',
            'password_baru_konfirm' => 'required|matches[password_baru]',
        ];
        $messages = [
            'password_baru' => [
                'min_length' => 'Password baru minimal 8 karakter.',
            ],
            'password_baru_konfirm' => [
                'matches' => 'Konfirmasi password baru tidak cocok.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()
                ->with('error', 'Validasi gagal. Periksa input Anda.')
                ->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->find($userId);
        if (! $user) {
            return redirect()->to('/login')->with('error', 'User tidak ditemukan.');
        }

        $passwordLama = (string) $this->request->getPost('password_lama');
        if (! password_verify($passwordLama, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        $this->userModel->update($userId, [
            'password' => password_hash((string) $this->request->getPost('password_baru'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/password/change')->with('success', 'Password berhasil diubah.');
    }
}