@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
      <div class="card shadow rounded">
        <div class="card-header" style="background-color: #264653; color: white;">
          <h4 class="mb-0">Edit Holiday</h4>
        </div>

        <div class="card-body">
          <form action="{{ route('holidays.update', $holiday->h_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="name" class="form-label fw-semibold">Holiday Name</label>
              <input type="text" name="name" class="form-control" value="{{ $holiday->name }}" required>
            </div>

            <div class="mb-3">
              <label for="date" class="form-label fw-semibold">Date</label>
              <input type="date" name="date" class="form-control" value="{{ $holiday->date }}" required>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label fw-semibold">Description</label>
              <input type="text" name="description" class="form-control" value="{{ $holiday->description }}" required>
            </div>

            <div class="d-flex justify-content-between">
              <a href="{{ route('holidays') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Cancel
              </a>
              <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
                <i class="bi bi-check-circle me-1"></i> Update Holiday
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</div>

@endsection
