<?php

namespace App\Http\Controllers;

use App\Models\AssessmentRequest;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\BackendGuy;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = BackendGuy::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Store user in session (or use Auth)
            session(['backendGuy' => $user->id]);

            // Return a success message
            return redirect()->route('dashboard');
        }

        return response()->json(['message' => 'Invalid username or password'], 401);
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'username' => 'required|string|unique:backend_guys,username',
                'password' => 'required|string|confirmed|min:6',
            ]);

            $user = BackendGuy::create([
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
            ]);

            session(['backendGuy' => $user->id]);

            return redirect()->route('dashboard')->with('success', 'Registration successful!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'error' => 'An unexpected error occurred during registration. Please try again later.',
            ])->withInput();
        }
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); // Redirect to your home page or login page
    }

    public function getCurrentBackendGuy(Request $request)
    {
        $backendGuyId = $request->session()->get('backendGuy');
        if (!$backendGuyId) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $backendGuy = BackendGuy::find($backendGuyId);

        if (!$backendGuy) {
            return response()->json(['error' => 'Backend guy not found'], 404);
        }

        return response()->json([
            'id' => $backendGuy->id,
            'name' => $backendGuy->name
        ]);
    }

    public function appLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'experience_level' => $user->experience_level,
                    'jobType_Id' => $user->jobType_Id
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ], 401); // Use proper HTTP status code
    }

    public function getUserInfo($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Retrieve the job title based on the jobType_Id
        $jobTitle = 'Not specified';
        if ($user->jobType_Id) {
            $job = Job::find($user->jobType_Id); // Use the Job model for better readability
            $jobTitle = $job ? $job->jobType : 'Not specified';
        }

        $assessmentRequest = AssessmentRequest::where('user_id', $id)->orderBy('updated_at', 'desc')->first();

        $rating = null;

        if ($assessmentRequest) {
            $rating = $assessmentRequest->rating;
        }

        // Return the user information along with the job title
        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'experience_level' => $user->experience_level,
            'job_title' => $jobTitle,
            'rating' => $rating,
        ]);
    }

}
