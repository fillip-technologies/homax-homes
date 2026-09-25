@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Property Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Admin</a></li>
                        <li class="breadcrumb-item active">Property Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Dashboard Stat Boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- Total Properties -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($totalProperties ?? 0) }}</h3>
                            <p>Total Properties</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <a href="{{ route('admin.properties.list') }}" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <!-- Today's Listings -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($todayListings ?? 0) }}</h3>
                            <p>Today’s Listings</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-plus-square"></i>
                        </div>
                        <a href="{{ route('admin.properties.list') }}" class="small-box-footer">
                            See Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <!-- Total Enquiries -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($totalInquiries ?? $totalEnquiries ?? 0) }}</h3>
                            <p>Total Enquiries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <a href="{{ route('admin.inquiryformlist') }}" class="small-box-footer">
                            View Enquiries <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- Today’s Enquiries -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($todayInquiries ?? $todayEnquiries ?? 0) }}</h3>
                            <p>Today’s Enquiries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <a href="{{ route('admin.inquiryformlist') }}" class="small-box-footer">
                            View Enquiries <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection


