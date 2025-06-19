<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('/storage/hhh.avif'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            

        }

        .login-container {
            background-color: white; 
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 150px; 
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="storage/love.png" alt="Logo" class="logo" style="width:100px; height:60px;"> 
        {{--  <form action="{{ route('forgotPassword') }}" method="POST" enctype="multipart/form-data" class="login100-form validate-form">
            @csrf
       <div class="mb-4">
        <label for="email" class="form-label">Email address</label>
           <input type="text" id="email_address" class="form-control form-control-lg bg-light fs-6 @error('email') is-invalid @enderror required autofocus" name="email" placeholder="Email address">
       </div>
       @error('email')
       <div class="is-invalid my-1"><span class="text-danger fs-6">User's Email does not match.</span></div>
       @enderror


        <div class="d-grid gap-2">
           <button type="submit" class="btn btn-primary">Reset Password Link</button>
         </div>
   </form>  --}}
   <form action="{{ route('resetPassword') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="text" id="email_address"  class="form-control form-control-lg bg-light fs-6 @error('email') is-invalid @enderror required autofocus" name="email" placeholder="Email Address">
        @if ($errors->has('email'))
                <span class="text-danger">{{ $errors->first('email') }}</span>
        @endif
    </div>
    <div class="mb-3">
        <label for="password-confirm" class="form-label">Password</label>
        <input type="password-confirm" id="password"  class="form-control form-control-lg bg-light fs-6 @error('password') is-invalid @enderror required autofocus" name="password" placeholder="Password">
        @if ($errors->has('password'))
                <span class="text-danger">{{ $errors->first('password') }}</span>
        @endif
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password Confirm</label>
        <input type="password" id="password-confirm"  class="form-control form-control-lg bg-light fs-6 @error('password') is-invalid @enderror required autofocus" name="password_confirmation" placeholder="Confrimation Password">
        @if ($errors->has('password-confirm'))
                <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
        @endif
    </div>
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">Reset</button>
      </div>
</form>
    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
