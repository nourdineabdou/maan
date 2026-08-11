<?php

namespace Database\Seeders;

use App\Models\Moughataa;
use App\Models\Region;
use Illuminate\Database\Seeder;

class MoughataaSeeder extends Seeder
{
    /**
     * Liste exhaustive des 63 moughataas (départements) de Mauritanie,
     * regroupées par wilaya, telle que recensée sur Wikipédia (« Departments
     * of Mauritania » / « Subdivisions de la Mauritanie », état 2023 — inclut
     * les 6 moughataas créées en 2021 : Adel Bagrou, Touil, Lexeiba, Male,
     * Tékane, Wompou). Les intitulés arabes sont une transcription de bonne
     * foi ; à faire relire via l'écran d'administration des régions si
     * besoin d'une orthographe officielle différente.
     */
    private const MOUGHATAAS = [
        'HCH' => [
            ['code' => 'NEM', 'fr' => 'Néma', 'ar' => 'نعمة'],
            ['code' => 'BAS', 'fr' => 'Bassiknou', 'ar' => 'باسكنو'],
            ['code' => 'DJI', 'fr' => 'Djigueni', 'ar' => 'جكني'],
            ['code' => 'AMO', 'fr' => 'Amourj', 'ar' => 'أمرج'],
            ['code' => 'OUA', 'fr' => 'Oualata', 'ar' => 'ولاتة'],
            ['code' => 'TIM', 'fr' => 'Timbédra', 'ar' => 'تمبدغة'],
            ['code' => 'ADB', 'fr' => 'Adel Bagrou', 'ar' => 'العدل بكرو'],
            ['code' => 'NBL', 'fr' => "N'Beiket Lehwach", 'ar' => 'انبيكت لحواش'],
        ],
        'HGH' => [
            ['code' => 'AIO', 'fr' => 'Aïoun', 'ar' => 'العيون'],
            ['code' => 'TAM', 'fr' => 'Tamchekett', 'ar' => 'تمشكط'],
            ['code' => 'TIN', 'fr' => 'Tintane', 'ar' => 'تنتان'],
            ['code' => 'KOB', 'fr' => 'Koubenni', 'ar' => 'كوبني'],
            ['code' => 'TOU', 'fr' => 'Touil', 'ar' => 'الطويل'],
        ],
        'ASS' => [
            ['code' => 'KIF', 'fr' => 'Kiffa', 'ar' => 'كيفة'],
            ['code' => 'BOU', 'fr' => 'Boumdeid', 'ar' => 'بومديد'],
            ['code' => 'GUE', 'fr' => 'Guerou', 'ar' => 'كرو'],
            ['code' => 'KAN', 'fr' => 'Kankossa', 'ar' => 'كنكوصة'],
            ['code' => 'BAR', 'fr' => 'Barkéol', 'ar' => 'باركيول'],
        ],
        'GOR' => [
            ['code' => 'KAE', 'fr' => 'Kaédi', 'ar' => 'كيهيدي'],
            ['code' => 'MAG', 'fr' => 'Maghama', 'ar' => 'مغامة'],
            ['code' => 'MON', 'fr' => 'Monguel', 'ar' => 'مونكل'],
            ['code' => 'MBO', 'fr' => "M'Bout", 'ar' => 'امبود'],
            ['code' => 'LEX', 'fr' => 'Lexeiba', 'ar' => 'لكصيبة'],
        ],
        'BRA' => [
            ['code' => 'ALE', 'fr' => 'Aleg', 'ar' => 'ألاك'],
            ['code' => 'BAB', 'fr' => 'Bababé', 'ar' => 'بابابي'],
            ['code' => 'BOG', 'fr' => 'Bogué', 'ar' => 'بوكي'],
            ['code' => 'MBA', 'fr' => "M'Bagne", 'ar' => 'امبان'],
            ['code' => 'MLH', 'fr' => 'Magta Lahjar', 'ar' => 'مقطع لحجار'],
            ['code' => 'MAL', 'fr' => 'Male', 'ar' => 'مالي'],
        ],
        'TRZ' => [
            ['code' => 'ROS', 'fr' => 'Rosso', 'ar' => 'روصو'],
            ['code' => 'BTL', 'fr' => 'Boutilimit', 'ar' => 'بوتلميت'],
            ['code' => 'MED', 'fr' => 'Méderdra', 'ar' => 'مدردرة'],
            ['code' => 'RKZ', 'fr' => "R'Kiz", 'ar' => 'اركيز'],
            ['code' => 'OUN', 'fr' => 'Ouad Naga', 'ar' => 'واد الناقة'],
            ['code' => 'KEU', 'fr' => 'Keur Macène', 'ar' => 'كير مسن'],
            ['code' => 'TEK', 'fr' => 'Tékane', 'ar' => 'تكان'],
        ],
        'ADR' => [
            ['code' => 'ATA', 'fr' => 'Atar', 'ar' => 'أطار'],
            ['code' => 'AOU', 'fr' => 'Aoujeft', 'ar' => 'أوجفت'],
            ['code' => 'CHI', 'fr' => 'Chinguetti', 'ar' => 'شنقيط'],
            ['code' => 'OUD', 'fr' => 'Ouadane', 'ar' => 'وادان'],
        ],
        'DNO' => [
            ['code' => 'NDB', 'fr' => 'Nouadhibou', 'ar' => 'نواذيبو'],
            ['code' => 'CHA', 'fr' => 'Chami', 'ar' => 'شامي'],
        ],
        'TAG' => [
            ['code' => 'TID', 'fr' => 'Tidjikja', 'ar' => 'تجكجة'],
            ['code' => 'MOU', 'fr' => 'Moudjéria', 'ar' => 'مجرية'],
            ['code' => 'TIC', 'fr' => 'Tichitt', 'ar' => 'تيشيت'],
        ],
        'GID' => [
            ['code' => 'SEL', 'fr' => 'Sélibaby', 'ar' => 'سيليبابي'],
            ['code' => 'OUL', 'fr' => 'Ould Yengé', 'ar' => 'ولد يانگه'],
            ['code' => 'GHA', 'fr' => 'Ghabou', 'ar' => 'غابو'],
            ['code' => 'WOM', 'fr' => 'Wompou', 'ar' => 'ومبو'],
        ],
        'TZE' => [
            ['code' => 'ZOU', 'fr' => 'Zouérate', 'ar' => 'ازويرات'],
            ['code' => 'BIR', 'fr' => 'Bir Moghrein', 'ar' => 'بير أم اكرين'],
            ['code' => 'FDE', 'fr' => "F'Déirick", 'ar' => 'الفديرك'],
        ],
        'INC' => [
            ['code' => 'AKJ', 'fr' => 'Akjoujt', 'ar' => 'أكجوجت'],
            ['code' => 'BEN', 'fr' => 'Bénichab', 'ar' => 'بنشاب'],
        ],
        'NKN' => [
            ['code' => 'TEY', 'fr' => 'Teyarett', 'ar' => 'تيارت'],
            ['code' => 'TOJ', 'fr' => 'Toujounine', 'ar' => 'توجونين'],
            ['code' => 'DAR', 'fr' => 'Dar Naïm', 'ar' => 'دار النعيم'],
        ],
        'NKO' => [
            ['code' => 'KSA', 'fr' => 'Ksar', 'ar' => 'لكصر'],
            ['code' => 'TEV', 'fr' => 'Tevragh Zeina', 'ar' => 'تفرغ زينة'],
            ['code' => 'SEB', 'fr' => 'Sebkha', 'ar' => 'السبخة'],
        ],
        'NKS' => [
            ['code' => 'MIN', 'fr' => 'El Mina', 'ar' => 'الميناء'],
            ['code' => 'RIY', 'fr' => 'Riyad', 'ar' => 'الرياض'],
            ['code' => 'ARA', 'fr' => 'Arafat', 'ar' => 'عرفات'],
        ],
    ];

    public function run(): void
    {
        foreach (self::MOUGHATAAS as $regionCode => $moughataas) {
            $region = Region::where('code', $regionCode)->first();

            if (! $region) {
                continue;
            }

            foreach ($moughataas as $order => $moughataa) {
                Moughataa::updateOrCreate(
                    ['region_id' => $region->id, 'code' => $moughataa['code']],
                    [
                        'name' => ['fr' => $moughataa['fr'], 'ar' => $moughataa['ar']],
                        'display_order' => $order + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
