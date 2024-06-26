<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Actions;

use App\Models\User;
use Filament\Pages\Actions\Action;

class ActivateUserAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'activate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(function (User $record) {
            if ($record->isActive()) {
                return false;
            }

            if (auth()->user()->is($record)) {
                return false;
            }

            return auth()->user()->isAdmin() || auth()->user()->isCoordinator();
        });

        $this->label(__('user.action.activate'));

        $this->color('success');

        $this->icon('heroicon-s-badge-check');

        $this->modalHeading(__('user.action_activate_confirm.title'));

        $this->modalSubheading(__('user.action_activate_confirm.text'));

        $this->modalButton(__('user.action_activate_confirm.action'));

        $this->modalWidth('md');

        $this->action(function (User $record) {
            $record->activate();
            $this->success();
        });

        $this->successNotificationTitle(__('user.action_activate_confirm.success'));
    }
}
