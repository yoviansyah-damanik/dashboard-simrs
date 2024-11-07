<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserInterface {}

class UserRepository implements UserInterface
{
    /**
     * Fungsi ini bersifat final. Merubah mapping berakibat pada struktur data yang ditampilkan.
     * Jika ingin merubah isi data, ubahlah pada fungsi reconstruction().
     * @param $patient array
     * @return array
     */
    private function mapping(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'as' => $user->as,
            'role' => $user->roleName,
            'created_at' => $user->created_at,
            'last_login_at' => $user->last_login_at
        ];
    }

    public static function getAll(
        ?string $search = null,
        string | array  $role = 'semua',
        int $limit = 10
    ): array | LengthAwarePaginator {
        $result = User::with('roles')
            ->when(
                $role != 'semua',
                fn($q) => $q->role($role)
            )
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->limit($limit);

        return tap($result->paginate($limit), function ($paginatedInstance) {
            return $paginatedInstance->getCollection()->transform(function ($value) {
                return (new self)
                    ->mapping($value);
            });
        });
    }
}
