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
            'is_active' => (bool) $user->is_active,
            'created_at' => $user->created_at,
            'last_login_at' => $user->last_login_at
        ];
    }

    public static function getUser(string $id): ?array
    {
        $user = User::with('roles')->find($id);

        return $user ? (new self)->mapping($user) : null;
    }

    public static function create(array $data): User
    {
        $user = User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'as' => $data['as'],
            'password' => bcrypt($data['password']),
        ]);

        $user->assignRole($data['role']);

        return $user;
    }

    public static function update(string $id, array $data): User
    {
        $user = User::findOrFail($id);

        $user->update([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'as' => $data['as'],
        ]);

        $user->syncRoles([$data['role']]);

        return $user;
    }

    public static function delete(string $id): void
    {
        User::findOrFail($id)->delete();
    }

    public static function resetPassword(string $id, string $password): void
    {
        User::findOrFail($id)->update([
            'password' => bcrypt($password),
        ]);
    }

    public static function toggleActive(string $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return $user;
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
