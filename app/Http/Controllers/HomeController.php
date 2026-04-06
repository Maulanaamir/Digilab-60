<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('dashboard');
        }

        $categories = Category::orderBy('name', 'ASC')->get(); 
        
        $query = Book::query(); 



        $books = $query->latest()->take(24)->get();

        return view('welcome', compact('categories', 'books'));
    }


}