<?php
namespace App\Http\Controllers;
use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    // --- AREA ADMIN ---
    public function index() {
        return view('loans.index', [
            'loans' => Loan::all(),
            'members' => User::where('role', 'siswa')->get(),
            'books' => Book::where('stock', '>', 0)->get()
        ]);
    }

    public function store(Request $request) {
        Loan::create(['user_id' => $request->user_id, 'book_id' => $request->book_id, 'status' => 'borrowed']);
        Book::find($request->book_id)->decrement('stock');
        return back();
    }

    public function update(Request $request, $id) {
        $loan = Loan::find($id);
        $loan->update(['status' => 'returned']);
        $loan->book->increment('stock');
        return back();
    }

    public function destroy($id) {
        Loan::find($id)->delete();
        return back();
    }

    // --- AREA SISWA ---
    public function borrowBook($id) {
        Loan::create(['user_id' => Auth::id(), 'book_id' => $id, 'status' => 'borrowed']);
        Book::find($id)->decrement('stock');
        return redirect()->route('my.books');
    }

    public function myBooks() {
        return view('my-books', ['loans' => Loan::where('user_id', Auth::id())->get()]);
    }

    public function returnBook($id) {
        $loan = Loan::find($id);
        $loan->update(['status' => 'returned']);
        $loan->book->increment('stock');
        return back();
    }
}