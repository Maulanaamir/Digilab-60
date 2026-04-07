<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // ini buat dashboard 
        $currentlyBorrowed = Loan::where('status', 'borrowed')->count();
        $returnedBooks = Loan::where('status', 'returned')->count();
        $totalBooks = Book::sum('stock');

        $recentActivities = Loan::with(['user', 'book'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'currentlyBorrowed',
            'returnedBooks',
            'totalBooks',
            'recentActivities'
        ));
    }
}