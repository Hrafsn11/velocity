<x-guest-layout>
    <div class="text-center mb-4">
        <h5 class="mb-1 fw-bold">Adventure Starts Here 🚀</h5>
        <p class="text-muted small mb-0">Make your app management easy and fun!</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Username</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text ps-3"><i class="ti ti-user text-muted"></i></span>
                <input type="text" 
                       class="form-control ps-1 @error('name') is-invalid @enderror" 
                       id="name" name="name" 
                       value="{{ old('name') }}" 
                       placeholder="johndoe" 
                       autofocus required autocomplete="name" />
            </div>
            @error('name')
                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Email</label>
            <div class="input-group input-group-merge">
                <span class="input-group-text ps-3"><i class="ti ti-mail text-muted"></i></span>
                <input type="email" 
                       class="form-control ps-1 @error('email') is-invalid @enderror" 
                       id="email" name="email" 
                       value="{{ old('email') }}" 
                       placeholder="name@velocity.com" 
                       required autocomplete="username" />
            </div>
            @error('email')
                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Password</label>
            <div class="input-group input-group-merge form-password-toggle">
                <span class="input-group-text ps-3"><i class="ti ti-lock text-muted"></i></span>
                <input type="password" 
                       id="password" 
                       class="form-control ps-1 @error('password') is-invalid @enderror" 
                       name="password" 
                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                       required autocomplete="new-password" />
                <span class="input-group-text cursor-pointer pe-3"><i class="ti ti-eye-off"></i></span>
            </div>
            @error('password')
                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Confirm Password</label>
            <div class="input-group input-group-merge form-password-toggle">
                <span class="input-group-text ps-3"><i class="ti ti-shield-lock text-muted"></i></span>
                <input type="password" 
                       id="password_confirmation" 
                       class="form-control ps-1" 
                       name="password_confirmation" 
                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                       required autocomplete="new-password" />
                <span class="input-group-text cursor-pointer pe-3"><i class="ti ti-eye-off"></i></span>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" required />
                <label class="form-check-label small text-muted" for="terms-conditions">
                    I agree to <a href="javascript:void(0);" class="text-primary fw-bold text-decoration-none">privacy policy & terms</a>
                </label>
            </div>
        </div>

        <button class="btn btn-primary d-grid w-100 fw-bold py-2 shadow-sm" type="submit">
            Sign Up
        </button>
    </form>

    <p class="text-center mt-4 mb-0">
        <span class="text-muted small">Already have an account?</span>
        <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none small">
            Sign in instead
        </a>
    </p>
</x-guest-layout>