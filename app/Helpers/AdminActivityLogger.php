<?php

namespace App\Helpers;

use App\Models\AdminActivityLog;

class AdminActivityLogger
{
    /**
     * Log admin activity
     *
     * @param  \App\Models\Admin|null  $admin
     * @param  mixed  $subject
     */
    public static function log(string $action, $admin = null, $subject = null, array $meta = []): void
    {
        AdminActivityLog::create([
            'admin_id' => $admin?->id,
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'route' => request()->path(),
            'method' => request()->method(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'meta' => ! empty($meta) ? json_encode($meta) : null, // <-- itt
        ]);
    }

    public static function invitationSent($admin, $invitation)
    {
        self::log('admin_invitation_sent', $admin, $invitation, ['email' => $invitation->email]);
    }

    public static function registeredViaInvitation($admin)
    {
        self::log('admin_registered_via_invitation', $admin, $admin, ['email' => $admin->email, 'status' => $admin->status]);
    }

    public static function approved($admin, $subject)
    {
        self::log('admin_approved', $admin, $subject);
    }

    public static function rejected($admin, $subject)
    {
        self::log('admin_rejected', $admin, $subject);
    }

    public static function login($admin)
    {
        self::log('admin_login', $admin);
    }

    public static function logout($admin)
    {
        self::log('admin_logout', $admin);
    }

    public static function passwordChanged($admin)
    {
        self::log('admin_password_changed', $admin);
    }

    public static function deleted($admin, $subject)
    {
        self::log('admin_deleted', $admin, $subject);
    }
}
