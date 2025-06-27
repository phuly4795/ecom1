<x-guest-layout>
    @section('title', 'Quên mật khẩu')
    <div class="container">
        <div class="forgot-password-container">
            <div class="forgot-password-logo">
                <i class="fa fa-lock"></i>
            </div>
            <h2 class="forgot-password-title">Quên mật khẩu</h2>

            <!-- Hiển thị thông báo lỗi nếu có -->
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control"
                            value="{{ old('email') }}" placeholder="Nhập email của bạn" required autofocus>
                    </div>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <!-- Nút đăng nhập -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary forgot-password-btn">
                        <i class="fa fa-sign-in"></i> Xác nhận
                    </button>
                </div>

            </form>

        </div>
    </div>
    <style>
        body {
            background-color: #f5f5f5;
        }

        .forgot-password-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .forgot-password-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .forgot-password-logo i {
            font-size: 60px;
            color: #337ab7;
        }

        .forgot-password-title {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .forgot-password-btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .forgot-password-footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
        }

        .forgot-password-footer a {
            color: #337ab7;
        }

        .has-error .form-control {
            border-color: #a94442;
        }

        .help-block {
            color: #a94442;
        }
    </style>
</x-guest-layout>
