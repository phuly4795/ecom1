<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <i class="fa fa-lock"></i>
            </div>
            <h2 class="login-title">ĐĂNG NHẬP</h2>

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

            <form method="POST" action="{{ route('login') }}">
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

                <!-- Mật khẩu -->
                <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Nhập mật khẩu" required>
                    </div>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <!-- Nhớ mật khẩu -->
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                        </label>
                    </div>
                </div>

                <!-- Nút đăng nhập -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary login-btn">
                        <i class="fa fa-sign-in"></i> Đăng nhập
                    </button>
                </div>

                <!-- Quên mật khẩu -->
                <div class="text-center">
                    <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
                </div>
            </form>

            <!-- Đăng ký tài khoản mới -->
            @if (Route::has('register'))
                <div class="login-footer">
                    Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
                </div>
            @endif
        </div>
    </div>
    <style>
        body {
            background-color: #f5f5f5;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-logo i {
            font-size: 60px;
            color: #337ab7;
        }

        .login-title {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .login-footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
        }

        .login-footer a {
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
