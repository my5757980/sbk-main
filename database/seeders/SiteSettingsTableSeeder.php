<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SiteSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Reset the site_settings table
         */
        if (\App::environment() == 'local') {
            \DB::table('site_settings')->truncate();
        }

        \DB::table('site_settings')->insert([
            'site_title'      => 'Website',
            'contact_email'   => 'contact@domain.com',
            'contact_phone'   => '',
            'address'         => '',
            'logo'            => '',
            'facebook'        => '',
            'twitter'         => '',
            'pinterest'       => '',
            'footer_scripts'  => '',
            'footer_sentence' => '',
            'copyright'       => '',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ]);
    }
}
