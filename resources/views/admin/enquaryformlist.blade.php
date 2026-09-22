@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Enquary Form List</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Enquary Form List</li>
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

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile Number</th>
                                            <th>Email</th>
                                            <th>Intent</th>
                                            <th>Source</th>
                                            <th>Message</th>
                                            <th>Date and Time</th>
                                            <th>View Properties</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     @foreach($inquaries as $inquary)
                                        <tr>
                                            <td>{{ $inquary->name }}</td>
                                            <td>{{ $inquary->phone }}</td>
                                            <td>
                                                @if($inquary->email)
                                                    <a href="mailto:{{ $inquary->email }}">{{ $inquary->email }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $inquary->intent ?: 'General' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $inquary->source ?: 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($inquary->message)
                                                    {{ $inquary->message }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $inquary->created_at }}</td>
                                            <td>
                                                @if($inquary->property_id)
                                                    <a target="_blank" href="{{ route('property.show', ['id' => $inquary->property_id]) }}" class="btn btn-info">View Property</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                     @endforeach


                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile Number</th>
                                            <th>Email</th>
                                            <th>Intent</th>
                                            <th>Source</th>
                                            <th>Message</th>
                                            <th>Date and Time</th>
                                            <th>View Properties</th>
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



