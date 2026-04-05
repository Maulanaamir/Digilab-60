<?php
namespace App\Http\Controllers;
use App\Models\Book;
use App\Models\Loan;

class DashboardController extends Controller
{
    public function index() {
        return view('dashboard', [
            'totalBooks' => Book::sum('stock'),
            'currentlyBorrowed' => Loan::where('status', 'borrowed')->count(),
            'returnedBooks' => Loan::where('status', 'returned')->count(),
            'recentActivities' => Loan::latest()->take(5)->get()
        ]);
    }
}