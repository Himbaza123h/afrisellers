<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // African Countries
            [
                'name' => 'Rwanda',
                'flag_url' => 'https://flagcdn.com/w320/rw.png',
                'status' => 'active',
            ],
            [
                'name' => 'Kenya',
                'flag_url' => 'https://flagcdn.com/w320/ke.png',
                'status' => 'active',
            ],
            [
                'name' => 'Tanzania',
                'flag_url' => 'https://flagcdn.com/w320/tz.png',
                'status' => 'active',
            ],
            [
                'name' => 'Uganda',
                'flag_url' => 'https://flagcdn.com/w320/ug.png',
                'status' => 'active',
            ],
            [
                'name' => 'Ethiopia',
                'flag_url' => 'https://flagcdn.com/w320/et.png',
                'status' => 'active',
            ],
            [
                'name' => 'Ghana',
                'flag_url' => 'https://flagcdn.com/w320/gh.png',
                'status' => 'active',
            ],
            [
                'name' => 'Nigeria',
                'flag_url' => 'https://flagcdn.com/w320/ng.png',
                'status' => 'active',
            ],
            [
                'name' => 'South Africa',
                'flag_url' => 'https://flagcdn.com/w320/za.png',
                'status' => 'active',
            ],
            [
                'name' => 'Egypt',
                'flag_url' => 'https://flagcdn.com/w320/eg.png',
                'status' => 'active',
            ],
            [
                'name' => 'Morocco',
                'flag_url' => 'https://flagcdn.com/w320/ma.png',
                'status' => 'active',
            ],
            [
                'name' => 'Senegal',
                'flag_url' => 'https://flagcdn.com/w320/sn.png',
                'status' => 'active',
            ],
            [
                'name' => 'Ivory Coast',
                'flag_url' => 'https://flagcdn.com/w320/ci.png',
                'status' => 'active',
            ],
            [
                'name' => 'Cameroon',
                'flag_url' => 'https://flagcdn.com/w320/cm.png',
                'status' => 'active',
            ],
            [
                'name' => 'Tunisia',
                'flag_url' => 'https://flagcdn.com/w320/tn.png',
                'status' => 'active',
            ],
            [
                'name' => 'Algeria',
                'flag_url' => 'https://flagcdn.com/w320/dz.png',
                'status' => 'active',
            ],
            [
                'name' => 'Zimbabwe',
                'flag_url' => 'https://flagcdn.com/w320/zw.png',
                'status' => 'active',
            ],
            [
                'name' => 'Zambia',
                'flag_url' => 'https://flagcdn.com/w320/zm.png',
                'status' => 'active',
            ],
            [
                'name' => 'Mozambique',
                'flag_url' => 'https://flagcdn.com/w320/mz.png',
                'status' => 'active',
            ],
            [
                'name' => 'Angola',
                'flag_url' => 'https://flagcdn.com/w320/ao.png',
                'status' => 'active',
            ],
            [
                'name' => 'Sudan',
                'flag_url' => 'https://flagcdn.com/w320/sd.png',
                'status' => 'active',
            ],
            [
                'name' => 'Madagascar',
                'flag_url' => 'https://flagcdn.com/w320/mg.png',
                'status' => 'active',
            ],
            [
                'name' => 'Mali',
                'flag_url' => 'https://flagcdn.com/w320/ml.png',
                'status' => 'active',
            ],
            [
                'name' => 'Burkina Faso',
                'flag_url' => 'https://flagcdn.com/w320/bf.png',
                'status' => 'active',
            ],
            [
                'name' => 'Niger',
                'flag_url' => 'https://flagcdn.com/w320/ne.png',
                'status' => 'active',
            ],
            [
                'name' => 'Malawi',
                'flag_url' => 'https://flagcdn.com/w320/mw.png',
                'status' => 'active',
            ],
            [
                'name' => 'Chad',
                'flag_url' => 'https://flagcdn.com/w320/td.png',
                'status' => 'active',
            ],
            [
                'name' => 'Somalia',
                'flag_url' => 'https://flagcdn.com/w320/so.png',
                'status' => 'active',
            ],
            [
                'name' => 'Guinea',
                'flag_url' => 'https://flagcdn.com/w320/gn.png',
                'status' => 'active',
            ],
            [
                'name' => 'Benin',
                'flag_url' => 'https://flagcdn.com/w320/bj.png',
                'status' => 'active',
            ],
            [
                'name' => 'Burundi',
                'flag_url' => 'https://flagcdn.com/w320/bi.png',
                'status' => 'active',
            ],
            [
                'name' => 'Togo',
                'flag_url' => 'https://flagcdn.com/w320/tg.png',
                'status' => 'active',
            ],
            [
                'name' => 'Sierra Leone',
                'flag_url' => 'https://flagcdn.com/w320/sl.png',
                'status' => 'active',
            ],
            [
                'name' => 'Libya',
                'flag_url' => 'https://flagcdn.com/w320/ly.png',
                'status' => 'active',
            ],
            [
                'name' => 'Congo',
                'flag_url' => 'https://flagcdn.com/w320/cg.png',
                'status' => 'active',
            ],
            [
                'name' => 'Central African Republic',
                'flag_url' => 'https://flagcdn.com/w320/cf.png',
                'status' => 'active',
            ],
            [
                'name' => 'Liberia',
                'flag_url' => 'https://flagcdn.com/w320/lr.png',
                'status' => 'active',
            ],
            [
                'name' => 'Mauritania',
                'flag_url' => 'https://flagcdn.com/w320/mr.png',
                'status' => 'active',
            ],
            [
                'name' => 'Eritrea',
                'flag_url' => 'https://flagcdn.com/w320/er.png',
                'status' => 'active',
            ],
            [
                'name' => 'Gambia',
                'flag_url' => 'https://flagcdn.com/w320/gm.png',
                'status' => 'active',
            ],
            [
                'name' => 'Botswana',
                'flag_url' => 'https://flagcdn.com/w320/bw.png',
                'status' => 'active',
            ],
            [
                'name' => 'Namibia',
                'flag_url' => 'https://flagcdn.com/w320/na.png',
                'status' => 'active',
            ],
            [
                'name' => 'Gabon',
                'flag_url' => 'https://flagcdn.com/w320/ga.png',
                'status' => 'active',
            ],
            [
                'name' => 'Lesotho',
                'flag_url' => 'https://flagcdn.com/w320/ls.png',
                'status' => 'active',
            ],
            [
                'name' => 'Guinea-Bissau',
                'flag_url' => 'https://flagcdn.com/w320/gw.png',
                'status' => 'active',
            ],
            [
                'name' => 'Equatorial Guinea',
                'flag_url' => 'https://flagcdn.com/w320/gq.png',
                'status' => 'active',
            ],
            [
                'name' => 'Mauritius',
                'flag_url' => 'https://flagcdn.com/w320/mu.png',
                'status' => 'active',
            ],
            [
                'name' => 'Eswatini',
                'flag_url' => 'https://flagcdn.com/w320/sz.png',
                'status' => 'active',
            ],
            [
                'name' => 'Djibouti',
                'flag_url' => 'https://flagcdn.com/w320/dj.png',
                'status' => 'active',
            ],
            [
                'name' => 'Comoros',
                'flag_url' => 'https://flagcdn.com/w320/km.png',
                'status' => 'active',
            ],
            [
                'name' => 'Cabo Verde',
                'flag_url' => 'https://flagcdn.com/w320/cv.png',
                'status' => 'active',
            ],
            [
                'name' => 'Sao Tome and Principe',
                'flag_url' => 'https://flagcdn.com/w320/st.png',
                'status' => 'active',
            ],
            [
                'name' => 'Seychelles',
                'flag_url' => 'https://flagcdn.com/w320/sc.png',
                'status' => 'active',
            ],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(
                ['name' => $country['name']],
                $country
            );
        }

        $this->command->info('Countries seeded successfully!');
    }
}

