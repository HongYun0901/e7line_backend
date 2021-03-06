<?php

use App\Category;
use App\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate();
        $name = ['711','王品','全家','Apple','威秀','礁溪','電影票','漢來','其他','響食天堂'];


        foreach (range(1,10) as $id){
            \App\Product::create([
                'name' => $name[$id-1],
                'create_date'=>now(),
                'update_date'=>now(),
            ]);
        }
    }
}
