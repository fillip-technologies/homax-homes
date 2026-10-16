@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Blog Categories</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Blog Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    @include('admin.partials.alerts')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">All Categories</h3></div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th style="width:90px">Order</th>
                                        <th style="width:90px">Posts</th>
                                        <th style="width:100px">Status</th>
                                        <th style="width:150px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td>
                                                <strong>{{ $category->name }}</strong>
                                                <div class="small text-muted">{{ $category->slug }}</div>
                                                @if ($category->description)
                                                    <div class="small">{{ $category->description }}</div>
                                                @endif
                                            </td>
                                            <td>{{ $category->sort_order }}</td>
                                            <td>{{ $category->posts_count }}</td>
                                            <td>
                                                <span class="badge badge-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                    {{ $category->is_active ? 'Active' : 'Hidden' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    data-toggle="modal" data-target="#edit-category-{{ $category->id }}">Edit</button>
                                                <form action="{{ route('admin.blog.categories.destroy', $category) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Delete this category? Its {{ $category->posts_count }} post(s) will become uncategorised.')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">No categories yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">Add Category</h3></div>
                        <form action="{{ route('admin.blog.categories.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" maxlength="100" required class="form-control" value="{{ old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" id="description" name="description" maxlength="255" class="form-control" value="{{ old('description') }}">
                                </div>
                                <div class="form-group">
                                    <label for="sort_order">Order</label>
                                    <input type="number" id="sort_order" name="sort_order" min="0" class="form-control" value="{{ old('sort_order') }}" placeholder="Last">
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="is_active">Active (shown on the blog)</label>
                                </div>
                            </div>
                            <div class="card-footer"><button class="btn btn-success">Add Category</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @foreach ($categories as $category)
        <div class="modal fade" id="edit-category-{{ $category->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="modal-content" action="{{ route('admin.blog.categories.update', $category) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" maxlength="100" required class="form-control" value="{{ $category->name }}">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" maxlength="255" class="form-control" value="{{ $category->description }}">
                        </div>
                        <div class="form-group">
                            <label>Order</label>
                            <input type="number" name="sort_order" min="0" class="form-control" value="{{ $category->sort_order }}">
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input" id="active-{{ $category->id }}" name="is_active" value="1" @checked($category->is_active)>
                            <label class="custom-control-label" for="active-{{ $category->id }}">Active (shown on the blog)</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
