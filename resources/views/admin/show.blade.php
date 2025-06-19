@extends('layouts.app') {{-- Or your admin layout --}}

@section('title', 'Admin Profile')

@section('content')
      <hr>

      <div class="mb-3">
        <p class="mb-1"><strong>Email:</strong></p>
        <p class="text-muted">{{ $admin->email }}</p>
      </div>

      <div class="mb-3">
        <p class="mb-1"><strong>Joined:</strong></p>
        <p class="text-muted">{{ optional($admin->created_at)->format('F j, Y') ?? 'N/A' }}</p>
      </div>

      <div class="mt-4 text-end">
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
          <i class="fas fa-edit me-1"></i> Edit Profile
        </a>
      </div>
    </div>
  </div>
</div>
@endsection