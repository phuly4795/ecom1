<x-guest-layout>
    @section('title', 'Cập nhật mật khẩu')
    <div class="container">
        <div class="reset-password-container">
            <div class="reset-password-logo">
                <i class="fa fa-lock"></i>
            </div>
            <h2 class="reset-password-title">Cập nhật mật khẩu</h2>

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

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="mt-4 mb-3" style="margin-bottom: 3%">
                    <label for="email">Địa chỉ email</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control"
                            value="{{ old('email', $request->email) }}" readonly required autocomplete="username">
                    </div>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif


                </div>

                <!-- Password -->
                <div class="mt-4 mb-3" style="margin-bottom: 3%">
                    <x-input-label for="password" :value="__('Mật khẩu')" />
                    <x-text-input id="password"
                        class="block form-control mt-1 w-full  {{ $errors->has('password') ? 'has-error' : '' }}"
                        type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4" style="margin-bottom: 3%">
                    <x-input-label for="password_confirmation" :value="__('Nhập lại mật khẩu')" />

                    <x-text-input id="password_confirmation"
                        class="block form-control mt-1 w-full {{ $errors->has('password_confirmation') ? 'has-error' : '' }}"
                        type="password" name="password_confirmation" required autocomplete="new-password" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary reset-password-btn">
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

        .reset-password-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .reset-password-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .reset-password-logo i {
            font-size: 60px;
            color: #337ab7;
        }

        .reset-password-title {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .reset-password-btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .reset-password-footer {
            margin-top: 20px;
            text-align: center;
            color: #777;
        }

        .reset-password-footer a {
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
