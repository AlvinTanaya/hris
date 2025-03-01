<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/IconTimurJayaIndosteel.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #2c3e50);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        /* Decorative shapes */
        .shape {
            position: absolute;
            opacity: 0.2;
            z-index: -1;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(#ffc107, #ff6b6b);
            top: -100px;
            right: -50px;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(#4facfe, #00f2fe);
            bottom: -100px;
            left: -50px;
            clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            background: linear-gradient(#f093fb, #f5576c);
            bottom: 50px;
            right: 20%;
            clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
            transform: rotate(45deg);
        }

        .otp-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .otp-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #ffc107, #ff6b6b, #4facfe, #00f2fe, #f093fb, #f5576c);
            z-index: 2;
        }

        h3 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 1.8rem;
            background: linear-gradient(90deg, #ffc107, #ff6b6b);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        h3::after {
            content: "";
            position: absolute;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #ffc107, #ff6b6b);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 3px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .btn-primary {
            background: linear-gradient(90deg, #ffc107, #ff6b6b);
            border: none;
            width: 100%;
            font-weight: 600;
            padding: 0.8rem;
            margin-top: 0.5rem;
            transition: 0.3s;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #ff6b6b, #ffc107);
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            font-weight: 500;
            padding: 0.8rem;
            margin-top: 1rem;
            transition: 0.3s;
            border-radius: 10px;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            color: white;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            color: #ff6b6b;
            margin-top: 0.5rem;
        }

        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            border: none;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border-left: 4px solid #dc3545;
        }

        .back-icon {
            margin-right: 0.5rem;
        }
    </style>
</head>

<body>
    <!-- Decorative shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="otp-container">
        <h3>OTP Verification</h3>

        <!-- Flash Message -->
        @if(session('status'))
        <div class="alert alert-success text-center">{{ session('status') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        <!-- Form Send OTP -->
        <form id="send-otp-form" method="POST">

            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" required autocomplete="off" placeholder="Enter your email">
                @error('email')
                <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Send OTP</button>
        </form>





        <!-- Back Button -->
        <a href="{{ route('login') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left back-icon"></i>Back to Login Page
        </a>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Optional: Axios for AJAX (alternative to jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#send-otp-form').submit(function(e) {
                e.preventDefault(); // Prevent default form submission

                let email = $('#email').val();
                let _token = $('input[name="_token"]').val();

                $.ajax({
                    url: "{{ route('password.otp.send') }}",
                    method: "POST",
                    data: {
                        email: email,
                        _token: _token
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Sending OTP...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        }).then(() => {
                            window.location.href = "{{ route('otp.verify') }}?email=" + encodeURIComponent(response.email);
                        });
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response?.message || 'Something went wrong!',
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>