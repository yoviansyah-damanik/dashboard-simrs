<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Repository\UserRepository;
use Illuminate\Validation\Rules\Password;
use Jantinnerezo\LivewireAlert\LivewireAlert;

/**
 * Form bagi admin untuk mengatur ulang kata sandi pengguna lain dari halaman Manajemen Pengguna.
 */
class ForgotPassword extends Component
{
    use LivewireAlert;

    public ?string $userId = null;
    public ?string $name = null;

    public string $newPassword = '';
    public string $rePassword = '';

    #[On('setUserToResetPassword')]
    public function setUserToResetPassword(string $id)
    {
        $this->resetValidation();
        $this->reset(['newPassword', 'rePassword']);

        $user = User::find($id);

        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
        }
    }

    public function updated($attribute)
    {
        $this->validateOnly($attribute, [
            'newPassword' => ['required', Password::min(8)->mixedCase()->numbers()],
            'rePassword' => 'required|same:newPassword',
        ], [], [
            'newPassword' => 'Kata Sandi Baru',
            'rePassword' => 'Ulangi Kata Sandi Baru',
        ]);
    }

    public function resetPassword()
    {
        $this->validate([
            'newPassword' => ['required', Password::min(8)->mixedCase()->numbers()],
            'rePassword' => 'required|same:newPassword',
        ], [], [
            'newPassword' => 'Kata Sandi Baru',
            'rePassword' => 'Ulangi Kata Sandi Baru',
        ]);

        try {
            UserRepository::resetPassword($this->userId, $this->newPassword);

            $this->alert('success', 'Berhasil mengatur ulang kata sandi pengguna.');
            $this->dispatch('toggle-reset-password-user-modal');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('pages.users.forgot-password');
    }
}
