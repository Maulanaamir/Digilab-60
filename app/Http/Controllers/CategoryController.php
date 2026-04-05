<?php
namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return view('categories.index', ['categories' => Category::all(), 'categoryToEdit' => null]);
    }

    public function store(Request $request) {
        Category::create($request->all());
        return back(); // Tendang balik ke halaman tadi
    }

    public function edit(Category $category) {
        return view('categories.index', ['categories' => Category::all(), 'categoryToEdit' => $category]);
    }

    public function update(Request $request, Category $category) {
        $category->update($request->all());
        return redirect()->route('categories.index');
    }

    public function destroy(Category $category) {
        $category->delete();
        return back();
    }
}