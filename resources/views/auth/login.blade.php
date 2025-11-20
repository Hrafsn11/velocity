<x-guest-layout>
    <h4 class="mb-2 fw-bold">Welcome! 👋</h4>
    <p class="mb-4">Please sign-in to your account and start the adventure</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
        @csrf
        
        <div class="form-floating form-floating-outline mb-3">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" autofocus required />
            <label for="email">Email</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <div class="form-password-toggle">
                <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                        <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                        <label for="password">Password</label>
                    </div>
                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="mb-3 d-flex justify-content-between">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember" />
                <label class="form-check-label" for="remember_me"> Remember Me </label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="float-end mb-1">
                    <span>Forgot Password?</span>
                </a>
            @endif
        </div>
        
        <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
        </div>
    </form>

    @if (Route::has('register'))
    <p class="text-center">
        <span>New on our platform?</span>
        <a href="{{ route('register') }}">
            <span>Create an account</span>
        </a>
    </p>
    @endif

    <div class="divider my-4">
        <div class="divider-text">Demo Accounts</div>
    </div>

    <div class="alert alert-info" role="alert">
        <strong>Super Admin:</strong> admin@admin.com / password<br>
        <strong>Admin:</strong> admin@example.com / password<br>
        <strong>User:</strong> user@example.com / password
    </div>
</x-guest-layout>
