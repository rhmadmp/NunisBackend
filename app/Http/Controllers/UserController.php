<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    function register(Request $req)
    {
        $user = new User;
        $user->nama = $req->input('nama');
        $user->email = $req->input('email');
        $user->password = Hash::make($req->input('password'));
        $user->phone = $req->input('phone');
        $user->address = $req->input('address');
        $user->status = $req->input('status');
        $user->save();
        return $user;
    }
    // UserController.php

    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'image' => 'required|string'
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Simpan base64 string langsung ke database
        $user->pictures = $request->image;
        $user->save();
    
        return response()->json([
            'success' => true, 
            'image_url' => $user->pictures
        ]);
    }

   
    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $req->email)->first();

        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json(["Error" => "Sorry, email or password doesn't match"], 401);
        }

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function deleteUser($id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json(["Error" => "User not found"], 404);
        }

        // Delete the user
        $user->delete();

        // Return success response
        return response()->json(["Message" => "User deleted successfully"], 200);
    }
    public function index()
    {
        // Fetch all users from the database
        $users = User::all();

        // Return users as JSON response
        return response()->json($users);
    }
    public function getoneuser($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(["Error" => "User not found"], 404);
        }

        return response()->json($user);
    }
    public function getpicturebyemail($email)
{
    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json(["Error" => "User not found"], 404);
    }
    return response()->json([
        'picture' => $user->pictures
    ]);
}
}
