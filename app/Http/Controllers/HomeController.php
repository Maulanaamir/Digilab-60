<?php
namespace App\Http\Controllers;
use App\Models\Book;
use App\Models\Category;

class HomeController extends Controller
{
    public function index() {
        // Tampilkan semua buku saja, lupakan fitur search bar
        return view('welcome', [
            'categories' => Category::all(),
            'books' => Book::latest()->get()
        ]);
    }

    public function show($id) {
        return view('book-detail', ['book' => Book::find($id)]);
    }
}