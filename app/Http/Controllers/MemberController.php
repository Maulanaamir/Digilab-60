<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index() {
        return view('members.index', ['members' => User::where('role', 'siswa')->get(), 'memberToEdit' => null]);
    }

    public function store(Request $request) {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa'
        ]);
        return back();
    }

    public function edit($id) {
        return view('members.index', [
            'members' => User::where('role', 'siswa')->get(), 
            'memberToEdit' => User::find($id)
        ]);
    }

    public function update(Request $request, $id) {
        $user = User::find($id);
        $user->update(['name' => $request->name, 'email' => $request->email]);
        
        if($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        return redirect()->route('members.index');
    }

    public function destroy($id) {
        User::find($id)->delete();
        return back();
    }
}