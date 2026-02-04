<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Register - Power Monitoring</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" >
    <!-- CSS Files -->
    <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/now-ui-dashboard.css?v=1.5.0" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif !important; overflow-x: hidden; }
        .wrapper { 
            background: url('{{ asset('assets/img/bg-login.jpg') }}') no-repeat center center; 
            background-size: cover;
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .card-login { 
            width: 100%; max-width: 450px; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
            background: white; 
            border: none;
        }
        .btn-primary { background: #f96332 !important; border: none; font-weight: 800; font-size: 0.9rem; }
        .form-control { font-size: 0.9rem; }
        .input-group-text i { color: #aaa; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card card-login">
            <div class="card-header text-center pb-2">
                <h4 class="card-title font-weight-bold mb-1" style="color: #333;">Create Account</h4>
                <p class="text-muted small font-weight-bold text-uppercase">Join Power Monitoring</p>
            </div>
            
            <div class="card-body pt-2">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                                        
                    <div class="input-group no-border input-lg mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="now-ui-icons users_circle-08"></i></span>
                        </div>
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required autofocus>
                    </div>

                    <div class="input-group no-border input-lg mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="now-ui-icons ui-1_email-85"></i></span>
                        </div>
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                    </div>

                    <div class="input-group no-border input-lg mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="now-ui-icons objects_key-25"></i></span>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="Password (Min 8 chars)" required>
                    </div>

                    <div class="input-group no-border input-lg mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="now-ui-icons objects_key-25"></i></span>
                        </div>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger p-2 mb-3" style="font-size: 0.8rem; border-radius: 8px;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary btn-round btn-lg btn-block mb-3 shadow">REGISTER</button>
                    
                    <div class="text-center mt-3">
                        <span class="text-muted small">Already have an account? <a href="{{ route('login') }}" class="font-weight-bold text-primary">Login</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
