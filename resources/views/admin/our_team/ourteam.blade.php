@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Our Team &amp; Users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Our Team &amp; Users</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <!-- Main content -->
        {{-- Add New properties --}}



<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- /.card -->
                <div class="card">
                    <div class="card-header w-100 d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Team &amp; Users</h3>
                        <a href="{{ route('our_team.create') }}" class="btn btn-success ml-auto">Add New</a>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        @php
                            $canManageUsers = Auth::guard('admin')->user()?->hasPermission('manage_users');
                            $meId = Auth::guard('admin')->id();

                            // Update password: always for your own row; for others only admin accounts, and only if you manage users.
                            $canUpdatePassword = fn ($user) => $user && ($user->id === $meId || ($canManageUsers && $user->role === 'admin'));

                            // Access column: badges for what the account can open in the admin.
                            $renderAccess = function ($user) {
                                if (!$user) {
                                    return '<span class="text-muted">No login</span>';
                                }
                                if ($user->role !== 'admin') {
                                    return '<span class="badge badge-secondary">No admin access</span>';
                                }
                                $labels = $user->accessLabels();
                                if ($labels === ['Full access']) {
                                    return '<span class="badge badge-success">Full access</span>';
                                }
                                if (!$labels) {
                                    return '<span class="badge badge-warning">None yet</span>';
                                }
                                return collect($labels)->map(fn ($l) => '<span class="badge badge-info mr-1">' . e($l) . '</span>')->implode('');
                            };
                        @endphp
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Designation</th>
                                    <th>Login email</th>
                                    <th>Role</th>
                                    <th>Admin access</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    @php $login = $member->user; @endphp
                                    <tr>
                                        <td><strong>{{ $member->employee_name }}</strong>@if ($login && $login->id === $meId) <span class="badge badge-primary">You</span>@endif</td>
                                        <td>{{ $login ? 'Team member + login' : ($member->user_id ? 'Team member (login missing)' : 'Team member') }}</td>
                                        <td>{{ $member->designation }}</td>
                                        <td>{{ $member->user_id ?: '-' }}</td>
                                        <td>{{ $login ? ucfirst($login->role) : '-' }}</td>
                                        <td>{!! $renderAccess($login) !!}</td>
                                        <td>{{ $member->joining_date ?: '-' }}</td>
                                        <td>
                                            @if ($member->status)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('our_team.edit', $member->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @if ($login && $canManageUsers)
                                                <a href="{{ route('user_permission.edit', $login->id) }}" class="btn btn-primary btn-sm">Access</a>
                                            @endif
                                            @if ($canUpdatePassword($login))
                                                <a href="{{ route('admin.password.edit', $login->id) }}" class="btn btn-secondary btn-sm">Update password</a>
                                            @endif
                                            <form action="{{ route('our_team.destroy', $member->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this team member{{ $login ? ' and their login' : '' }}?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach ($loginOnlyUsers as $user)
                                    <tr>
                                        <td><strong>{{ $user->name }}</strong>@if ($user->id === $meId) <span class="badge badge-primary">You</span>@endif</td>
                                        <td>Login only</td>
                                        <td class="text-muted">-</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ ucfirst($user->role) }}</td>
                                        <td>{!! $renderAccess($user) !!}</td>
                                        <td class="text-muted">-</td>
                                        <td class="text-muted">-</td>
                                        <td class="text-nowrap">
                                            @if ($canManageUsers && $user->role === 'admin')
                                                <a href="{{ route('user_permission.edit', $user->id) }}" class="btn btn-primary btn-sm">Access</a>
                                            @endif
                                            @if ($canUpdatePassword($user))
                                                <a href="{{ route('admin.password.edit', $user->id) }}" class="btn btn-secondary btn-sm">Update password</a>
                                            @endif
                                            @unless (($canManageUsers && $user->role === 'admin') || $canUpdatePassword($user))
                                                <span class="text-muted">-</span>
                                            @endunless
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Designation</th>
                                    <th>Login email</th>
                                    <th>Role</th>
                                    <th>Admin access</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
        <!-- /.content -->
    </div>
@endsection

@section('extraCss')
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
@endsection

@section('extraJs')
    <!-- DataTables & Plugins -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <!-- Page specific script -->
    <script>
 $(function() {
    $("#example1").DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        lengthMenu: [[20, 50, 100, 300, 800, 1000], [20, 50, 100, 300, 800, 1000]],
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="fas fa-copy"></i> Copy',
                titleAttr: 'Copy',
                title: 'Our_Team_List'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                titleAttr: 'CSV',
                title: 'Our_Team_List'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Excel',
                title: 'Our_Team_List'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'PDF',
                title: 'Our_Team_List'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                titleAttr: 'Print',
                title: 'Our_Team_List'
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Column Visibility'
            }
        ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
    </script>
@endsection



