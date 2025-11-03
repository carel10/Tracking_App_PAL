<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Division;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Load all settings grouped
        $settings = [
            'authentication' => $this->getAuthenticationSettings(),
            'password_policy' => $this->getPasswordPolicySettings(),
            'account_policy' => $this->getAccountPolicySettings(),
            'session_policy' => $this->getSessionPolicySettings(),
            'email_settings' => $this->getEmailSettings(),
            'system_info' => $this->getSystemInfo(),
        ];

        return view('settings.index', compact('settings'));
    }

    /**
     * Update authentication settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAuthentication(Request $request)
    {
        $data = $request->validate([
            'sso_enabled' => 'nullable|boolean',
            'mfa_enabled' => 'nullable|boolean',
            'mfa_required' => 'nullable|boolean',
        ]);

        Setting::set('sso_enabled', $request->has('sso_enabled') ? 1 : 0, 'authentication', 'boolean');
        Setting::set('mfa_enabled', $request->has('mfa_enabled') ? 1 : 0, 'authentication', 'boolean');
        Setting::set('mfa_required', $request->has('mfa_required') ? 1 : 0, 'authentication', 'boolean');

        $this->logSettingChange('authentication', $data);

        return redirect()->route('settings.index')
            ->with('success', 'Authentication settings updated successfully.')
            ->with('active_tab', 'authentication');
    }

    /**
     * Update password policy settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePasswordPolicy(Request $request)
    {
        $data = $request->validate([
            'min_length' => 'required|integer|min:6|max:32',
            'require_uppercase' => 'nullable|boolean',
            'require_lowercase' => 'nullable|boolean',
            'require_numbers' => 'nullable|boolean',
            'require_symbols' => 'nullable|boolean',
        ]);

        Setting::set('password_min_length', $data['min_length'], 'password_policy', 'integer');
        Setting::set('password_require_uppercase', $request->has('require_uppercase') ? 1 : 0, 'password_policy', 'boolean');
        Setting::set('password_require_lowercase', $request->has('require_lowercase') ? 1 : 0, 'password_policy', 'boolean');
        Setting::set('password_require_numbers', $request->has('require_numbers') ? 1 : 0, 'password_policy', 'boolean');
        Setting::set('password_require_symbols', $request->has('require_symbols') ? 1 : 0, 'password_policy', 'boolean');

        $this->logSettingChange('password_policy', $data);

        return redirect()->route('settings.index')
            ->with('success', 'Password policy settings updated successfully.')
            ->with('active_tab', 'password_policy');
    }

    /**
     * Update account policy settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAccountPolicy(Request $request)
    {
        $data = $request->validate([
            'max_login_attempts' => 'required|integer|min:3|max:10',
            'lockout_duration' => 'required|integer|min:5|max:1440', // minutes
        ]);

        Setting::set('account_max_login_attempts', $data['max_login_attempts'], 'account_policy', 'integer');
        Setting::set('account_lockout_duration', $data['lockout_duration'], 'account_policy', 'integer');

        $this->logSettingChange('account_policy', $data);

        return redirect()->route('settings.index')
            ->with('success', 'Account policy settings updated successfully.')
            ->with('active_tab', 'account_policy');
    }

    /**
     * Update session policy settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSessionPolicy(Request $request)
    {
        $data = $request->validate([
            'session_timeout' => 'required|integer|min:15|max:1440', // minutes
            'max_devices' => 'required|integer|min:1|max:20',
        ]);

        Setting::set('session_timeout', $data['session_timeout'], 'session_policy', 'integer');
        Setting::set('session_max_devices', $data['max_devices'], 'session_policy', 'integer');

        // Also update cache
        Cache::forever('session_timeout_minutes', $data['session_timeout']);
        Cache::forever('session_limit_per_user', $data['max_devices']);

        $this->logSettingChange('session_policy', $data);

        return redirect()->route('settings.index')
            ->with('success', 'Session policy settings updated successfully.')
            ->with('active_tab', 'session_policy');
    }

    /**
     * Update email settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEmailSettings(Request $request)
    {
        $data = $request->validate([
            'mail_mailer' => 'required|string|in:smtp,sendmail,mailgun,ses',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'email_settings', 'string');
        }

        $dataToLog = $data;
        unset($dataToLog['mail_password']);
        $this->logSettingChange('email_settings', $dataToLog);

        return redirect()->route('settings.index')
            ->with('success', 'Email settings updated successfully.')
            ->with('active_tab', 'email_settings');
    }

    /**
     * Export data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $type = $request->validate(['type' => 'required|in:users,roles,permissions,all']);

        $data = [];

        if ($request->type === 'users' || $request->type === 'all') {
            $data['users'] = User::with('division', 'roles')->get()->toArray();
        }

        if ($request->type === 'roles' || $request->type === 'all') {
            $data['roles'] = Role::with('permissions', 'division')->get()->toArray();
        }

        if ($request->type === 'permissions' || $request->type === 'all') {
            $data['permissions'] = Permission::all()->toArray();
        }

        if ($request->type === 'all') {
            $data['divisions'] = Division::all()->toArray();
        }

        $filename = 'export_' . $request->type . '_' . date('Y-m-d_His') . '.json';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!File::exists(storage_path('app/exports'))) {
            File::makeDirectory(storage_path('app/exports'), 0755, true);
        }

        File::put($filepath, json_encode($data, JSON_PRETTY_PRINT));

        // Log export
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'data_export',
            'target_table' => 'settings',
            'target_id' => null,
            'details' => [
                'export_type' => $request->type,
                'filename' => $filename,
            ],
            'created_at' => now(),
        ]);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Get authentication settings.
     *
     * @return array
     */
    private function getAuthenticationSettings()
    {
        return [
            'sso_enabled' => Setting::get('sso_enabled', false),
            'mfa_enabled' => Setting::get('mfa_enabled', false),
            'mfa_required' => Setting::get('mfa_required', false),
        ];
    }

    /**
     * Get password policy settings.
     *
     * @return array
     */
    private function getPasswordPolicySettings()
    {
        return [
            'min_length' => Setting::get('password_min_length', 8),
            'require_uppercase' => Setting::get('password_require_uppercase', true),
            'require_lowercase' => Setting::get('password_require_lowercase', true),
            'require_numbers' => Setting::get('password_require_numbers', true),
            'require_symbols' => Setting::get('password_require_symbols', false),
        ];
    }

    /**
     * Get account policy settings.
     *
     * @return array
     */
    private function getAccountPolicySettings()
    {
        return [
            'max_login_attempts' => Setting::get('account_max_login_attempts', 5),
            'lockout_duration' => Setting::get('account_lockout_duration', 30),
        ];
    }

    /**
     * Get session policy settings.
     *
     * @return array
     */
    private function getSessionPolicySettings()
    {
        return [
            'session_timeout' => Cache::get('session_timeout_minutes', Setting::get('session_timeout', 120)),
            'max_devices' => Cache::get('session_limit_per_user', Setting::get('session_max_devices', 5)),
        ];
    }

    /**
     * Get email settings.
     *
     * @return array
     */
    private function getEmailSettings()
    {
        return [
            'mail_mailer' => Setting::get('mail_mailer', config('mail.default', 'smtp')),
            'mail_host' => Setting::get('mail_host', config('mail.mailers.smtp.host', '')),
            'mail_port' => Setting::get('mail_port', config('mail.mailers.smtp.port', 587)),
            'mail_username' => Setting::get('mail_username', config('mail.mailers.smtp.username', '')),
            'mail_password' => Setting::get('mail_password', ''),
            'mail_encryption' => Setting::get('mail_encryption', config('mail.mailers.smtp.encryption', 'tls')),
            'mail_from_address' => Setting::get('mail_from_address', config('mail.from.address', '')),
            'mail_from_name' => Setting::get('mail_from_name', config('mail.from.name', '')),
        ];
    }

    /**
     * Get system info.
     *
     * @return array
     */
    private function getSystemInfo()
    {
        return [
            'app_name' => config('app.name', 'Tracking App'),
            'app_version' => '1.0.0',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => config('app.env', 'production'),
            'debug_mode' => config('app.debug', false),
            'database_driver' => config('database.default', 'mysql'),
            'timezone' => config('app.timezone', 'UTC'),
            'server_time' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Log setting change.
     *
     * @param  string  $group
     * @param  array  $data
     * @return void
     */
    private function logSettingChange($group, $data)
    {
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'settings_updated',
            'target_table' => 'settings',
            'target_id' => null,
            'details' => [
                'group' => $group,
                'changes' => $data,
            ],
            'created_at' => now(),
        ]);
    }
}

