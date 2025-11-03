@extends('Layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">System Settings</h4>
    <p class="text-muted">Maintain system security and configuration</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ session('active_tab') == 'authentication' || !session('active_tab') ? 'active' : '' }}" 
                id="authentication-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#authentication" 
                type="button" 
                role="tab">
          <i data-feather="shield" class="icon-sm me-2"></i> Authentication
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ session('active_tab') == 'password_policy' ? 'active' : '' }}" 
                id="password-policy-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#password-policy" 
                type="button" 
                role="tab">
          <i data-feather="lock" class="icon-sm me-2"></i> Password Policy
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ session('active_tab') == 'account_policy' ? 'active' : '' }}" 
                id="account-policy-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#account-policy" 
                type="button" 
                role="tab">
          <i data-feather="user-x" class="icon-sm me-2"></i> Account Policy
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ session('active_tab') == 'session_policy' ? 'active' : '' }}" 
                id="session-policy-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#session-policy" 
                type="button" 
                role="tab">
          <i data-feather="clock" class="icon-sm me-2"></i> Session Policy
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ session('active_tab') == 'email_settings' ? 'active' : '' }}" 
                id="email-settings-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#email-settings" 
                type="button" 
                role="tab">
          <i data-feather="mail" class="icon-sm me-2"></i> Email Settings
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ session('active_tab') == 'system_info' ? 'active' : '' }}" 
                id="system-info-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#system-info" 
                type="button" 
                role="tab">
          <i data-feather="info" class="icon-sm me-2"></i> System Info
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ session('active_tab') == 'backup_export' ? 'active' : '' }}" 
                id="backup-export-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#backup-export" 
                type="button" 
                role="tab">
          <i data-feather="download" class="icon-sm me-2"></i> Backup/Export
        </button>
      </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="settingsTabsContent">
      <!-- Authentication Tab -->
      <div class="tab-pane fade {{ session('active_tab') == 'authentication' || !session('active_tab') ? 'show active' : '' }}" 
           id="authentication" 
           role="tabpanel">
        <form action="{{ route('settings.update-authentication') }}" method="POST">
          @csrf
          @method('PATCH')
          <div class="row">
            <div class="col-md-6">
              <h6 class="mb-3">Single Sign-On (SSO)</h6>
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="sso_enabled" 
                         id="sso_enabled"
                         {{ $settings['authentication']['sso_enabled'] ? 'checked' : '' }}>
                  <label class="form-check-label" for="sso_enabled">
                    Enable SSO Authentication
                  </label>
                </div>
                <small class="text-muted d-block mt-1">Allow users to login using Single Sign-On provider</small>
              </div>
            </div>
            <div class="col-md-6">
              <h6 class="mb-3">Multi-Factor Authentication (MFA)</h6>
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="mfa_enabled" 
                         id="mfa_enabled"
                         {{ $settings['authentication']['mfa_enabled'] ? 'checked' : '' }}>
                  <label class="form-check-label" for="mfa_enabled">
                    Enable MFA
                  </label>
                </div>
                <small class="text-muted d-block mt-1">Allow users to enable two-factor authentication</small>
              </div>
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="mfa_required" 
                         id="mfa_required"
                         {{ $settings['authentication']['mfa_required'] ? 'checked' : '' }}>
                  <label class="form-check-label" for="mfa_required">
                    Require MFA for all users
                  </label>
                </div>
                <small class="text-muted d-block mt-1">Force all users to enable MFA on next login</small>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i data-feather="save" class="icon-sm me-2"></i> Save Authentication Settings
          </button>
        </form>
      </div>

      <!-- Password Policy Tab -->
      <div class="tab-pane fade {{ session('active_tab') == 'password_policy' ? 'show active' : '' }}" 
           id="password-policy" 
           role="tabpanel">
        <form action="{{ route('settings.update-password-policy') }}" method="POST">
          @csrf
          @method('PATCH')
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="min_length" class="form-label">Minimum Password Length <span class="text-danger">*</span></label>
                <input type="number" 
                       class="form-control" 
                       id="min_length" 
                       name="min_length" 
                       min="6" 
                       max="32" 
                       value="{{ $settings['password_policy']['min_length'] }}" 
                       required>
                <small class="text-muted">Minimum characters required (6-32)</small>
              </div>
            </div>
            <div class="col-md-6">
              <h6 class="mb-3">Complexity Requirements</h6>
              <div class="mb-2">
                <div class="form-check">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="require_uppercase" 
                         id="require_uppercase"
                         {{ $settings['password_policy']['require_uppercase'] ? 'checked' : '' }}>
                  <label class="form-check-label" for="require_uppercase">
                    Require uppercase letters (A-Z)
                  </label>
                </div>
              </div>
              <div class="mb-2">
                <div class="form-check">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="require_lowercase" 
                         id="require_lowercase"
                         {{ $settings['password_policy']['require_lowercase'] ? 'checked' : '' }}>
                  <label class="form-check-label" for="require_lowercase">
                    Require lowercase letters (a-z)
                  </label>
                </div>
              </div>
              <div class="mb-2">
                <div class="form-check">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="require_numbers" 
                         id="require_numbers"
                         {{ $settings['password_policy']['require_numbers'] ? 'checked' : '' }}>
                  <label class="form-check-label" for="require_numbers">
                    Require numbers (0-9)
                  </label>
                </div>
              </div>
              <div class="mb-2">
                <div class="form-check">
                  <input class="form-check-input" 
                         type="checkbox" 
                         name="require_symbols" 
                         id="require_symbols"
                         {{ $settings['password_policy']['require_symbols'] ? 'checked' : '' }}>
                  <label class="form-check-label" for="require_symbols">
                    Require special characters (!@#$%^&*)
                  </label>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i data-feather="save" class="icon-sm me-2"></i> Save Password Policy
          </button>
        </form>
      </div>

      <!-- Account Policy Tab -->
      <div class="tab-pane fade {{ session('active_tab') == 'account_policy' ? 'show active' : '' }}" 
           id="account-policy" 
           role="tabpanel">
        <form action="{{ route('settings.update-account-policy') }}" method="POST">
          @csrf
          @method('PATCH')
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="max_login_attempts" class="form-label">Maximum Login Attempts <span class="text-danger">*</span></label>
                <input type="number" 
                       class="form-control" 
                       id="max_login_attempts" 
                       name="max_login_attempts" 
                       min="3" 
                       max="10" 
                       value="{{ $settings['account_policy']['max_login_attempts'] }}" 
                       required>
                <small class="text-muted">Number of failed login attempts before account lockout (3-10)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="lockout_duration" class="form-label">Lockout Duration (Minutes) <span class="text-danger">*</span></label>
                <input type="number" 
                       class="form-control" 
                       id="lockout_duration" 
                       name="lockout_duration" 
                       min="5" 
                       max="1440" 
                       value="{{ $settings['account_policy']['lockout_duration'] }}" 
                       required>
                <small class="text-muted">How long to lock the account after max attempts (5-1440 minutes)</small>
              </div>
            </div>
          </div>
          <div class="alert alert-warning">
            <i data-feather="alert-triangle" class="icon-sm me-2"></i>
            <strong>Warning:</strong> Account will be automatically locked after exceeding the maximum login attempts.
          </div>
          <button type="submit" class="btn btn-primary">
            <i data-feather="save" class="icon-sm me-2"></i> Save Account Policy
          </button>
        </form>
      </div>

      <!-- Session Policy Tab -->
      <div class="tab-pane fade {{ session('active_tab') == 'session_policy' ? 'show active' : '' }}" 
           id="session-policy" 
           role="tabpanel">
        <form action="{{ route('settings.update-session-policy') }}" method="POST">
          @csrf
          @method('PATCH')
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="session_timeout" class="form-label">Session Timeout (Minutes) <span class="text-danger">*</span></label>
                <input type="number" 
                       class="form-control" 
                       id="session_timeout" 
                       name="session_timeout" 
                       min="15" 
                       max="1440" 
                       value="{{ $settings['session_policy']['session_timeout'] }}" 
                       required>
                <small class="text-muted">Session expiration time in minutes (15 minutes to 24 hours)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="max_devices" class="form-label">Maximum Devices Per User <span class="text-danger">*</span></label>
                <input type="number" 
                       class="form-control" 
                       id="max_devices" 
                       name="max_devices" 
                       min="1" 
                       max="20" 
                       value="{{ $settings['session_policy']['max_devices'] }}" 
                       required>
                <small class="text-muted">Maximum number of concurrent sessions per user (1-20)</small>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i data-feather="save" class="icon-sm me-2"></i> Save Session Policy
          </button>
        </form>
      </div>

      <!-- Email Settings Tab -->
      <div class="tab-pane fade {{ session('active_tab') == 'email_settings' ? 'show active' : '' }}" 
           id="email-settings" 
           role="tabpanel">
        <form action="{{ route('settings.update-email') }}" method="POST">
          @csrf
          @method('PATCH')
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="mail_mailer" class="form-label">Mail Driver <span class="text-danger">*</span></label>
                <select class="form-select" id="mail_mailer" name="mail_mailer" required>
                  <option value="smtp" {{ $settings['email_settings']['mail_mailer'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                  <option value="sendmail" {{ $settings['email_settings']['mail_mailer'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                  <option value="mailgun" {{ $settings['email_settings']['mail_mailer'] == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                  <option value="ses" {{ $settings['email_settings']['mail_mailer'] == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="mail_host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control" 
                       id="mail_host" 
                       name="mail_host" 
                       value="{{ $settings['email_settings']['mail_host'] }}" 
                       required>
              </div>
              <div class="mb-3">
                <label for="mail_port" class="form-label">SMTP Port <span class="text-danger">*</span></label>
                <input type="number" 
                       class="form-control" 
                       id="mail_port" 
                       name="mail_port" 
                       min="1" 
                       max="65535" 
                       value="{{ $settings['email_settings']['mail_port'] }}" 
                       required>
              </div>
              <div class="mb-3">
                <label for="mail_username" class="form-label">SMTP Username</label>
                <input type="text" 
                       class="form-control" 
                       id="mail_username" 
                       name="mail_username" 
                       value="{{ $settings['email_settings']['mail_username'] }}">
              </div>
              <div class="mb-3">
                <label for="mail_password" class="form-label">SMTP Password</label>
                <input type="password" 
                       class="form-control" 
                       id="mail_password" 
                       name="mail_password" 
                       placeholder="Leave blank to keep current password">
                <small class="text-muted">Leave blank if you don't want to change the password</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="mail_encryption" class="form-label">Encryption</label>
                <select class="form-select" id="mail_encryption" name="mail_encryption">
                  <option value="">None</option>
                  <option value="tls" {{ $settings['email_settings']['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                  <option value="ssl" {{ $settings['email_settings']['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="mail_from_address" class="form-label">From Email Address <span class="text-danger">*</span></label>
                <input type="email" 
                       class="form-control" 
                       id="mail_from_address" 
                       name="mail_from_address" 
                       value="{{ $settings['email_settings']['mail_from_address'] }}" 
                       required>
              </div>
              <div class="mb-3">
                <label for="mail_from_name" class="form-label">From Name <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control" 
                       id="mail_from_name" 
                       name="mail_from_name" 
                       value="{{ $settings['email_settings']['mail_from_name'] }}" 
                       required>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i data-feather="save" class="icon-sm me-2"></i> Save Email Settings
          </button>
        </form>
      </div>

      <!-- System Info Tab -->
      <div class="tab-pane fade {{ session('active_tab') == 'system_info' ? 'show active' : '' }}" 
           id="system-info" 
           role="tabpanel">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-sm">
              <tr>
                <td><strong>Application Name:</strong></td>
                <td>{{ $settings['system_info']['app_name'] }}</td>
              </tr>
              <tr>
                <td><strong>Application Version:</strong></td>
                <td>{{ $settings['system_info']['app_version'] }}</td>
              </tr>
              <tr>
                <td><strong>Laravel Version:</strong></td>
                <td>{{ $settings['system_info']['laravel_version'] }}</td>
              </tr>
              <tr>
                <td><strong>PHP Version:</strong></td>
                <td>{{ $settings['system_info']['php_version'] }}</td>
              </tr>
              <tr>
                <td><strong>Environment:</strong></td>
                <td>
                  <span class="badge bg-{{ $settings['system_info']['environment'] == 'production' ? 'success' : 'warning' }}">
                    {{ $settings['system_info']['environment'] }}
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>Debug Mode:</strong></td>
                <td>
                  <span class="badge bg-{{ $settings['system_info']['debug_mode'] ? 'danger' : 'success' }}">
                    {{ $settings['system_info']['debug_mode'] ? 'Enabled' : 'Disabled' }}
                  </span>
                </td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-sm">
              <tr>
                <td><strong>Database Driver:</strong></td>
                <td>{{ $settings['system_info']['database_driver'] }}</td>
              </tr>
              <tr>
                <td><strong>Timezone:</strong></td>
                <td>{{ $settings['system_info']['timezone'] }}</td>
              </tr>
              <tr>
                <td><strong>Server Time:</strong></td>
                <td>{{ $settings['system_info']['server_time'] }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <!-- Backup/Export Tab -->
      <div class="tab-pane fade {{ session('active_tab') == 'backup_export' ? 'show active' : '' }}" 
           id="backup-export" 
           role="tabpanel">
        <div class="alert alert-info">
          <i data-feather="info" class="icon-sm me-2"></i>
          <strong>Export Data:</strong> Download system data as JSON files for backup purposes.
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-body text-center">
                <i data-feather="users" class="icon-lg mb-3 text-primary"></i>
                <h6>Export Users</h6>
                <p class="text-muted small">Export all users with their roles and divisions</p>
                <form action="{{ route('settings.export') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="type" value="users">
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i data-feather="download" class="icon-sm me-2"></i> Export
                  </button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body text-center">
                <i data-feather="shield" class="icon-lg mb-3 text-success"></i>
                <h6>Export Roles</h6>
                <p class="text-muted small">Export all roles with their permissions</p>
                <form action="{{ route('settings.export') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="type" value="roles">
                  <button type="submit" class="btn btn-success btn-sm">
                    <i data-feather="download" class="icon-sm me-2"></i> Export
                  </button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body text-center">
                <i data-feather="key" class="icon-lg mb-3 text-info"></i>
                <h6>Export Permissions</h6>
                <p class="text-muted small">Export all system permissions</p>
                <form action="{{ route('settings.export') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="type" value="permissions">
                  <button type="submit" class="btn btn-info btn-sm">
                    <i data-feather="download" class="icon-sm me-2"></i> Export
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="card border-warning">
              <div class="card-body text-center">
                <i data-feather="database" class="icon-lg mb-3 text-warning"></i>
                <h6>Export All Data</h6>
                <p class="text-muted small">Export users, roles, permissions, and divisions in one file</p>
                <form action="{{ route('settings.export') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="type" value="all">
                  <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Export all system data? This may take a moment.')">
                    <i data-feather="download" class="icon-sm me-2"></i> Export All
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Activate tab from session if exists
    @if(session('active_tab'))
        const activeTab = '{{ session('active_tab') }}';
        const tabButton = document.getElementById(activeTab + '-tab');
        const tabPane = document.getElementById(activeTab);
        if (tabButton && tabPane) {
            // Remove active from all tabs
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            // Activate selected tab
            tabButton.classList.add('active');
            tabPane.classList.add('show', 'active');
        }
    @endif
});
</script>
@endsection

