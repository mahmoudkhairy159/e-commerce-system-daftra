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
                'name_en' => 'Electronics',
                'name_ar' => 'الإلكترونيات',
                'description_en' => 'Category for electronic devices',
                'description_ar' => 'فئة للأجهزة الإلكترونية',
            ],
            [
                'code' => 'C2',
                'name_en' => 'Fashion',
                'name_ar' => 'الأزياء',
                'description_en' => 'Category for fashion items',
                'description_ar' => 'فئة لعناصر الأزياء',
            ],
            [
                'code' => 'C7',
                'name_en' => 'Home & Kitchen',
                'name_ar' => 'المنزل والمطبخ',
                'description_en' => 'Everything for your home and kitchen needs',
                'description_ar' => 'كل ما تحتاجه لمنزلك ومطبخك',
            ],
            [
                'code' => 'C8',
                'name_en' => 'Beauty & Personal Care',
                'name_ar' => 'الجمال والعناية الشخصية',
                'description_en' => 'Beauty products and personal care items',
                'description_ar' => 'منتجات التجميل والعناية الشخصية',
            ],
            [
                'code' => 'C9',
                'name_en' => 'Sports & Outdoors',
                'name_ar' => 'الرياضة والأنشطة الخارجية',
                'description_en' => 'Sporting goods and outdoor equipment',
                'description_ar' => 'المعدات الرياضية ومعدات الأنشطة الخارجية',
            ],
            [
                'code' => 'C10',
                'name_en' => 'Toys & Games',
                'name_ar' => 'الألعاب',
                'description_en' => 'Toys, games, and entertainment for all ages',
                'description_ar' => 'الألعاب والتسلية لجميع الأعمار',
            ],
            [
                'code' => 'C11',
                'name_en' => 'Books & Stationery',
                'name_ar' => 'الكتب والقرطاسية',
                'description_en' => 'Books, stationery, and office supplies',
                'description_ar' => 'الكتب والقرطاسية ومستلزمات المكتب',
            ],
            [
                'code' => 'C12',
                'name_en' => 'Automotive',
                'name_ar' => 'السيارات',
                'description_en' => 'Auto parts, tools, and accessories',
                'description_ar' => 'قطع غيار السيارات والأدوات والإكسسوارات',
            ],
            [
                'code' => 'C13',
                'name_en' => 'Health & Wellness',
                'name_ar' => 'الصحة واللياقة',
                'description_en' => 'Health supplements and wellness products',
                'description_ar' => 'المكملات الصحية ومنتجات العافية',
            ],
            [
                'code' => 'C14',
                'name_en' => 'Grocery & Gourmet',
                'name_ar' => 'البقالة والمأكولات الفاخرة',
                'description_en' => 'Grocery items and gourmet food products',
                'description_ar' => 'منتجات البقالة والطعام الفاخر',
            ],
            [
                'code' => 'C55',
                'name_en' => 'Pet Supplies',
                'name_ar' => 'مستلزمات الحيوانات الأليفة',
                'description_en' => 'Food, toys, and accessories for pets',
                'description_ar' => 'طعام وألعاب وإكسسوارات للحيوانات الأليفة',
            ],
            [
                'code' => 'C56',
                'name_en' => 'Industrial & Scientific',
                'name_ar' => 'الصناعية والعلمية',
                'description_en' => 'Industrial equipment and scientific instruments',
                'description_ar' => 'المعدات الصناعية والأدوات العلمية',
            ],
        ];

        $mainCategoryIds = $this->createCategories($mainCategories);

        $subCategories = [
            // Electronics Subcategories
            [
                'parent_code' => 'C1',
                'code' => 'C3',
                'name_en' => 'Mobile Phones & Accessories',
                'name_ar' => 'الهواتف المحمولة والإكسسوارات',
                'description_en' => 'Smartphones, feature phones, and accessories',
                'description_ar' => 'الهواتف الذكية والهواتف العادية والإكسسوارات',
            ],
            [
                'parent_code' => 'C1',
                'code' => 'C4',
                'name_en' => 'Laptops & Computers',
                'name_ar' => 'أجهزة الكمبيوتر المحمولة والكمبيوتر',
                'description_en' => 'Laptops, desktops, and computer components',
                'description_ar' => 'أجهزة الكمبيوتر المحمولة وأجهزة سطح المكتب ومكونات الكمبيوتر',
            ],
            [
                'parent_code' => 'C1',
                'code' => 'C15',
                'name_en' => 'Audio & Headphones',
                'name_ar' => 'الصوتيات وسماعات الرأس',
                'description_en' => 'Speakers, headphones, and audio equipment',
                'description_ar' => 'مكبرات الصوت وسماعات الرأس والمعدات الصوتية',
            ],
            [
                'parent_code' => 'C1',
                'code' => 'C16',
                'name_en' => 'Cameras & Photography',
                'name_ar' => 'الكاميرات والتصوير',
                'description_en' => 'Digital cameras, lenses, and photography equipment',
                'description_ar' => 'الكاميرات الرقمية والعدسات ومعدات التصوير',
            ],
            [
                'parent_code' => 'C1',
                'code' => 'C17',
                'name_en' => 'TV & Home Entertainment',
                'name_ar' => 'التلفزيون والترفيه المنزلي',
                'description_en' => 'Televisions, streaming devices, and home theater systems',
                'description_ar' => 'أجهزة التلفزيون وأجهزة البث وأنظمة المسرح المنزلي',
            ],
            [
                'parent_code' => 'C1',
                'code' => 'C18',
                'name_en' => 'Wearable Technology',
                'name_ar' => 'التكنولوجيا القابلة للارتداء',
                'description_en' => 'Smartwatches, fitness trackers, and wearable gadgets',
                'description_ar' => 'الساعات الذكية وأجهزة تتبع اللياقة البدنية والأجهزة القابلة للارتداء',
            ],

            // Fashion Subcategories
            [
                'parent_code' => 'C2',
                'code' => 'C5',
                'name_en' => 'Men\'s Fashion',
                'name_ar' => 'أزياء الرجال',
                'description_en' => 'Clothing, shoes, and accessories for men',
                'description_ar' => 'الملابس والأحذية والإكسسوارات للرجال',
            ],
            [
                'parent_code' => 'C2',
                'code' => 'C6',
                'name_en' => 'Women\'s Fashion',
                'name_ar' => 'أزياء النساء',
                'description_en' => 'Clothing, shoes, and accessories for women',
                'description_ar' => 'الملابس والأحذية والإكسسوارات للنساء',
            ],
            [
                'parent_code' => 'C2',
                'code' => 'C19',
                'name_en' => 'Kids\' Fashion',
                'name_ar' => 'أزياء الأطفال',
                'description_en' => 'Clothing, shoes, and accessories for children',
                'description_ar' => 'الملابس والأحذية والإكسسوارات للأطفال',
            ],
            [
                'parent_code' => 'C2',
                'code' => 'C20',
                'name_en' => 'Jewelry & Watches',
                'name_ar' => 'المجوهرات والساعات',
                'description_en' => 'Fine jewelry, fashion jewelry, and watches',
                'description_ar' => 'المجوهرات الراقية والمجوهرات العصرية والساعات',
            ],
            [
                'parent_code' => 'C2',
                'code' => 'C21',
                'name_en' => 'Bags & Luggage',
                'name_ar' => 'الحقائب والأمتعة',
                'description_en' => 'Handbags, backpacks, luggage, and travel accessories',
                'description_ar' => 'حقائب اليد وحقائب الظهر والأمتعة وإكسسوارات السفر',
            ],

            // Home & Kitchen Subcategories
            [
                'parent_code' => 'C7',
                'code' => 'C22',
                'name_en' => 'Kitchen & Dining',
                'name_ar' => 'المطبخ وأدوات المائدة',
                'description_en' => 'Cookware, utensils, appliances, and dining essentials',
                'description_ar' => 'أواني الطهي والأدوات والأجهزة ومستلزمات تناول الطعام',
            ],
            [
                'parent_code' => 'C7',
                'code' => 'C23',
                'name_en' => 'Furniture',
                'name_ar' => 'الأثاث',
                'description_en' => 'Furniture for living room, bedroom, office, and more',
                'description_ar' => 'أثاث لغرفة المعيشة وغرفة النوم والمكتب والمزيد',
            ],
            [
                'parent_code' => 'C7',
                'code' => 'C24',
                'name_en' => 'Home Decor',
                'name_ar' => 'ديكور المنزل',
                'description_en' => 'Decorative items, wall art, and home accents',
                'description_ar' => 'العناصر الزخرفية وفن الجدار ولمسات المنزل',
            ],
            [
                'parent_code' => 'C7',
                'code' => 'C25',
                'name_en' => 'Bedding & Bath',
                'name_ar' => 'مستلزمات النوم والحمام',
                'description_en' => 'Bed sheets, towels, and bathroom accessories',
                'description_ar' => 'ملاءات السرير والمناشف وإكسسوارات الحمام',
            ],
            [
                'parent_code' => 'C7',
                'code' => 'C26',
                'name_en' => 'Home Appliances',
                'name_ar' => 'الأجهزة المنزلية',
                'description_en' => 'Major and small household appliances',
                'description_ar' => 'الأجهزة المنزلية الكبيرة والصغيرة',
            ],

            // Beauty & Personal Care Subcategories
            [
                'parent_code' => 'C8',
                'code' => 'C27',
                'name_en' => 'Skincare',
                'name_ar' => 'العناية بالبشرة',
                'description_en' => 'Facial care, body care, and skincare sets',
                'description_ar' => 'العناية بالوجه والعناية بالجسم ومجموعات العناية بالبشرة',
            ],
            [
                'parent_code' => 'C8',
                'code' => 'C28',
                'name_en' => 'Makeup',
                'name_ar' => 'المكياج',
                'description_en' => 'Foundations, lipsticks, eye makeup, and more',
                'description_ar' => 'كريمات الأساس وأحمر الشفاه ومكياج العيون والمزيد',
            ],
            [
                'parent_code' => 'C8',
                'code' => 'C29',
                'name_en' => 'Haircare',
                'name_ar' => 'العناية بالشعر',
                'description_en' => 'Shampoos, conditioners, and styling products',
                'description_ar' => 'الشامبو والبلسم ومنتجات تصفيف الشعر',
            ],
            [
                'parent_code' => 'C8',
                'code' => 'C30',
                'name_en' => 'Fragrances',
                'name_ar' => 'العطور',
                'description_en' => 'Perfumes, colognes, and body sprays',
                'description_ar' => 'العطور والكولونيا وبخاخات الجسم',
            ],

            // Sports & Outdoors Subcategories
            [
                'parent_code' => 'C9',
                'code' => 'C31',
                'name_en' => 'Exercise & Fitness',
                'name_ar' => 'التمارين واللياقة البدنية',
                'description_en' => 'Fitness equipment, workout gear, and accessories',
                'description_ar' => 'معدات اللياقة البدنية ومستلزمات التمارين والإكسسوارات',
            ],
            [
                'parent_code' => 'C9',
                'code' => 'C32',
                'name_en' => 'Team Sports',
                'name_ar' => 'الرياضات الجماعية',
                'description_en' => 'Equipment and gear for football, basketball, soccer, and more',
                'description_ar' => 'المعدات والتجهيزات لكرة القدم وكرة السلة وكرة القدم والمزيد',
            ],
            [
                'parent_code' => 'C9',
                'code' => 'C33',
                'name_en' => 'Outdoor Recreation',
                'name_ar' => 'الترفيه الخارجي',
                'description_en' => 'Camping, hiking, and outdoor adventure gear',
                'description_ar' => 'معدات التخييم والمشي لمسافات طويلة ومعدات المغامرات الخارجية',
            ],
            [
                'parent_code' => 'C9',
                'code' => 'C34',
                'name_en' => 'Cycling',
                'name_ar' => 'ركوب الدراجات',
                'description_en' => 'Bicycles, parts, accessories, and cycling clothing',
                'description_ar' => 'الدراجات وقطع الغيار والإكسسوارات وملابس ركوب الدراجات',
            ],

            // Toys & Games Subcategories
            [
                'parent_code' => 'C10',
                'code' => 'C35',
                'name_en' => 'Board Games & Puzzles',
                'name_ar' => 'ألعاب الطاولة والألغاز',
                'description_en' => 'Board games, card games, puzzles, and educational games',
                'description_ar' => 'ألعاب الطاولة وألعاب الورق والألغاز والألعاب التعليمية',
            ],
            [
                'parent_code' => 'C10',
                'code' => 'C36',
                'name_en' => 'Action Figures & Collectibles',
                'name_ar' => 'شخصيات الحركة والمقتنيات',
                'description_en' => 'Action figures, collectible toys, and memorabilia',
                'description_ar' => 'شخصيات الحركة والألعاب للتجميع والتذكارات',
            ],
            [
                'parent_code' => 'C10',
                'code' => 'C37',
                'name_en' => 'Dolls & Accessories',
                'name_ar' => 'الدمى والإكسسوارات',
                'description_en' => 'Fashion dolls, playsets, and doll accessories',
                'description_ar' => 'دمى الأزياء ومجموعات اللعب وإكسسوارات الدمى',
            ],
            [
                'parent_code' => 'C10',
                'code' => 'C38',
                'name_en' => 'Video Games',
                'name_ar' => 'ألعاب الفيديو',
                'description_en' => 'Gaming consoles, games, and gaming accessories',
                'description_ar' => 'وحدات التحكم في الألعاب والألعاب وإكسسوارات الألعاب',
            ],

            // Books & Stationery Subcategories
            [
                'parent_code' => 'C11',
                'code' => 'C39',
                'name_en' => 'Fiction & Literature',
                'name_ar' => 'الروايات والأدب',
                'description_en' => 'Novels, short stories, and literary works',
                'description_ar' => 'الروايات والقصص القصيرة والأعمال الأدبية',
            ],
            [
                'parent_code' => 'C11',
                'code' => 'C40',
                'name_en' => 'Non-fiction',
                'name_ar' => 'كتب غير خيالية',
                'description_en' => 'Biography, self-help, history, and educational books',
                'description_ar' => 'السيرة الذاتية والمساعدة الذاتية والتاريخ والكتب التعليمية',
            ],
            [
                'parent_code' => 'C11',
                'code' => 'C41',
                'name_en' => 'Office Supplies',
                'name_ar' => 'لوازم المكتب',
                'description_en' => 'Pens, notebooks, and office organization',
                'description_ar' => 'الأقلام والدفاتر وتنظيم المكتب',
            ],
            [
                'parent_code' => 'C11',
                'code' => 'C42',
                'name_en' => 'Art Supplies',
                'name_ar' => 'لوازم الفنون',
                'description_en' => 'Drawing, painting, and craft supplies',
                'description_ar' => 'لوازم الرسم والتلوين والحرف اليدوية',
            ],

            // Automotive Subcategories
            [
                'parent_code' => 'C12',
                'code' => 'C43',
                'name_en' => 'Car Parts & Accessories',
                'name_ar' => 'قطع غيار السيارات والإكسسوارات',
                'description_en' => 'Replacement parts, interior accessories, and exterior accessories',
                'description_ar' => 'قطع الغيار وإكسسوارات داخلية وإكسسوارات خارجية',
            ],
            [
                'parent_code' => 'C12',
                'code' => 'C44',
                'name_en' => 'Tools & Equipment',
                'name_ar' => 'الأدوات والمعدات',
                'description_en' => 'Automotive tools, diagnostic equipment, and garage accessories',
                'description_ar' => 'أدوات السيارات ومعدات التشخيص وإكسسوارات الجراج',
            ],
            [
                'parent_code' => 'C12',
                'code' => 'C45',
                'name_en' => 'Car Care',
                'name_ar' => 'العناية بالسيارة',
                'description_en' => 'Cleaning, polishing, and maintenance products',
                'description_ar' => 'منتجات التنظيف والتلميع والصيانة',
            ],
            [
                'parent_code' => 'C12',
                'code' => 'C46',
                'name_en' => 'Motorcycle Parts & Accessories',
                'name_ar' => 'قطع غيار الدراجات النارية والإكسسوارات',
                'description_en' => 'Parts, gear, and accessories for motorcycles',
                'description_ar' => 'قطع الغيار والمعدات والإكسسوارات للدراجات النارية',
            ],

            // Health & Wellness Subcategories
            [
                'parent_code' => 'C13',
                'code' => 'C47',
                'name_en' => 'Vitamins & Supplements',
                'name_ar' => 'الفيتامينات والمكملات',
                'description_en' => 'Nutritional supplements, vitamins, and minerals',
                'description_ar' => 'المكملات الغذائية والفيتامينات والمعادن',
            ],
            [
                'parent_code' => 'C13',
                'code' => 'C48',
                'name_en' => 'Medical Supplies',
                'name_ar' => 'المستلزمات الطبية',
                'description_en' => 'First aid, health monitors, and medical devices',
                'description_ar' => 'الإسعافات الأولية وأجهزة مراقبة الصحة والأجهزة الطبية',
            ],
            [
                'parent_code' => 'C13',
                'code' => 'C49',
                'name_en' => 'Personal Care',
                'name_ar' => 'العناية الشخصية',
                'description_en' => 'Bath and body, oral care, and feminine care',
                'description_ar' => 'الاستحمام والجسم والعناية بالفم والعناية النسائية',
            ],
            [
                'parent_code' => 'C13',
                'code' => 'C50',
                'name_en' => 'Natural & Alternative Medicine',
                'name_ar' => 'الطب الطبيعي والبديل',
                'description_en' => 'Herbal remedies, essential oils, and alternative health products',
                'description_ar' => 'العلاجات العشبية والزيوت الأساسية ومنتجات الصحة البديلة',
            ],

            // Grocery & Gourmet Subcategories
            [
                'parent_code' => 'C14',
                'code' => 'C51',
                'name_en' => 'Beverages',
                'name_ar' => 'المشروبات',
                'description_en' => 'Coffee, tea, juices, and specialty drinks',
                'description_ar' => 'القهوة والشاي والعصائر والمشروبات المتخصصة',
            ],
            [
                'parent_code' => 'C14',
                'code' => 'C52',
                'name_en' => 'Snacks & Sweets',
                'name_ar' => 'الوجبات الخفيفة والحلويات',
                'description_en' => 'Chips, cookies, candies, and gourmet snacks',
                'description_ar' => 'رقائق البطاطس والكعك والحلوى والوجبات الخفيفة الفاخرة',
            ],
            [
                'parent_code' => 'C14',
                'code' => 'C53',
                'name_en' => 'Cooking & Baking',
                'name_ar' => 'الطبخ والخبز',
                'description_en' => 'Spices, baking ingredients, and cooking essentials',
                'description_ar' => 'التوابل ومكونات الخبز وأساسيات الطبخ',
            ],
            [
                'parent_code' => 'C14',
                'code' => 'C54',
                'name_en' => 'Organic & Natural Foods',
                'name_ar' => 'الأغذية العضوية والطبيعية',
                'description_en' => 'Organic groceries, natural food products, and specialty diets',
                'description_ar' => 'البقالة العضوية والمنتجات الغذائية الطبيعية والأنظمة الغذائية الخاصة',
            ],
        ];

        $this->createSubcategories($subCategories, $mainCategoryIds);
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

    /**
     * Create subcategories based on parent categories.
     */
    private function createSubcategories(array $categories, array $mainCategoryIds): void
    {
        foreach ($categories as $category) {
            $parentId = $mainCategoryIds[$category['parent_code']] ?? null;

            if ($parentId) {
                Category::updateOrCreate(
                    ['code' => $category['code']],
                    [
                        'parent_id' => $parentId,
                        'code' => $category['code'],
                        'en' => [
                            'name' => $category['name_en'],
                            'description' => $category['description_en'],
                        ],
                        'ar' => [
                            'name' => $category['name_ar'],
                            'description' => $category['description_ar'],
                        ],
                    ]
                );
            }
        }
    }
}
