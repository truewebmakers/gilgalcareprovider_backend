<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<style>
    .logo {
        text-align: center;
        padding: 10px 0;
        margin: 15px 0;
    }
    input#email {
        background: #d3d3d3;
    }

    .login {
    text-align: center;
    margin: 30px;
}
</style>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="logo">
                <img style="height: 50px;" src="https://gilgalcareprovider.s3.ap-southeast-1.amazonaws.com/logo/logo-bgr.png">
            </div>
            @if(session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Reset Password</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ request()->input('email') }}" required readonly>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <input type="checkbox" id="showPassword" class="form-check-input ms-2"> Show Password
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                        </form>
                    </div>

                </div>
            </div>
            <div class="login">
                <a href="https://gilgalcareprovider.hyperiontech.com.au/login" class="btn btn-success w-30"> Login to Website </a>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS (Optional for form validation or other interactions) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom script for show/hide password -->
    <script>
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            const passwordConfirmationField = document.getElementById('password_confirmation');

            if (this.checked) {
                passwordField.type = 'text';
                passwordConfirmationField.type = 'text';
            } else {
                passwordField.type = 'password';
                passwordConfirmationField.type = 'password';
            }
        });
    </script>
</body>
</html>
