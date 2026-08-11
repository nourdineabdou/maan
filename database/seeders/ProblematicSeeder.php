<?php

namespace Database\Seeders;

use App\Models\Problematic;
use Illuminate\Database\Seeder;

class ProblematicSeeder extends Seeder
{
    /**
     * Les 16 problématiques (cahier des charges, section 13).
     * "Autres" impose une description obligatoire (validé au niveau du
     * formulaire d'adhésion, pas ici).
     */
    private const PROBLEMATICS = [
        ['code' => 'education', 'fr' => 'Éducation', 'ar' => 'التعليم', 'icon' => 'bi-book'],
        ['code' => 'health', 'fr' => 'Santé', 'ar' => 'الصحة', 'icon' => 'bi-heart-pulse'],
        ['code' => 'employment', 'fr' => 'Emploi', 'ar' => 'التشغيل', 'icon' => 'bi-briefcase'],
        ['code' => 'economy', 'fr' => 'Économie', 'ar' => 'الاقتصاد', 'icon' => 'bi-graph-up'],
        ['code' => 'agriculture', 'fr' => 'Agriculture', 'ar' => 'الزراعة', 'icon' => 'bi-flower1'],
        ['code' => 'fishing', 'fr' => 'Pêche', 'ar' => 'الصيد', 'icon' => 'bi-water'],
        ['code' => 'livestock', 'fr' => 'Élevage', 'ar' => 'تربية المواشي', 'icon' => 'bi-tree'],
        ['code' => 'infrastructure', 'fr' => 'Infrastructures', 'ar' => 'البنى التحتية', 'icon' => 'bi-building'],
        ['code' => 'environment', 'fr' => 'Environnement', 'ar' => 'البيئة', 'icon' => 'bi-globe'],
        ['code' => 'justice', 'fr' => 'Justice', 'ar' => 'العدالة', 'icon' => 'bi-bank'],
        ['code' => 'security', 'fr' => 'Sécurité', 'ar' => 'الأمن', 'icon' => 'bi-shield-check'],
        ['code' => 'governance', 'fr' => 'Gouvernance', 'ar' => 'الحكامة', 'icon' => 'bi-diagram-3'],
        ['code' => 'youth_sports', 'fr' => 'Jeunesse et sports', 'ar' => 'الشباب والرياضة', 'icon' => 'bi-trophy'],
        ['code' => 'culture', 'fr' => 'Culture', 'ar' => 'الثقافة', 'icon' => 'bi-palette'],
        ['code' => 'digital_innovation', 'fr' => 'Numérique et innovation', 'ar' => 'الرقمنة والابتكار', 'icon' => 'bi-cpu'],
        ['code' => 'other', 'fr' => 'Autres', 'ar' => 'أخرى', 'icon' => 'bi-three-dots'],
    ];

    public function run(): void
    {
        foreach (self::PROBLEMATICS as $order => $problematic) {
            Problematic::updateOrCreate(
                ['code' => $problematic['code']],
                [
                    'name' => ['fr' => $problematic['fr'], 'ar' => $problematic['ar']],
                    'description' => ['fr' => '', 'ar' => ''],
                    'icon' => $problematic['icon'],
                    'requires_justification' => $problematic['code'] === 'other',
                    'is_active' => true,
                    'display_order' => $order + 1,
                ]
            );
        }
    }
}
