@extends('admin.layout.base')

@section('title', 'Change Provider Password')

@section('content')
<style>
    /* ===== Custom Styling ===== */
    .password-box {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        padding: 35px 40px;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .password-box:hover {
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
    }

    .password-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    .password-header h5 {
        font-weight: 600;
        color: #333;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .password-header a {
        color: #007bff;
        font-size: 14px;
        text-decoration: none;
    }

    .password-header a:hover {
        text-decoration: underline;
    }

    .form-label {
        font-weight: 500;
        color: #444;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        transition: 0.2s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }

    .btn-primary {
        background: linear-gradient(90deg, #007bff, #0056d2);
        border: none;
        border-radius: 8px;
        padding: 10px 25px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #0056d2, #0041a8);
        transform: translateY(-2px);
    }

    .btn-light.border:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
    }

    .toggle-password:hover {
        color: #007bff;
    }
</style>

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="password-box">

            {{-- Header: Title + Back Button in One Line --}}
            <div class="password-header">
                <h5><i class="fa fa-lock"></i> Change Provider Password</h5>
                <a href="{{ route('admin.provider.index') }}"><i class="fa fa-angle-left"></i> Back</a>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @elseif(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            {{-- Password Change Form --}}
            <form action="{{ route('admin.provider.passwordchange', $provider->id) }}" method="POST" autocomplete="off">
                @csrf
                @method('PATCH')

                <div class="form-group position-relative mb-4">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" 
                           class="form-control" 
                           name="password" 
                           id="password" 
                           placeholder="Enter new password" 
                           required>
                    <span class="toggle-password" onclick="togglePassword('password', this)">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>

                <div class="form-group position-relative mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" 
                           class="form-control" 
                           name="password_confirmation" 
                           id="confirm_password" 
                           placeholder="Confirm new password" 
                           required>
                    <span class="toggle-password" onclick="togglePassword('confirm_password', this)">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>

                <div class="form-group mt-4 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Change Password
                    </button>
                    <a href="{{ route('admin.provider.index') }}" class="btn btn-light border">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JS for show/hide password + auto fade alerts --}}
<script>
    function togglePassword(id, element) {
        const input = document.getElementById(id);
        const icon = element.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Auto-hide alert after 3s
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => el.classList.remove('show'));
    }, 3000);
</script>
@endsection
