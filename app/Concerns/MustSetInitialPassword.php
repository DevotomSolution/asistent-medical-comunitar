<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Notifications\WelcomeNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait MustSetInitialPassword
{
    protected static function bootMustSetInitialPassword(): void
    {
        static::creating(function (self $user) {
            if (! $user->password) {
                $user->password = Hash::make(Str::random(128));
            }
        });

        static::created(function (self $user) {
            $user->sendWelcomeNotification();
        });

        static::updating(function (self $user) {
            if ($user->isDirty('password') && ! $user->hasSetPassword()) {
                $user->markPasswordAsSet();
            }
        });
    }

    public function hasSetPassword(): bool
    {
        return ! \is_null($this->password_set_at);
    }

    public function markPasswordAsSet(): self
    {
        return $this->forceFill([
            'password_set_at' => now(),
        ]);
    }

    public function sendWelcomeNotification(): void
    {
        $this->notify(new WelcomeNotification);
    }

    public function scopeOnlyInvited(Builder $query): Builder
    {
        return $query->whereNull('password_set_at');
    }
}
