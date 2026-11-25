
@extends('layouts.app')

@section('title', 'My Subjects List')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">My Subjects List</h1>

    @can('create', App\Models\Subject::class)
        <a href="{{ route('subjects.create') }}" class="btn btn-primary">
            + Add Subject
        </a>
    @endcan
</div>

@cannot('create', App\Models\Subject::class)
    <p class="text-muted small">You are viewing this list in read-only mode.</p>
@endcannot

{{-- Search and Filter Form --}}
<form method="GET" action="{{ route('subjects.index') }}" class="mb-4">
    <div class="row g-3 align-items-end">

        <div class="col-md-3">
            <label for="code" class="form-label">Search by Code</label>
            <input type="text" name="code" id="code"
                   class="form-control"
                   placeholder="Search subject code"
                   value="{{ request('code') }}">
        </div>

        <div class="col-md-3">
            <label for="prerequisite_id" class="form-label">
                Prerequisite
            </label>
            <select name="prerequisite_id" id="prerequisite_id"
                    class="form-select">
                <option value="">All Prerequisites</option>

                @foreach ($prerequisites as $prerequisite)
                    <option value="{{ $prerequisite->id }}"
                        @selected(request('prerequisite_id') == $prerequisite->id)>
                        {{ $prerequisite->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="sort" class="form-label">Sort By</label>
            <select name="sort" id="sort" class="form-select">
                <option value="code" @selected($sort === 'code')>
                    Subject Code
                </option>
                <option value="title" @selected($sort === 'title')>
                    Subject Title
                </option>
            </select>
        </div>

        <div class="col-md-2">
            <label for="is_active" class="form-label">Status</label>
            <select name="is_active" id="is_active" class="form-select">
                <option value="">Any Status</option>
                <option value="1" @selected(request('is_active') === '1')>
                    Active
                </option>
                <option value="0" @selected(request('is_active') === '0')>
                    Inactive
                </option>
            </select>
        </div>

        <div class="col-md-2">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{ route('subjects.index') }}"
                   class="btn btn-secondary">Clear</a>
            </div>
        </div>

    </div>
</form>

{{-- Search Results Description --}}
<p>
    @if (request()->hasAny(['code', 'prerequisite_id', 'is_active']))
        Showing filtered subjects.
    @else
        Showing all subjects.
    @endif
</p>

{{-- Subjects Table --}}
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>ID</th>
            <th>Code</th>
            <th>Title</th>
            <th>Units</th>
            <th>Prerequisite</th>

            @can('create', App\Models\Subject::class)
                <th>Actions</th>
            @endcan
        </tr>
    </thead>

    <tbody>
        @forelse ($subjects as $subj)
            <tr>
                <td>{{ $subjects->firstItem() + $loop->index }}</td>
                <td>{{ $subj->id }}</td>

                <td>
                    <a href="{{ route('subjects.show', $subj->id) }}">
                        {{ $subj->code }}
                    </a>
                </td>

                <td>{{ $subj->title }}</td>

                <td>
                    @if ($subj->units >= 3)
                        <strong>{{ $subj->units }} (Heavy Load)</strong>
                    @else
                        {{ $subj->units }}
                    @endif
                </td>

                <td>{{ $subj->prerequisite->name ?? 'None' }}</td>

                @can('create', App\Models\Subject::class)
                    <td>
                        @can('update', $subj)
                            <a href="{{ route('subjects.edit', $subj) }}"
                               class="btn btn-primary btn-sm">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $subj)
                            <form action="{{ route('subjects.destroy', $subj) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this record?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </td>
                @endcan
            </tr>

        @empty
            <tr>
                <td colspan="7" class="text-center">
                    No subjects match. Try clearing the filters.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Pagination --}}
{{ $subjects->links() }}

@endsection
