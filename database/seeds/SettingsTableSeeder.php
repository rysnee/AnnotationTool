<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('settings')->insert(
            array(
                array('key' => 'timezone',          'value' => 'Asia/Ho_Chi_Minh'),
                array('key' => 'dataset_path',      'value' => base_path() . '/public/dataset'),
                array('key' => 'output_path',       'value' => base_path() . '/storage/app/public'),
            )
        );

    }
}
