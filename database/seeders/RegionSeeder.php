<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Les 15 wilayas de la Mauritanie.
     */
    private const REGIONS = [
        ['code' => 'HCH', 'fr' => 'Hodh Ech Chargui', 'ar' => 'الحوض الشرقي'],
        ['code' => 'HGH', 'fr' => 'Hodh El Gharbi', 'ar' => 'الحوض الغربي'],
        ['code' => 'ASS', 'fr' => 'Assaba', 'ar' => 'العصابة'],
        ['code' => 'GOR', 'fr' => 'Gorgol', 'ar' => 'غورغل'],
        ['code' => 'BRA', 'fr' => 'Brakna', 'ar' => 'البراكنة'],
        ['code' => 'TRZ', 'fr' => 'Trarza', 'ar' => 'الترارزة'],
        ['code' => 'ADR', 'fr' => 'Adrar', 'ar' => 'آدرار'],
        ['code' => 'DNO', 'fr' => 'Dakhlet Nouadhibou', 'ar' => 'داخلة نواذيبو'],
        ['code' => 'TAG', 'fr' => 'Tagant', 'ar' => 'تكانت'],
        ['code' => 'GID', 'fr' => 'Guidimaka', 'ar' => 'كيديماغا'],
        ['code' => 'TZE', 'fr' => 'Tiris Zemmour', 'ar' => 'تيرس زمور'],
        ['code' => 'INC', 'fr' => 'Inchiri', 'ar' => 'إنشيري'],
        ['code' => 'NKN', 'fr' => 'Nouakchott Nord', 'ar' => 'نواكشوط الشمالية'],
        ['code' => 'NKO', 'fr' => 'Nouakchott Ouest', 'ar' => 'نواكشوط الغربية'],
        ['code' => 'NKS', 'fr' => 'Nouakchott Sud', 'ar' => 'نواكشوط الجنوبية'],
    ];

    public function run(): void
    {
        foreach (self::REGIONS as $order => $region) {
            Region::updateOrCreate(
                ['code' => $region['code']],
                [
                    'name' => ['fr' => $region['fr'], 'ar' => $region['ar']],
                    'display_order' => $order + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
