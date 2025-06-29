<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainCategories = [

            [
                'code' => 'C1',
                'name_en' => 'T-shirts',
                'name_ar' => 'تي شيرت',
                'description_en' => 'Category for t-shirts and casual tops',
                'description_ar' => 'فئة للتي شيرت والملابس العلوية غير الرسمية',
            ],
            [
                'code' => 'C2',
                'name_en' => 'Polo',
                'name_ar' => 'بولو',
                'description_en' => 'Category for polo shirts and collared tops',
                'description_ar' => 'فئة لقمصان البولو والملابس العلوية ذات الياقات',
            ],
            [
                'code' => 'C3',
                'name_en' => 'Jeans',
                'name_ar' => 'جينز',
                'description_en' => 'Category for denim jeans and pants',
                'description_ar' => 'فئة للجينز والسراويل الدنيم',
            ],
            [
                'code' => 'C4',
                'name_en' => 'Shirts',
                'name_ar' => 'قمصان',
                'description_en' => 'Category for formal and casual shirts',
                'description_ar' => 'فئة للقمصان الرسمية وغير الرسمية',
            ],
        ];

        $categoryIds=$this->createCategories($mainCategories);
    }

    /**
     * Create main categories.
     */
    private function createCategories(array $categories): array
    {
        $categoryIds = [];

        foreach ($categories as $category) {
            $existing = Category::where('code', $category['code'])->first();

            if (!$existing) {
                $created = Category::create([
                    'code' => $category['code'],
                    'en' => [
                        'name' => $category['name_en'],
                        'description' => $category['description_en'],
                    ],
                    'ar' => [
                        'name' => $category['name_ar'],
                        'description' => $category['description_ar'],
                    ],
                ]);
                $categoryIds[$category['code']] = $created->id;
            } else {
                $categoryIds[$category['code']] = $existing->id;
            }
        }

        return $categoryIds;
    }

}