<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoanController extends Controller
{
    public function index()
    {
        // get data from loan model
        $loans = Loan::with(['user', 'book'])->latest()->get();
        //ini biar ngambil data siswa dan buku yang stock nya ada
        $members = User::where('role', 'siswa')->orderBy('name', 'ASC')->get();
        $books = Book::where('stock', '>', 0)->orderBy('title', 'ASC')->get();

        return view('loans.index', compact('loans', 'members', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user,id',
            'books_id' => 'required|exists:books,id',
        ]);

        $book = Book::findorfai($request->book_id);

        if ($book->stock < 1) {
            # code...
            return back()->with('err', 'maap stok nya lagi abiisss');
        }

        Loan::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
            //ini biar pas minjem buku dia otomatis ambhil waktu hari ini
            'borrow_date' => Carbon::now()->toDateString(),
            // dibuat kosong karna belum di kembalikn 
            'return_date' => null,
            // menyesuaikan  default nya adalah di pnjam
            'status' => 'borrowed',

        ]);

        $book->decrement('stock');
        return redirect()->route('loans.index')->with('success', 'Peminjaman buku berhasil dicatat!');
    }
    public function update(Request $request, string $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status === 'borrowed') {
            $loan->status = 'returned';
            $loan->save();

            $loan->book->increment('stock');

            return redirect()->route('loans.index')->with('success', 'buku berhasil di kembaikan');
        }


    }
    public function destroy(string $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status === 'borrowed') {
            $loan->book->increment('stock');
        }

        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'Data peminjaman berhasil dihapus!');
    }
    public function borrowBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $user = Auth::user();

        if ($book->stock <= 0) {
            return back()->with('error', 'Oops! Buku ini baru saja habis dipinjam orang lain.');
        }

        $alreadyBorrowed = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->exists();

        if ($alreadyBorrowed) {
            return back()->with('error', 'Kamu tidak bisa meminjam buku ini karena kamu belum mengembalikannya.');
        }


        $book->decrement('stock');

        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrow_date' => Carbon::now()->toDateString(),
            'return_date' => null,
            'status' => 'borrowed',
        ]);

        return redirect()->route('my.books')->with('success', 'Buku berhasil dipinjam! Selamat membaca.');
    }

}
