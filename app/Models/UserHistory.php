<?php

namespace App\Models;

use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHistory extends Model
{
    protected $fillable = [
        'user_id',
        'login_at',
        'ip_address',
        'browser',
        'platform',
        'device',
        'device_type',
        'is_robot',
    ];
    public $timestamps = false;

    public static function booting()
    {
        parent::booting();
        $agent = new Agent();
        static::creating(function ($model) use ($agent) {
            $model->user_id = Auth::id();
            $model->login_at = Carbon::now();
            $model->ip_address = request()->ip();
            $model->browser = $agent->browser() . ' ' . $agent->version($agent->browser());
            $model->platform = $agent->platform() . ' ' . $agent->version($agent->platform());
            $model->device = $agent->device();
            $model->device_type = $agent->isDesktop() ? 'desktop' : ($agent->isMobile() ? 'mobile' : 'tablet');
            $model->is_robot = $agent->isRobot();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
