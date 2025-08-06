<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Traits\RequestResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    use RequestResponse;

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    'min:6',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^()\-_=+{};:,<.>]).+$/',
                ],
            ]);

            $email = $request->email;

            $key = "resend-verification:" . sha1($email);

            // Check throttle before doing anything
            if (RateLimiter::tooManyAttempts($key, 1)) {
                $seconds = RateLimiter::availableIn($key);
                return $this->error('Verification already sent. Please wait ' . ceil($seconds / 60) . ' minute(s).');
            }

            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                if (!$existingUser->hasVerifiedEmail()) {
                    $existingUser->sendEmailVerificationNotification();
                    RateLimiter::hit($key, 300); // Lock for 5 minutes

                    $existingUser->name = $request->name;
                    $existingUser->password = bcrypt($request->password);
                    $existingUser->save();

                    return $this->success('Verification email resent. Please check your inbox.');
                }

                return $this->error('This email is already taken.');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => bcrypt($request->password),
            ]);

            $user->sendEmailVerificationNotification();
            RateLimiter::hit($key, 300); // Lock for 5 minutes

            return $this->success('Registration successful. Please check your email for verification.');
        } catch (\Exception $e) {
            return $this->internalServerError('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $fields = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $fields['email'])->first();
            if (!$user || !Hash::check($fields['password'], $user->password)) {
                return $this->error('Invalid credentials');
            }

            if (!$user->hasVerifiedEmail()) {
                return $this->error('Please verify your email address.');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success('Login successful', ['token' => $token, 'user' => $user]);
        } catch (\Exception $e) {
            return $this->internalServerError('Login failed: '.$e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return $this->success('Logged out');
        } catch (\Exception $e) {
            return $this->internalServerError('Logout failed: '.$e->getMessage());
        }
    }

    public function verify(Request $request, $id, $hash)
    {
        try {
            $user = User::findOrFail($id);

            if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                return $this->forbidden('Invalid verification link.');
            }

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }

            return response()->view('auth.verify-success');
        } catch (\Exception $e) {
            return $this->internalServerError('Verification failed: '.$e->getMessage());
        }
    }
}
