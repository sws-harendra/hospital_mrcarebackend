@extends('backend.admins.layouts.base')

@push('title')
    <title>Edit Department | {{ env('APP_NAME') }}</title>
@endpush

@section('page-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Department</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admins.department.index') }}">Departments</a></div>
                <div class="breadcrumb-item">Edit</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-8 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Department: {{ $department->name }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admins.department.update', $department->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label for="name">Department Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $department->name) }}" 
                                           placeholder="Enter department name"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="4" 
                                              placeholder="Enter department description"
                                              required>{{ old('description', $department->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Maximum 500 characters allowed.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label class="custom-switch mt-2">
                                        <input type="hidden" name="status" value="0">
                                        <input type="checkbox" name="status" 
                                               class="custom-switch-input" 
                                               value="1"
                                               {{ old('status', $department->status) ? 'checked' : '' }}>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Active Department</span>
                                    </label>
                                    <small class="form-text text-muted">
                                        Active departments will be available for selection.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Update Department
                                    </button>
                                    <a href="{{ route('admins.department.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection