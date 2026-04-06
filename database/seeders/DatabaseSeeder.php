<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@perpus.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'user',
            'email' => 'siswa@perpus.com',
            'password' => bcrypt('password'), 
            'role' => 'siswa',
        ]);


        $kategoriFiksi = Category::create(['name' => 'Novel & Fiksi']);
        $kategoriPelajaran = Category::create(['name' => 'Buku Pelajaran']);

        Book::create([
            'title' => 'Mastering Laravel 12',
            'author' => 'Ahmad Maulana',
            'published_year' => 2026,
            'stock' => 10,
            'category_id' => $kategoriPelajaran->id,
        ]);

        Book::create([
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'published_year' => 2005,
            'stock' => 5,
            'category_id' => $kategoriFiksi->id, 
        ]);
    }
}
