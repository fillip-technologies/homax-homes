@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Careers</h1></div>
                <div class="col-sm-6 text-sm-right">
                    <a href="{{ route('careers') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary mr-2">View page</a>
                    <a href="{{ route('admin.careers.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add opening</a>
                </div>
            </div>
        </div>
    </section>

    @include('admin.partials.alerts')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body p-0">
                    <p class="text-muted px-3 pt-3">
                        Openings appear on the public Careers page in this order. Hide one to take it off the site without deleting it.
                        Applications arrive under <a href="{{ route('admin.inquiryformlist') }}">Property Enquiry</a> as &ldquo;Job application&rdquo;.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width:60px">Order</th>
                                    <th>Title</th>
                                    <th>Department</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($openings as $job)
                                    <tr class="{{ $job->is_active ? '' : 'text-muted' }}">
                                        <td>{{ $job->sort_order }}</td>
                                        <td>
                                            <strong>{{ $job->title }}</strong>
                                            <div class="small text-muted">/careers/{{ $job->slug }}</div>
                                        </td>
                                        <td>{{ $job->department ?: '—' }}</td>
                                        <td>{{ $job->type_label }}</td>
                                        <td>{{ $job->location ?: '—' }}</td>
                                        <td>
                                            @if ($job->is_active)
                                                <span class="badge badge-success">Live</span>
                                            @else
                                                <span class="badge badge-secondary">Hidden</span>
                                            @endif
                                        </td>
                                        <td class="text-right text-nowrap">
                                            @if ($job->is_active)
                                                <a href="{{ $job->url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" title="View"><i class="fas fa-external-link-alt"></i></a>
                                            @endif
                                            <form method="POST" action="{{ route('admin.careers.toggle', $job) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-info">{{ $job->is_active ? 'Hide' : 'Publish' }}</button>
                                            </form>
                                            <a href="{{ route('admin.careers.edit', $job) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="POST" action="{{ route('admin.careers.destroy', $job) }}" class="d-inline"
                                                  onsubmit="return confirm('Delete this job opening?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">No job openings yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
