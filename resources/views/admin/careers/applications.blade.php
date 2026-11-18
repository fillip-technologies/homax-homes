@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Job Applications</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.careers.index') }}">Careers</a></li>
                        <li class="breadcrumb-item active">Applications</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    @include('admin.partials.alerts')

    <section class="content">
        <div class="container-fluid">
            {{-- Filter: all, general applications, or one opening. --}}
            <div class="mb-3 d-flex flex-wrap" style="gap:.5rem">
                @php
                    $chip = fn (bool $on) => 'btn btn-sm ' . ($on ? 'btn-primary' : 'btn-outline-secondary');
                @endphp
                <a href="{{ route('admin.careers.applications.index') }}" class="{{ $chip($filter === '') }}">
                    All <span class="badge badge-light ml-1">{{ $totalCount }}</span>
                </a>
                <a href="{{ route('admin.careers.applications.index', ['job' => 'general']) }}" class="{{ $chip($filter === 'general') }}">
                    General applications <span class="badge badge-light ml-1">{{ $generalCount }}</span>
                </a>
                @foreach ($openings as $opening)
                    <a href="{{ route('admin.careers.applications.index', ['job' => $opening->id]) }}" class="{{ $chip($filter === (string) $opening->id) }}">
                        {{ $opening->title }}
                        @unless ($opening->is_active)<small>(hidden)</small>@endunless
                        <span class="badge badge-light ml-1">{{ $opening->applications_count }}</span>
                    </a>
                @endforeach
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Applicant</th>
                                    <th>Contact</th>
                                    <th>Applied for</th>
                                    <th>Documents</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($applications as $app)
                                    <tr>
                                        <td class="text-nowrap">
                                            {{ $app->created_at?->format('d M Y') }}<br>
                                            <small class="text-muted">{{ $app->created_at?->format('h:i A') }}</small>
                                        </td>
                                        <td><strong>{{ $app->name }}</strong></td>
                                        <td>
                                            <a href="mailto:{{ $app->email }}">{{ $app->email }}</a>
                                            @if ($app->phone)
                                                <br><a href="tel:{{ preg_replace('/[^\d+]/', '', $app->phone) }}">{{ $app->phone }}</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($app->is_general)
                                                <span class="badge badge-secondary">General application</span>
                                            @else
                                                <i class="fas fa-briefcase text-muted mr-1"></i>
                                                @if ($app->jobOpening?->is_active)
                                                    <a target="_blank" href="{{ $app->jobOpening->url }}">{{ $app->job_title }}</a>
                                                @else
                                                    {{ $app->job_title }}
                                                    <span class="badge badge-light border">{{ $app->jobOpening ? 'hidden' : 'removed' }}</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('admin.careers.applications.file', [$app, 'resume']) }}" class="btn btn-xs btn-outline-primary">
                                                <i class="fas fa-file-download"></i> Resume
                                            </a>
                                            @if ($app->cover_letter_path)
                                                <a href="{{ route('admin.careers.applications.file', [$app, 'cover-letter']) }}" class="btn btn-xs btn-outline-secondary">
                                                    <i class="fas fa-file-download"></i> Cover letter
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('admin.careers.applications.destroy', $app) }}" class="d-inline"
                                                  onsubmit="return confirm('Delete this application and its files? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No applications here yet.</td></tr>
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
