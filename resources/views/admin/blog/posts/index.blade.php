@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Blog Posts</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Blog Posts</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    @include('admin.partials.alerts')

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header w-100 d-flex justify-content-between align-items-center">
                    <h3 class="card-title">All Posts ({{ $posts->total() }})</h3>
                    <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-success ml-auto">
                        <i class="fas fa-plus mr-1"></i> Add New Post
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="form-row mb-3">
                        <div class="col-md-4 mb-2">
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search title or excerpt">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="category" class="form-control">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="status" class="form-control">
                                <option value="">All statuses</option>
                                @foreach (['published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Draft'] as $value => $label)
                                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:80px">Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Published</th>
                                    <th>Views</th>
                                    <th style="width:290px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                    @php
                                        $label = $post->status_label;
                                        $badge = ['Published' => 'success', 'Scheduled' => 'info', 'Draft' => 'secondary'][$label];
                                    @endphp
                                    <tr>
                                        <td>
                                            @if ($post->image_url)
                                                <img src="{{ $post->image_url }}" alt="" style="width:64px;height:48px;object-fit:cover;border-radius:4px">
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $post->title }}</strong>
                                            @if ($post->is_featured)
                                                <span class="badge badge-warning ml-1">Featured</span>
                                            @endif
                                            <div class="small text-muted">/blog/{{ $post->slug }}</div>
                                        </td>
                                        <td>{{ $post->category?->name ?? '—' }}</td>
                                        <td><span class="badge badge-{{ $badge }}">{{ $label }}</span></td>
                                        <td>{{ $post->published_at?->format('d M Y, H:i') ?? '—' }}</td>
                                        <td>{{ number_format($post->views) }}</td>
                                        <td>
                                            <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-sm btn-primary mb-1">Edit</a>
                                            <a href="{{ $post->url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary mb-1">
                                                {{ $label === 'Published' ? 'View' : 'Preview' }}
                                            </a>
                                            <form action="{{ route('admin.blog.posts.publish', $post) }}" method="POST" class="d-inline">
                                                @csrf @method('PUT')
                                                <button class="btn btn-sm btn-outline-{{ $post->status === 'draft' ? 'success' : 'warning' }} mb-1">
                                                    {{ $post->status === 'draft' ? 'Publish' : 'Unpublish' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.blog.posts.feature', $post) }}" method="POST" class="d-inline">
                                                @csrf @method('PUT')
                                                <button class="btn btn-sm btn-outline-warning mb-1" title="Toggle featured">
                                                    <i class="{{ $post->is_featured ? 'fas' : 'far' }} fa-star"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.blog.posts.destroy', $post) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this post?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger mb-1"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">No posts found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">{{ $posts->links() }}</div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
