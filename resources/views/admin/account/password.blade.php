@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Password</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Update Password</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('admin.partials.alerts')

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ $isSelf ? 'Change your password' : 'Set a new password for ' . $user->name }}
                            </h3>
                        </div>
                        <form action="{{ route('admin.password.update', $user) }}" method="POST" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <p class="text-muted mb-3">Account: <strong>{{ $user->email }}</strong></p>

                                <div class="form-group">
                                    <label for="password">New password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        autocomplete="new-password" minlength="8" required>
                                    <small class="form-text text-muted">At least 8 characters, with letters and numbers.</small>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Confirm new password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                        autocomplete="new-password" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">Update password</button>
                                <a href="{{ url()->previous() }}" class="btn btn-default ml-2">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
