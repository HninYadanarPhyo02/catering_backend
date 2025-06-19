@extends('layouts.app') {{-- or your admin layout --}}

@section('title', 'Edit Profile')

@section('content')
<div class="card shadow-lg rounded-4">
    <div class="card-body p-4">
      <h4 class="mb-4 text-primary">Edit Profile</h4>

      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PATCH')

        {{-- Name Field --}}
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input
            type="text"
            id="name"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $admin->name) }}"
            required
          >
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Email Field --}}
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $admin->email) }}"
            required
          >
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Submit & Cancel --}}
        <div class="d-flex justify-content-between">
          <a href="{{ route('admin.show') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Cancel
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Save Changes
          </button>
        </div>

      </form>

    </div>
  </div>

</div>
@endsection