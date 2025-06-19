<?php

namespace App\Http\Controllers;

use Rules\Password;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    // login
    public function adminLogin(Request $request)
    {
        // dd($request->all());
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();   // Prevent session issues

            if (Auth::user()->role == 'admin') {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('logout');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    // register
    public function registerPage()
    {
        return view('auth.register');
    }

    // public function register(Request $request)
    // {
    //     //  dd($request->all());
    //     $this->userValidationCheck($request);
    //     $data = $this->requestUserData($request);
    //     //     $lastEmployee = Employee::orderBy('emp_id', 'desc')->first();

    //     // // If no employees yet, start from 1
    //     //     $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;
    //     //     $newNumber = $lastNumber + 1;
    //     //     $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    //     Employee::create($data);

    //     return redirect()->route('dashboard');
    // }

    public function register(Request $request)
{
    $this->userValidationCheck($request);
    $data = $this->requestUserData($request);

    // Order by numeric part of emp_id descending
    $lastEmployee = Employee::orderByRaw('CAST(SUBSTRING(emp_id, 5) AS UNSIGNED) DESC')->first();

    $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;
    $newNumber = $lastNumber + 1;
    $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    $data['emp_id'] = $newEmpId;
    Employee::create($data);

    return redirect()->route('dashboard');
}

    

    private function userValidationCheck($request)
    {
        Validator::make($request->all(), [
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'gender' => 'required|in:male,female',
            'address' => 'required',
            'password' => 'required|min:6',
            'role' => 'required|in:user,admin',
        ]);
    }

    private function requestUserData($request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        ]);

        $lastEmployee = Employee::orderBy('emp_id', 'desc')->first();

        // If no employees yet, start from 1
        $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;
        $newNumber = $lastNumber + 1;
        $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        $password = $request->filled('password') ? $request->password : 'password123';
        return [
            'emp_id' => $newEmpId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'address' => $request->address,
            // 'password' => Hash::make($request->password),
            'password' => Hash::make($password),
            'role' => $request->role ?? 'employee',
        ];
    }

    // // forgotPasswordPage
    // public function forgotPasswordPage()
    // {
    //     return view('auth.forgot-password');
    // }

    // // forgotPassword
    // public function forgotPassword(Request $request)
    // {
    //     // dd($request->toArray());
    //     Validator::make($request->all(), [
    //         'email' => 'required|exists:users,email',
    //     ], [])->validate();

    //     $token = Str::random(64);
    //     DB::table('password_resets')->insert([
    //         'email' => $request->email,
    //         'token' => $token,
    //         'created_at' => Carbon::now(),
    //     ]);

    //     Mail::send('auth.forgetPasswordLink', ['token' => $token], function ($message) use ($request) {
    //         $message->from('nangpoepoeyee189@gmail.com');
    //         $message->to($request->email);
    //         $message->subject('Reset Password');
    //     });

    //     return back()->with(['message' => 'reset password has been send']);
    // }

    // resetPasswordPage
    public function resetPasswordPage($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // resetPassword
    public function resetPassword(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
        ], [])->validate();
        $data = DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->token,
        ])->first();
        if (!$data) {
            return back()->withInput()->with(['message' => 'something went wrong.']);
        }
    }

    //     $user = User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
    //     DB::table('password_resets')->where(['email' => $request->email])->delete();
    //     toast('Password has been changed', 'success');

    //     return redirect('/')->with('message', 'Password has been changed!');
    // }
    // //     public function dashboard()
    // // {
    // //     return view('admin.dashboard'); // Make sure this view exists
    // // }
    // public function logout(Request $request)
    // {
    //     Auth::logout();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect('/login');  // Redirect to login page after logout
    // }
    public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/login'); // or wherever
}
}