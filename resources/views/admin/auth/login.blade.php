<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fiber Ops - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="shortcut icon" type="image/png" href="{{ Setting::get('site_icon') }}"/>


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0; padding: 0;
            height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a2980, #26d0ce);
            background-size: 200% 200%;
            animation: gradientMove 6s ease infinite;
        }
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .login-card {
            max-width: 400px;
            width: 90%;
            padding: 40px 30px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            backdrop-filter: blur(12px);
            color: white;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        }
        .login-card img {
            height: 80px;
            margin-bottom: 15px;
        }
        .btn-custom {
            background: #007bff; 
            border: none;
            font-weight: bold;
            border-radius: 25px;
        }
        .btn-custom:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
<div class="login-card text-center">
    <img src="{{ Setting::get('site_icon') }}" alt="Logo">
    <h3 class="fw-bold mb-4">Fiber Ops</h3>

    <form method="POST" action="/admin/login">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <!-- Email Input -->
        <div class="input-group mb-3">
            <span class="input-group-text bg-transparent text-white"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" class="form-control bg-transparent text-white" placeholder="Email Address" name="email" required>
        </div>

        <!-- Password Input with Toggle -->
        <div class="input-group mb-4">
            <span class="input-group-text bg-transparent text-white"><i class="fa-solid fa-lock"></i></span>
            <input type="password" class="form-control bg-transparent text-white" id="passwordField" placeholder="Password" name="password" required>
            <span class="input-group-text bg-transparent text-white" id="togglePassword" style="cursor:pointer;">
                <i class="fa-solid fa-eye"></i>
            </span>
        </div>

        <button class="btn btn-custom w-100 py-2">Log In</button>
    </form>

    <p class="mt-4 mb-0">&#169; <span id="year"></span> Tera Software Ltd | All Rights Reserved</p>
</div>

<script>
    document.getElementById("year").textContent = new Date().getFullYear();

    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('passwordField');
    togglePassword.addEventListener('click', function () {
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        this.innerHTML = type === 'password' ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
    });
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
