<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    // admin

    public function index()
    {
        $loans = Loan::with(['user', 'book'])->latest()->get();
        
        $members = User::where('role', 'siswa')->orderBy('name', 'ASC')->get();
        $books = Book::where('stock', '>', 0)->orderBy('title', 'ASC')->get();

        return view('loans.index', compact('loans', 'members', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->stock < 1) {
            return back()->with('error', 'Maaf, stok buku ini sedang kosong!');
        }

        Loan::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
            'borrow_date' => Carbon::now()->toDateString(),
            'return_date' => null, 
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
            $loan->return_date = Carbon::now()->toDateString(); 
            $loan->save();

            // Kembalikan stok buku +1
            $loan->book->increment('stock');

            return redirect()->route('loans.index')->with('success', 'Buku telah dikembalikan dan stok diperbarui!');
        }

        return redirect()->route('loans.index')->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
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

    public function myBooks()
    {
        $loans = Loan::with('book')
                     ->where('user_id', Auth::id())
                     ->where('status', 'borrowed')
                     ->latest()
                     ->get();

        return view('my-books', compact('loans'));
    }
        public function returnBook($id)
    {
        // Cari data peminjaman yang valid (milik user yang sedang login & statusnya masih dipinjam)
        $loan = Loan::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->where('status', 'borrowed')
                    ->first();

        if ($loan) {
            $loan->update([
                'status' => 'returned',
                'return_date' => Carbon::now()->toDateString(),
            ]);

            // Kembalikan stok buku +1
            $loan->book->increment('stock');

            return redirect()->back()->with('success', 'Buku berhasil dikembalikan! Terima kasih.');
        }

        return redirect()->back()->with('error', 'Gagal mengembalikan buku atau buku sudah dikembalikan.');
    }

}

