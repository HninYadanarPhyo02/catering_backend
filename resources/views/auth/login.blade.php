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
            background-color: #1f2937; 
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
        <!-- <img src="storage/love.png" alt="Logo" class="logo" style="width:100px; height:60px;">  -->
        <!-- <img src="{{ asset('images/food.png') }}" alt="System Logo" class="img-fluid" style="max-height: 100px;"> -->
        <div class="d-flex justify-content-center mb-3">
    <img src="{{ asset('images/food.png') }}" alt="System Logo" 
         class="img-fluid" style="max-height: 100px;">
</div>

        <form action="{{ route('loginAuth') }}" method="post">
            @csrf
            <div class="mb-3">
            <label for="email" class="form-label" style="color:#ffffff;">Email address</label>
            <input type="email" name="email" class="form-control form-control-lg bg-light fs-6 @error('email') is-invalid @enderror"  placeholder="Email address">
                @error('email')
                    <div class="is-invalid my-1"><span class="text-danger fs-6">User's Email and Password does not match.</span></div>
                @enderror           
            </div>
            <div class="mb-4">
                <label for="password" class="form-label" style="color:#ffffff">Password</label>
              <input type="password" class="form-control form-control-lg bg-light fs-6 @error('password') is-invalid @enderror" name="password" placeholder="Password">
                @error('password')
                    <div class="is-invalid mb-1"><span class="text-danger">User's Email and Password does not match.</span></div>
                @enderror           
             </div>
                <div class="input-group mb-4 d-flex justify-content-between">
                <div></div>
                <div class="col-md-4">
                    <small><a href="{{ url('/register') }}" style="color: #ffffff; text-align: left !important;">Register?</a></small>
                </div>
                <div class="forgot col-md-6">
                    <small><a href="{{ route('forgotPasswordPage') }} " style="color: #ffffff">Reset Password?</a></small>
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" style="background-color:rgb(140, 149, 153); color: black; border: 2px solid rgb(140, 149, 153);">Login</button>
            </div>
         </form> 
    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
