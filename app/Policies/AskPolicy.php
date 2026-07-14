<?php

namespace App\Policies;

use App\Models\Ask;
use App\Models\User;

class AskPolicy
{
    public function update(User $user, Ask $ask): bool
    {
        return $user->id === $ask->user_id;
    }

    public function delete(User $user, Ask $ask): bool
    {
        return $user->id === $ask->user_id;
    }

    public function view(User $user, Ask $ask): bool
    {
        return $user->id === $ask->user_id;
    }
}
