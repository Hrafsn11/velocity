<x-guest-layout>
    <div class="text-center mb-4">
        <h5 class="mb-1 fw-bold">Welcome Back! 👋</h5>
        <p class="text-muted small mb-0">Sign in to access your workspace.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success p-2 text-center small mb-3 rounded" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Email</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text ps-3"><i class="ti ti-mail text-muted"></i></span>
                <input type="email" class="form-control" id="email" name="email" 
                       value="{{ old('email') }}" placeholder="name@velocity.com" autofocus required />
            </div>
            @error('email') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
        </div>
        
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="small fw-bold text-primary text-decoration-none">Forgot?</a>
                @endif
            </div>
            <div class="input-group input-group-merge form-password-toggle">
                <span class="input-group-text ps-3"><i class="ti ti-lock text-muted"></i></span>
                <input type="password" id="password" class="form-control" name="password" 
                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                <span class="input-group-text cursor-pointer pe-3"><i class="ti ti-eye-off"></i></span>
            </div>
            @error('password') <div class="invalid-feedback d-block small mt-1">{{ $message }}</div> @enderror
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember" />
                <label class="form-check-label text-muted small" for="remember_me">Keep me signed in</label>
            </div>
        </div>
        
        <button class="btn btn-primary d-grid w-100 fw-bold py-2" type="submit">
            Sign In
        </button>
    </form>

    <div class="text-center">
        @if (Route::has('register'))
            <p class="small text-muted mb-3">
                New here? <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none">Create account</a>
            </p>
        @endif

        <div class="bg-lighter rounded p-2 mt-3 border border-dashed d-inline-block w-100">
            <div class="small text-uppercase text-muted fw-bold mb-1" style="font-size: 0.65rem;">Quick Demo Login</div>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="fillLogin('admin@admin.com')">Admin</button>
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="fillLogin('user@example.com')">User</button>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</x-guest-layout>