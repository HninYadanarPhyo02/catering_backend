@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border rounded-3">
                <div class="card-body p-5 bg-light">
                    {{-- Header --}}
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3 text-primary">
                            <i class="fas fa-user-circle fa-3x"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-secondary">{{ $admin->name }}</h4>
                            <small class="text-muted">Administrator</small>
                        </div>
                    </div>

                    <hr class="border-secondary">

                    {{-- Basic Info --}}
                    <div class="mb-4">
                        <p class="mb-1 fw-semibold text-secondary">Email:</p>
                        <p class="text-muted">{{ $admin->email }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="mb-1 fw-semibold text-secondary">Joined:</p>
                        <p class="text-muted">{{ optional($admin->created_at)->format('F j, Y') ?? 'N/A' }}</p>
                    </div>

                    <hr class="border-secondary">

                    {{-- Update Form --}}
                    <h5 class="mb-4 mt-5 text-primary fw-bold">Update Profile</h5>

                    @if(session('success'))
                    <div class="alert alert-success rounded-3">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="name" class="form-label text-secondary fw-semibold">Name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $admin->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                required
                                style="border-radius: .375rem;"
                            >
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label text-secondary fw-semibold">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $admin->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                                style="border-radius: .375rem;"
                            >
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password Fields --}}
                        <style>
                            .password-wrapper {
                                position: relative;
                            }
                            .toggle-password-btn {
                                position: absolute;
                                top: 50%;
                                right: 12px;
                                transform: translateY(-50%);
                                border: none;
                                background: transparent;
                                cursor: pointer;
                                padding: 0;
                                display: flex;
                                align-items: center;
                                color: #6c757d;
                                transition: color 0.3s ease;
                            }
                            .toggle-password-btn:hover {
                                color: #0d6efd;
                            }
                            .toggle-password-btn svg {
                                width: 20px;
                                height: 20px;
                                fill: currentColor;
                            }
                        </style>

                        <div class="mb-4 password-wrapper">
                            <label for="old_password" class="form-label text-secondary fw-semibold">Old Password</label>
                            <input
                                type="password"
                                id="old_password"
                                name="old_password"
                                class="form-control @error('old_password') is-invalid @enderror"
                                autocomplete="current-password"
                                style="border-radius: .375rem;"
                            >
                            <button type="button" class="toggle-password-btn" data-target="old_password" tabindex="-1" aria-label="Toggle Old Password Visibility">
                                <!-- Eye icon -->
                                <svg viewBox="0 0 24 24" class="eye-icon">
                                    <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>
                                <!-- Eye slash icon (hidden by default) -->
                                <svg viewBox="0 0 24 24" class="eye-slash-icon" style="display:none;">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-10-7-10-7a20.5 20.5 0 0 1 3.1-4.3M1 1l22 22" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <path d="M9.88 9.88a3 3 0 0 0 4.24 4.24" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </button>
                            @error('old_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 password-wrapper">
                            <label for="new_password" class="form-label text-secondary fw-semibold">New Password</label>
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror"
                                autocomplete="new-password"
                                style="border-radius: .375rem;"
                            >
                            <button type="button" class="toggle-password-btn" data-target="new_password" tabindex="-1" aria-label="Toggle New Password Visibility">
                                <svg viewBox="0 0 24 24" class="eye-icon">
                                    <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>
                                <svg viewBox="0 0 24 24" class="eye-slash-icon" style="display:none;">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-10-7-10-7a20.5 20.5 0 0 1 3.1-4.3M1 1l22 22" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <path d="M9.88 9.88a3 3 0 0 0 4.24 4.24" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </button>
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 password-wrapper">
                            <label for="new_password_confirmation" class="form-label text-secondary fw-semibold">Confirm New Password</label>
                            <input
                                type="password"
                                id="new_password_confirmation"
                                name="new_password_confirmation"
                                class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                autocomplete="new-password"
                                style="border-radius: .375rem;"
                            >
                            <button type="button" class="toggle-password-btn" data-target="new_password_confirmation" tabindex="-1" aria-label="Toggle Confirm Password Visibility">
                                <svg viewBox="0 0 24 24" class="eye-icon">
                                    <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>
                                <svg viewBox="0 0 24 24" class="eye-slash-icon" style="display:none;">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-10-7-10-7a20.5 20.5 0 0 1 3.1-4.3M1 1l22 22" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <path d="M9.88 9.88a3 3 0 0 0 4.24 4.24" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </button>
                            @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary fw-semibold px-4 py-2 rounded-3">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-password-btn').forEach(button => {
        button.addEventListener('click', () => {
            const targetInput = document.getElementById(button.dataset.target);
            if (!targetInput) return;

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                button.querySelector('.eye-icon').style.display = 'none';
                button.querySelector('.eye-slash-icon').style.display = 'inline';
            } else {
                targetInput.type = 'password';
                button.querySelector('.eye-icon').style.display = 'inline';
                button.querySelector('.eye-slash-icon').style.display = 'none';
            }
        });
    });
</script>
@endsection
