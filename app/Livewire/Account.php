<?php

namespace App\Livewire;

use Illuminate\Validation\Rules\Password;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Account extends Component
{
    use LivewireAlert;
    use WithPagination;

    public string $username;
    public string $name;
    public string $email;

    public string $newPassword = '';
    public string $rePassword = '';
    public string $currentPassword = '';

    public array $types;
    public string $type;

    public function mount()
    {
        $user = auth()->user();
        $this->username = $user->username ?? '';
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';

        $this->types = [
            ['value' => 'account', 'title' => 'Informasi Akun', 'icon' => 'icon-[solar--user-circle-bold-duotone]', 'desc' => 'Perbarui profil dan kontak identitas Anda'],
            ['value' => 'password', 'title' => 'Keamanan Sandi', 'icon' => 'icon-[solar--lock-password-bold-duotone]', 'desc' => 'Ganti kata sandi untuk keamanan akun'],
            ['value' => 'history', 'title' => 'Riwayat Login', 'icon' => 'icon-[solar--shield-check-bold-duotone]', 'desc' => 'Catatan aktivitas login dan perangkat'],
        ];
        $this->type = 'account';
    }

    public function switchTab(string $type): void
    {
        $this->type = $type;
        $this->resetPage();
        $this->resetValidation();
        $this->newPassword = '';
        $this->rePassword = '';
        $this->currentPassword = '';
    }

    public function render()
    {
        $user = auth()->user();
        $histories = $user ? $user->histories()->orderByDesc('login_at')->paginate(10) : collect();
        $totalLoginCount = $user ? $user->histories()->count() : 0;
        $latestLogin = $user ? $user->histories()->orderByDesc('login_at')->first() : null;

        return view('pages.account', [
            'histories' => $histories,
            'totalLoginCount' => $totalLoginCount,
            'latestLogin' => $latestLogin,
        ]);
    }

    public function updated($attribute)
    {
        $this->validateOnly(
            $attribute,
            [
                'name' => 'required|string|max:45',
                'email' => 'required|email:dns|unique:users,email,' . auth()->user()->id,
                'newPassword' => [
                    'required',
                    Password::min(8)->mixedCase()->numbers()
                ],
                'rePassword' => 'required|same:newPassword',
                'currentPassword' => 'required|current_password',
            ],
            [],
            [
                'name' => 'Nama Lengkap',
                'email' => 'Email',
                'newPassword' => 'Kata Sandi Baru',
                'rePassword' => 'Ulangi Kata Sandi Baru',
                'currentPassword' => 'Kata Sandi Saat Ini',
            ]
        );
    }

    public function saveAccount()
    {
        $this->validate(
            [
                // 'username' => 'required|string|max:20',
                'name' => 'required|string|max:45',
                'email' => 'required|email:dns|unique:users,email,' . auth()->user()->id,
            ],
            [],
            [
                // 'username' => 'Nama Pengguna',
                'name' => 'Nama Lengkap',
                'email' => 'Email'
            ]
        );

        try {
            auth()->user()->update([
                // 'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
            ]);
            $this->alert('success', 'Berhasil memperbaharui akun.');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function savePassword()
    {
        $this->validate([
            'newPassword' => [
                'required',
                Password::min(8)->mixedCase()->numbers()
            ],
            'rePassword' => 'required|same:newPassword',
            'currentPassword' => 'required|current_password',
        ], [], [
            'newPassword' => 'Kata Sandi Baru',
            'rePassword' => 'Ulangi Kata Sandi Baru',
            'currentPassword' => 'Kata Sandi Saat Ini',
        ]);

        try {
            auth()->user()->update([
                'password' => bcrypt($this->newPassword),
            ]);

            $this->alert('success', 'Berhasil memperbaharui kata sandi.');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }
}
