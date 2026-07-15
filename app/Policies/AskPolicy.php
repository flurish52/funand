<?php

namespace App\Policies;

use App\Models\Ask;
use App\Models\User;

class AskPolicy
{
    public function update(User $user, Ask $ask): bool
    {
        return $this->ownsAsk($user, $ask);
    }

    public function delete(User $user, Ask $ask): bool
    {
        return $this->ownsAsk($user, $ask);
    }

    public function view(User $user, Ask $ask): bool
    {
        return $this->ownsAsk($user, $ask);
    }

    private function ownsAsk(User $user, Ask $ask): bool
    {
        return (int) $user->id === (int) $ask->user_id;
    }
}
