<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');  // desactivar las llaves foraneas

        // limpiar la base de datos
        User::truncate();
        Product::truncate();
        Category::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();

        $usersQuantity = 500;
        $categoriesQuantity = 30;
        $productsQuantity = 1000;
        $transactionsQuantity = 1000;

        User::factory()->count( $usersQuantity )->create();
        $listCategories = Category::factory()->count( $categoriesQuantity )->create();

        Product::factory()->count( $productsQuantity )
            ->create()
            ->each(
                function ( $product ) {
                    $categories = Category::all()->random( mt_rand(1, 5) )->pluck( 'id' );

                    $product->categories()->attach( $categories->first() );
                }
            );
        //Product::factory()->count( $productsQuantity )->hasAttached( $listCategories )->create(); // TODO: corregir esto

        Transaction::factory()->count( $transactionsQuantity )->create();
    }
}
