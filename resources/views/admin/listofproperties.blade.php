@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Properties List</li>
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
                                <h3 class="card-title mb-0">DataTable with default features</h3>
                                <a href="{{ route('admin.propertylisting') }}" class="btn btn-primary px-4 py-2">
                                    Add New
                                </a>
                            </div>


                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Address</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($properties as $property)
                                            <tr>
                                                <td>{{ $property->property_id }}</td>
                                                <td>
                                                    @if ($property->main_image)
                                                        <img src="{{ asset($property->main_image) }}" width="60"
                                                            height="40" style="object-fit:cover;">
                                                    @else
                                                        <span>No image</span>
                                                    @endif
                                                </td>
                                                <td>{{ $property->title }}</td>
                                                <td>{{ $property->address }}</td>
                                                <td>₹{{$property->price}}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $property->property_status === 'Available' ? 'badge-success' : 'badge-secondary' }}">
                                                        {{ $property->property_status }}
                                                    </span>
                                                </td>
                                                <td>{{ $property->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.properties.edit', $property->id) }}"
                                                        class="btn btn-sm btn-primary">Edit</a>

                                                    <form
                                                        action="{{ route('admin.properties.toggleStatus', $property->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit"
                                                            class="btn btn-sm {{ $property->is_active ? 'btn-danger' : 'btn-success' }}">
                                                            {{ $property->is_active ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>


                                                    <form action="{{ route('admin.properties.destroy', $property->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>City</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Created</th>
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
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "lengthMenu": [ [20, 50, 100, 300, 800, 1000], [20, 50, 100, 300, 800, 1000] ],
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
    </script>
@endsection



