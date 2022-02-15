<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
 
class AuthController extends Controller
{
 
    public function index()
    {
        return view('auth.login');
    }
 
    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
 
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('profile')
                        ->withSuccess('Signed in');
        }
 
        return redirect("login")->withSuccess('Login details are not valid');
    }
 
    public function registration()
    {
        return view('auth.registration');
    }
 
    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
 
        $data = $request->all();
        $check = $this->create($data);
        
        return redirect("dashboard");
    }
 
    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
      session()->flash('success','User Registered Successfully.');
    }
 
    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }
    
        return redirect("login")->withSuccess('You are not allowed to access');
    }
 
    public function signOut() {
        Session::flush();
        Auth::logout();
 
        return Redirect('login');
    }
    public function profile()
    {
        return view('profile');
    }
     public function edit()
    {

        return view('auth.edit');
    }

 public function update(Request $request, $id)
    {
        $rules = [
         'name' => 'required',
            'email' => 'required|email|unique:users',
                 'password' => 'required|min:6',
        ];

        $messages = [
            'name.required'         =>  'Your first name is required.',
            'email.required'        =>  'Your emails address is required.',
            'email.unique'          =>  'That email address is already in use.',
            'password.required'     =>  'Your username is required.',
            'password.unique'       =>  'That username is already in use.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails())
        {
            return Redirect::to('edit-profile')
                ->withErrors($validator)
                ->withInput();
        }else{
            $user = User::find($id);
            $user->name = $request->input('name');
            if($user->email !== $request->input('email'))
            {
                $user->email = $request->input('email');
            }
            if($user->password !== $request->input('password'))
            {
                $user->password = $request->input('password');
            }
            $user->save();

            Session::flash('success', 'Your profile was updated.');
            return Redirect::to('edit-profile');
        }
    }
}
