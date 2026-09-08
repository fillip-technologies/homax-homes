@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>User Permission</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">User Permission List</li>
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
                        <h3 class="card-title">User Permissions</h3>
                       <a href="{{ route('user_permission.create') }}" class="btn btn-success">Add New Permission</a>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>All Property</th>
                                    <th>Featured Image</th>
                                    <th>Add Now</th>
                                    <th>Property Image</th>
                                    <th>Our Team</th>
                                    <th>Blog</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->user->name }} ({{ $permission->user->email }})</td>
                                        <td>{{ $permission->all_property ? '✔️' : '❌' }}</td>
                                        <td>{{ $permission->featured_image ? '✔️' : '❌' }}</td>
                                        <td>{{ $permission->add_now ? '✔️' : '❌' }}</td>
                                        <td>{{ $permission->property_image ? '✔️' : '❌' }}</td>
                                        <td>{{ $permission->our_team ? '✔️' : '❌' }}</td>
                                        <td>{{ $permission->blog ? '✔️' : '❌' }}</td>
                                        <td>
                                            <a href="{{ route('user_permission.edit', $permission->user_id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            {{-- Optional Delete --}}
                                            {{-- <form action="{{ route('user_permission.destroy', $permission->user_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete permission?')">Delete</button>
                                            </form> --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No permissions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
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
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('extraJs')
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

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
                title: 'Enquiry_Form_List'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                titleAttr: 'CSV',
                title: 'Enquiry_Form_List'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Excel',
                title: 'Enquiry_Form_List'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                titleAttr: 'PDF',
                title: 'Enquiry_Form_List'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                titleAttr: 'Print',
                title: 'Enquiry_Form_List'
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



