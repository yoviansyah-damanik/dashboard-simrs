<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Repository\UserRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ActivationMenu extends Component
{
    use LivewireAlert;

    public ?string $userId = null;
    public ?string $name = null;
    public bool $isActive = true;

    #[On('setUserToActivate')]
    public function setUserToActivate(string $id)
    {
        $user = User::find($id);

        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->isActive = (bool) $user->is_active;
        }
    }

    public function toggle()
    {
        try {
            $user = UserRepository::toggleActive($this->userId);
            $this->isActive = (bool) $user->is_active;

            $this->alert('success', $this->isActive ? 'Berhasil mengaktifkan pengguna.' : 'Berhasil menonaktifkan pengguna.');
            $this->dispatch('refreshUsers');
            $this->dispatch('toggle-activation-user-modal');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('pages.users.activation-menu');
    }
}
