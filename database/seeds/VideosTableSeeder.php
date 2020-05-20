<?php

use Illuminate\Database\Seeder;

class VideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('videos')->insert(
            [
                ['name' =>  '1', 'id' => 1, 'num_frame' => 5, 'max_id' => 0 ],
                ['name' =>  '2', 'id' => 2, 'num_frame' => 5, 'max_id' => 0 ],
            ]
        );
    }
}
