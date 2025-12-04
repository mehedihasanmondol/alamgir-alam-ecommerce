<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

/**
 * ModuleName: Multi-Step Login
 * Purpose: Handle multi-step login flow (check user exists, then login or register)
 * 
 * Steps:
 * 1. User enters email/mobile
 * 2. Check if user exists
 * 3a. If exists: Show password field for login
 * 3b. If new: Show password and confirm password for registration
 * 
 * @category Livewire
 * @package  App\Livewire\Auth
 * @author   Admin
 * @created  2025-11-19
 */
class MultiStepLogin extends Component
{
    public $emailOrMobile = '';
    public $password = '';
    public $password_confirmation = ''; // Laravel expects snake_case for confirmed validation
    public $name = '';
    public $step = 1; // 1 = email/mobile, 2 = password (login or register)
    public $userExists = false;
    public $keepSignedIn = false;

    protected $listeners = ['resetForm' => 'resetToStep1'];

    /**
     * Validation rules for step 1
     */
    protected function rulesStep1(): array
    {
        return [
            'emailOrMobile' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Check if it's a valid email or mobile number
                    $isEmail = filter_var($value, FILTER_VALIDATE_EMAIL);
                    $isMobile = preg_match('/^[0-9]{10,15}$/', $value);
                    
                    if (!$isEmail && !$isMobile) {
                        $fail('Please enter a valid email address or mobile number.');
                    }
                },
            ],
        ];
    }

    /**
     * Validation rules for step 2 (login)
     */
    protected function rulesStep2Login(): array
    {
        return [
            'emailOrMobile' => 'required|string',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Validation rules for step 2 (register)
     */
    protected function rulesStep2Register(): array
    {
        return [
            'emailOrMobile' => 'required|string',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Check if user exists and move to step 2
     */
    public function checkUser()
    {
        $this->validate($this->rulesStep1());

        // Determine if input is email or mobile
        $isEmail = filter_var($this->emailOrMobile, FILTER_VALIDATE_EMAIL);
        
        // Check if user exists
        if ($isEmail) {
            $user = User::where('email', $this->emailOrMobile)->first();
        } else {
            $user = User::where('mobile', $this->emailOrMobile)->first();
        }

        if ($user) {
            $this->userExists = true;
            $this->name = $user->name;
        } else {
            $this->userExists = false;
        }

        $this->step = 2;
    }

    /**
     * Handle login for existing user
     */
    public function login()
    {
        $this->validate($this->rulesStep2Login());

        $credentials = $this->getCredentials();
        $credentials['password'] = $this->password;

        if (Auth::attempt($credentials, $this->keepSignedIn)) {
            request()->session()->regenerate();

            // Update user preferences and last login
            Auth::user()->update([
                'keep_signed_in' => $this->keepSignedIn,
                'last_login_at' => now(),
            ]);

            // Redirect based on role
            $user = Auth::user();
            
            if ($user->role === 'customer') {
                return redirect()->intended(route('customer.profile'));
            }
            
            if (in_array($user->role, ['admin', 'author'])) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'password' => ['The password you entered is incorrect.'],
        ]);
    }

    /**
     * Handle registration for new user
     */
    public function register()
    {
        $this->validate($this->rulesStep2Register());

        // Determine if input is email or mobile
        $isEmail = filter_var($this->emailOrMobile, FILTER_VALIDATE_EMAIL);

        // Create user
        $userData = [
            'name' => $this->name,
            'password' => Hash::make($this->password),
            'role' => 'customer', // Default role
            'is_active' => true,
        ];

        if ($isEmail) {
            $userData['email'] = $this->emailOrMobile;
            $userData['mobile'] = null;
        } else {
            $userData['mobile'] = $this->emailOrMobile;
            $userData['email'] = null;
        }

        $user = User::create($userData);

        // Login the user
        Auth::login($user, $this->keepSignedIn);
        request()->session()->regenerate();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Redirect to customer profile
        return redirect()->intended(route('customer.profile'));
    }

    /**
     * Submit form based on user existence
     */
    public function submit()
    {
        if ($this->step === 1) {
            $this->checkUser();
        } elseif ($this->step === 2) {
            if ($this->userExists) {
                $this->login();
            } else {
                $this->register();
            }
        }
    }

    /**
     * Go back to step 1
     */
    public function backToStep1()
    {
        $this->step = 1;
        $this->password = '';
        $this->password_confirmation = '';
        $this->name = '';
        $this->resetValidation();
    }

    /**
     * Reset form to step 1
     */
    public function resetToStep1()
    {
        $this->emailOrMobile = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->name = '';
        $this->step = 1;
        $this->userExists = false;
        $this->resetValidation();
    }

    /**
     * Get credentials array for authentication
     */
    protected function getCredentials(): array
    {
        $isEmail = filter_var($this->emailOrMobile, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'mobile';
        
        return [$field => $this->emailOrMobile];
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.auth.multi-step-login');
    }
}
