@extends('admin.layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid"><h1>About Page</h1></div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header"><h3 class="card-title">Page Text</h3></div>
                <div class="card-body">
                    <p class="text-muted">
                        The heading and paragraphs at the top of the About Us page.
                        Add as many paragraphs as you need; empty ones are ignored.
                    </p>
                    <form method="POST" action="{{ route('admin.footer.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="footer_heading">Heading</label>
                            <input type="text" id="footer_heading" name="footer[heading]" maxlength="120" class="form-control" value="{{ old('footer.heading', $about['heading']) }}">
                        </div>
                        {{-- Always sent, so removing every paragraph still saves an empty list. --}}
                        <input type="hidden" name="footer[paragraphs][]" value="">
                        <div id="para-list">
                            @foreach (old('footer.paragraphs', $about['paragraphs']) as $paragraph)
                                <div class="form-group para-row">
                                    <div class="d-flex justify-content-between">
                                        <label class="para-label">Paragraph</label>
                                        <button type="button" class="btn btn-sm btn-outline-danger para-remove mb-1">Remove</button>
                                    </div>
                                    <textarea name="footer[paragraphs][]" rows="3" maxlength="1500" class="form-control">{{ $paragraph }}</textarea>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="para-add" class="btn btn-outline-primary mb-3">+ Add paragraph</button>
                        <p class="text-muted small">Paragraphs are shown in two columns; if the count is odd, the last one is centered on its own.</p>
                        <br>
                        <button type="submit" class="btn btn-primary">Save page text</button>
                    </form>
                    <form method="POST" action="{{ route('admin.footer.reset') }}" class="mt-3"
                          onsubmit="return confirm('Reset the About page text to the default?')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="part" value="about">
                        <button type="submit" class="btn btn-outline-danger">Reset to default text</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">People</h3></div>
                <div class="card-body">
                    <p class="text-muted">
                        These people appear under the text on the public About Us page, in the order shown.
                        Untick &ldquo;Show on website&rdquo; to hide one without deleting it.
                    </p>

                    @forelse ($members as $m)
                        <div class="border rounded p-3 mb-3 {{ $m->is_active ? '' : 'bg-light' }}">
                            <div class="d-flex align-items-center">
                                @if ($m->photo_url)
                                    <img src="{{ $m->photo_url }}" alt="" class="rounded-circle mr-3" style="width:56px;height:56px;object-fit:cover;">
                                @else
                                    <span class="rounded-circle mr-3 bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;">{{ $m->initial }}</span>
                                @endif
                                <div class="flex-grow-1">
                                    <strong>{{ $m->name }}</strong> <span class="text-muted">{{ $m->role }}</span>
                                    @unless ($m->is_active) <span class="badge badge-secondary ml-1">Hidden</span> @endunless
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($m->description, 120) }}</div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary mr-2" type="button" data-toggle="collapse" data-target="#edit-m-{{ $m->id }}">Edit</button>
                                <form method="POST" action="{{ route('admin.about_members.destroy', $m) }}" onsubmit="return confirm('Delete this person?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                            <div class="collapse mt-3" id="edit-m-{{ $m->id }}">
                                <form method="POST" action="{{ route('admin.about_members.update', $m) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    @include('admin.partials.about_member_fields', ['m' => $m])
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No people yet.</p>
                    @endforelse

                    <hr>
                    <h5>Add a person</h5>
                    <form method="POST" action="{{ route('admin.about_members.store') }}" enctype="multipart/form-data">
                        @csrf
                        @include('admin.partials.about_member_fields', ['m' => null])
                        <button type="submit" class="btn btn-success">Add person</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('extraJs')
<script>
(function () {
    var list = document.getElementById('para-list');
    function renumber() {
        list.querySelectorAll('.para-row').forEach(function (row, i) {
            row.querySelector('.para-label').textContent = 'Paragraph ' + (i + 1);
        });
    }
    document.getElementById('para-add').addEventListener('click', function () {
        var row = document.createElement('div');
        row.className = 'form-group para-row';
        row.innerHTML = '<div class="d-flex justify-content-between"><label class="para-label"></label>' +
            '<button type="button" class="btn btn-sm btn-outline-danger para-remove mb-1">Remove</button></div>' +
            '<textarea name="footer[paragraphs][]" rows="3" maxlength="1500" class="form-control"></textarea>';
        list.appendChild(row);
        renumber();
        row.querySelector('textarea').focus();
    });
    list.addEventListener('click', function (e) {
        if (e.target.classList.contains('para-remove')) {
            e.target.closest('.para-row').remove();
            renumber();
        }
    });
    renumber();
})();
</script>
@endsection
