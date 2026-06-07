<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Repository\UserRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Delete extends Component
{
    use LivewireAlert;

    public ?string $userId = null;
    public ?string $name = null;

    #[On('setUserToDelete')]
    public function setUserToDelete(string $id)
    {
        $user = User::find($id);

        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
        }
    }

    public function delete()
    {
        try {
            UserRepository::delete($this->userId);

            $this->alert('success', 'Berhasil menghapus pengguna.');
            $this->dispatch('refreshUsers');
            $this->dispatch('toggle-delete-user-modal');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('pages.users.delete');
    }
}
