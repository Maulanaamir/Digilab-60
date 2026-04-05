<?php
namespace App\Http\Controllers;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Tampilkan Tabel Buku di Admin
    public function index() {
        return view('books.index', ['book' => Book::latest()->paginate(10)]);
    }

    // Tampilkan Form Tambah
    public function create() {
        return view('books.create', ['categories' => Category::all()]);
    }

    // Simpan Buku Baru
    public function store(Request $request) {
        Book::create($request->all());
        return redirect()->route('books.index');
    }

    // Tampilkan Form Edit
    public function edit(Book $book) {
        return view('books.edit', ['book' => $book, 'categories' => Category::all()]);
    }

    // Simpan Perubahan
    public function update(Request $request, Book $book) {
        $book->update($request->all());
        return redirect()->route('books.index');
    }

    // Hapus Buku
    public function destroy(Book $book) {
        $book->delete();
        return back();
    }
}