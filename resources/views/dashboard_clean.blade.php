@extends('Layouts.app')

@section('content')
<!-- Dashboard: Statistics, Users list, Activity log, User detail modal, Permissions mapping -->
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-3">
  <div>
    <h4 class="mb-0">Dashboard</h4>
    <p class="text-muted">Ringkasan sistem dan manajemen pengguna</p>
  </div>
  <div>
    <a href="#" class="btn btn-sm btn-primary">Buat User Baru</a>
  </div>
</div>

<!-- A. Statistik -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Total User</h6>
        <h3>{{ $stats['totalUsers'] }}</h3>
        <p class="text-muted">Jumlah keseluruhan akun</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">User Aktif</h6>
        <h3>{{ $stats['activeUsers'] }}</h3>
        <p class="text-muted">Akun dengan status aktif</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">User Nonaktif</h6>
        <h3>{{ $stats['inactiveUsers'] }}</h3>
        <p class="text-muted">Akun nonaktif</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Login Hari Ini</h6>
        <h3>{{ $stats['todayLogins'] }}</h3>
        <p class="text-muted">Aktivitas hari ini</p>
      </div>
    </div>
  </div>
</div>

<!-- B. Daftar User -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Daftar User</h6>
        <div class="table-responsive">
          <table class="table table-hover" id="usersTable">
            <thead>
              <tr>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Division</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Dibuat Pada</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role?->role_name ?? '-' }}</td>
                <td>{{ $user->division?->division_name ?? '-' }}</td>
                <td>{{ ucfirst($user->status) }}</td>
                <td>{{ $user->last_login }}</td>
                <td>{{ $user->created_at }}</td>
                <td>
                  <button class="btn btn-sm btn-info btn-detail" data-user='@json($user)'>Detail</button>
                  <button class="btn btn-sm btn-primary">Edit</button>
                  <button class="btn btn-sm btn-warning">Nonaktifkan</button>
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $users->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- C. User Activity Log -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">User Activity Log</h6>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Aktivitas</th>
                <th>Timestamp</th>
                <th>IP</th>
                <th>Device</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activities as $log)
              <tr>
                <td>{{ $log->user?->full_name ?? '—' }}</td>
                <td>{{ $log->user?->role?->role_name ?? '—' }}</td>
                <td>{{ $log->activity }}</td>
                <td>{{ $log->timestamp }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ Illuminate\Support\Str::limit($log->user_agent, 60) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{ $activities->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- D. User Detail Modal -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title">Detail User</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
    <dl class="row">
      <dt class="col-sm-3">Nama Lengkap</dt><dd class="col-sm-9" id="d-full_name"></dd>
      <dt class="col-sm-3">Email</dt><dd class="col-sm-9" id="d-email"></dd>
      <dt class="col-sm-3">Username</dt><dd class="col-sm-9" id="d-username"></dd>
      <dt class="col-sm-3">Role</dt><dd class="col-sm-9" id="d-role"></dd>
      <dt class="col-sm-3">Division</dt><dd class="col-sm-9" id="d-division"></dd>
      <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><select id="d-status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option><option value="pending">Pending</option></select></dd>
      <dt class="col-sm-3">Last Login</dt><dd class="col-sm-9" id="d-last_login"></dd>
      <dt class="col-sm-3">Created At</dt><dd class="col-sm-9" id="d-created_at"></dd>
      <dt class="col-sm-3">Updated At</dt><dd class="col-sm-9" id="d-updated_at"></dd>
    </dl>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary">Save changes</button>
    </div>
  </div>
  </div>
</div>

<!-- E. Permissions Mapping -->
<div class="row mb-5">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Permissions Mapping</h6>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Role</th>
                <th>Permissions</th>
                <th>Kategori</th>
              </tr>
            </thead>
            <tbody>
              @foreach(\App\Models\Role::with('permissions')->get() as $role)
              <tr>
                <td>{{ $role->role_name }}</td>
                <td>{{ $role->permissions->pluck('permission_code')->join(', ') }}</td>
                <td>{{ $role->permissions->pluck('category')->unique()->join(', ') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.btn-detail').forEach(function(btn){
        btn.addEventListener('click', function(){
            try{
                var user = JSON.parse(this.getAttribute('data-user'));
            }catch(e){ return; }
            document.getElementById('d-full_name').textContent = user.full_name || '';
            document.getElementById('d-email').textContent = user.email || '';
            document.getElementById('d-username').textContent = user.username || '';
            document.getElementById('d-role').textContent = (user.role && user.role.role_name) ? user.role.role_name : (user.role_id ? 'ID:'+user.role_id : '-');
            document.getElementById('d-division').textContent = (user.division && user.division.division_name) ? user.division.division_name : (user.division_id ? 'ID:'+user.division_id : '-');
            document.getElementById('d-status').value = user.status || 'pending';
            document.getElementById('d-last_login').textContent = user.last_login || '-';
            document.getElementById('d-created_at').textContent = user.created_at || '-';
            document.getElementById('d-updated_at').textContent = user.updated_at || '-';
            var myModal = new bootstrap.Modal(document.getElementById('userDetailModal'));
            myModal.show();
        });
    });
});
</script>
@endsection
