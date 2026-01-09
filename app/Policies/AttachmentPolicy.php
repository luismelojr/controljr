<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;

class AttachmentPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attachment $attachment): bool
    {
        return $attachment->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attachment $attachment): bool
    {
        return $attachment->user_id === $user->id;
    }
}
