<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Repository\UserRepository;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Password;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Create extends Component
{
    use LivewireAlert;

    public ?string $userId = null;

    public string $username = '';
    public string $name = '';
    public string $email = '';
    public string $as = '';
    public string $password = '';
    public string $role = '';

    public array $roles = [];

    public function mount()
    {
        $this->roles = Role::get()
            ->map(fn($item) => ['title' => $item->name, 'value' => $item->name])
            ->toArray();
    }

    /**
     * Menyiapkan form untuk mode tambah (tanpa $id) atau ubah (dengan $id) pengguna.
     */
    #[On('setUserForm')]
    public function setUserForm(?string $id = null)
    {
        $this->resetValidation();
        $this->reset(['userId', 'username', 'name', 'email', 'as', 'password', 'role']);

        if ($id) {
            $user = User::with('roles')->find($id);

            if ($user) {
                $this->userId = $user->id;
                $this->username = $user->username;
                $this->name = $user->name;
                $this->email = $user->email;
                $this->as = $user->as;
                $this->role = $user->roleName;
            }
        }
    }

    public function render()
    {
        return view('pages.users.create');
    }

    private function rules(): array
    {
        return [
            'username' => 'required|string|max:20|unique:users,username,' . $this->userId,
            'name' => 'required|string|max:45',
            'email' => 'required|email:dns|unique:users,email,' . $this->userId,
            'as' => 'required|string|max:45',
            'password' => $this->userId
                ? 'nullable|' . Password::min(8)->mixedCase()->numbers()
                : ['required', Password::min(8)->mixedCase()->numbers()],
            'role' => 'required|string',
        ];
    }

    private function attributes(): array
    {
        return [
            'username' => 'Nama Pengguna',
            'name' => 'Nama Lengkap',
            'email' => 'Email',
            'as' => 'Jabatan',
            'password' => 'Kata Sandi',
            'role' => 'Peran',
        ];
    }

    public function updated($attribute)
    {
        $this->validateOnly($attribute, $this->rules(), [], $this->attributes());
    }

    public function save()
    {
        $this->validate($this->rules(), [], $this->attributes());

        try {
            if ($this->userId) {
                UserRepository::update($this->userId, [
                    'username' => $this->username,
                    'name' => $this->name,
                    'email' => $this->email,
                    'as' => $this->as,
                    'role' => $this->role,
                ]);

                if ($this->password) {
                    UserRepository::resetPassword($this->userId, $this->password);
                }

                $this->alert('success', 'Berhasil memperbaharui pengguna.');
            } else {
                UserRepository::create([
                    'username' => $this->username,
                    'name' => $this->name,
                    'email' => $this->email,
                    'as' => $this->as,
                    'password' => $this->password,
                    'role' => $this->role,
                ]);

                $this->alert('success', 'Berhasil menambahkan pengguna.');
            }

            $this->dispatch('refreshUsers');
            $this->dispatch('toggle-create-user-modal');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }
}
