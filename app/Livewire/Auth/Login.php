<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Login extends Component
{
    use LivewireAlert;

    public bool $isLoading = false;
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = false;

    public function render()
    {
        $setting = Cache::remember('simrs_setting_profile', 3600, function () {
            try {
                return (array) DB::connection('simrs')
                    ->table('setting')
                    ->select('nama_instansi', 'alamat_instansi', 'kabupaten', 'propinsi', 'kontak', 'email')
                    ->first();
            } catch (\Throwable $e) {
                return [];
            }
        });

        return view('pages.auth.login', [
            'setting' => $setting,
        ])
            ->title('Login')
            ->layout('layouts.auth');
    }

    public function rules()
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
            'rememberMe' => 'nullable',
        ];
    }
    public function login()
    {
        $this->validate();
        try {
            $this->isLoading = true;

            $username = $this->username;
            $password = $this->password;

            $user = User::whereUsername($username)
                ->first();

            if ($user) {
                if (Hash::check($password, $user->password)) {
                    $agent = new Agent();

                    if ($agent->isRobot()) {
                        $this->alert('error', "Jangan ya dek yaaaaa.");
                        return;
                    }

                    Auth::login($user, $this->rememberMe === true);
                    $user->last_login_at = now();
                    $user->save();

                    $user->histories()->create();
                    return $this->redirectIntended(route('home'), false);
                }
            }

            $this->isLoading = false;
            $this->alert('warning', "Tidak ada pengguna ditemukan.");
        } catch (\Exception $e) {
            $this->isLoading = false;
            $this->alert('warning', $e->getMessage());
        } catch (\Throwable $e) {
            $this->isLoading = false;
            $this->alert('warning', $e->getMessage());
        }
    }
}
