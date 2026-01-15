@extends('admins.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Subcategories Management</h4>
                    <a href="{{ route('admins.subcategory') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Subcategory
                    </a>
                </div>
                <div class="card-body">
                    @if(session('msg'))
                        <div class="alert alert-{{ session('msg_type') ?? 'success' }} alert-dismissible fade show" role="alert">
                            {{ session('msg') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ isset($edit) ? 'Edit Subcategory' : 'Add New Subcategory' }}</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admins.subcategory') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="hidden_id" value="{{ $edit->id ?? '' }}">
                                        
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Parent Category <span class="text-danger">*</span></label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" id="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ (old('category_id', $edit->category_id ?? '') == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $edit->name ?? '') }}" placeholder="Enter subcategory name" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ isset($edit) ? 'Update' : 'Save' }} Subcategory</button>
                                            @if(isset($edit))
                                                <a href="{{ route('admins.subcategory') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">All Subcategories</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Parent Category</th>
                                                    <th>Slug</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($sub_categories as $subcategory)
                                                    <tr>
                                                        <td>{{ $subcategory->id }}</td>
                                                        <td><strong>{{ $subcategory->name }}</strong></td>
                                                        <td><span class="badge bg-info">{{ $subcategory->parent_category ?? 'N/A' }}</span></td>
                                                        <td><code>{{ $subcategory->slug }}</code></td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('admins.subcategory', $subcategory->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                                <a href="{{ route('admins.subcategory', [$subcategory->id, 'delete']) }}" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">No subcategories found. Add your first subcategory!</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
