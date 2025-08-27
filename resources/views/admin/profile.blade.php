@extends('layouts.app')
@section('title','Admin Profile')
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center g-4">

        {{-- Left Column: Profile Info --}}
        <div class="col-lg-4 col-md-5">
            <div class="card shadow-sm border rounded-3">
                <div class="card-body text-center p-4 bg-light">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-secondary"></i>
                    </div>
                    <h4 class="fw-bold text-dark">{{ $admin->name }}</h4>
                    <span class="badge bg-primary text-white mb-3">Administrator</span>
                    <div class="text-start mt-3">
                        <p class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i>{{ $admin->email }}</p>
                        <p class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Joined: {{ optional($admin->created_at)->format('F j, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Update Form --}}
        <div class="col-lg-8 col-md-7">
            <div class="card shadow-sm border rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-primary">Update Profile</h5>

                    @if(session('success'))
                    <div class="alert alert-success rounded-2">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        {{-- Name & Email in one row --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $admin->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Old Password --}}
                        <div class="mb-3">
                            <label for="old_password" class="form-label fw-semibold">Old Password</label>
                            <div class="input-group">
                                <input type="password" id="old_password" name="old_password" class="form-control @error('old_password') is-invalid @enderror">
                                <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="old_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('old_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- New Password & Confirm Password --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label fw-semibold">New Password</label>
                                <div class="input-group">
                                    <input type="password" id="new_password" name="new_password" class="form-control @error('new_password') is-invalid @enderror">
                                    <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="new_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror">
                                    <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="new_password_confirmation">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('new_password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary fw-semibold px-4 py-2 mt-2 rounded-2">Update</button>
                    </form>
                </div>
            </div>
        </div>


    </div>
</div>

<script>
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
</script>
@endsection