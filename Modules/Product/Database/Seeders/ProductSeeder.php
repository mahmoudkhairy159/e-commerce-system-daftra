<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;
use Illuminate\Support\Str;
use Modules\Product\Enums\ProductTypeEnum;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supportedLocales = core()->getSupportedLanguagesKeys();

        $products = [
            // Electronics - Mobile Phones & Accessories
            [
                'code' => 'IPHONE15PRO',
                'name_en' => 'Apple iPhone 15 Pro Max 256GB',
                'name_ar' => 'أبل آيفون 15 برو ماكس 256 جيجابايت',
                'short_description_en' => 'The latest iPhone with ProMotion display, A17 Pro chip, and advanced camera system',
                'short_description_ar' => 'أحدث آيفون مع شاشة بروموشن، شريحة A17 برو، ونظام كاميرا متقدم',
                'long_description_en' => 'The iPhone 15 Pro Max features a durable titanium design, a 6.7-inch Super Retina XDR display with ProMotion technology, the powerful A17 Pro chip with 6-core CPU and 5-core GPU, a pro camera system with 48MP main camera, enhanced telephoto camera, and improved Night mode capabilities.',
                'long_description_ar' => 'يتميز آيفون 15 برو ماكس بتصميم تيتانيوم متين، وشاشة سوبر رتينا XDR مقاس 6.7 بوصة مع تقنية بروموشن، وشريحة A17 برو القوية مع وحدة معالجة مركزية سداسية النواة ووحدة معالجة رسومات خماسية النواة، ونظام كاميرا احترافي مع كاميرا رئيسية بدقة 48 ميجابكسل، وكاميرا مقربة محسنة، وقدرات محسنة في وضع الليل.',
                'seo_description_en' => 'Buy the Apple iPhone 15 Pro Max with 256GB storage, A17 Pro chip, advanced camera system, and impressive battery life',
                'seo_description_ar' => 'اشترِ أبل آيفون 15 برو ماكس بسعة تخزين 256 جيجابايت، شريحة A17 برو، نظام كاميرا متقدم، وعمر بطارية مثير للإعجاب',
                'seo_keys_en' => 'iPhone 15 Pro Max,Apple,smartphone,A17 Pro,titanium',
                'seo_keys_ar' => 'آيفون 15 برو ماكس,أبل,هاتف ذكي,A17 برو,تيتانيوم',
                'return_policy_en' => '14-day return policy. Product must be in original condition with all accessories and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 14 يومًا. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والعبوة.',
                'price' => 1099.99,
                'offer_price' => 1049.99,
                'categories' => [1, 3], // Electronics, Mobile Phones & Accessories
                'related_products' => ['SAMSUNGS24U', 'SONYWH1000'], // Samsung S24 Ultra, Sony Headphones
                'accessories' => ['SONYWH1000'], // Sony Headphones
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 50,
            ],

            [
                'code' => 'SAMSUNGS24U',
                'name_en' => 'Samsung Galaxy S24 Ultra 512GB',
                'name_ar' => 'سامسونج جالاكسي S24 ألترا 512 جيجابايت',
                'short_description_en' => 'Samsung\'s flagship smartphone with 120Hz display, Snapdragon processor, and 200MP camera',
                'short_description_ar' => 'هاتف سامسونج الرائد مع شاشة 120 هرتز، معالج سنابدراجون، وكاميرا بدقة 200 ميجابكسل',
                'long_description_en' => 'The Samsung Galaxy S24 Ultra features a 6.8-inch Dynamic AMOLED 2X display with 120Hz refresh rate, powered by the latest Snapdragon processor. It comes with a revolutionary 200MP main camera, enhanced low-light photography, and advanced AI features. The device includes an embedded S Pen, 5G connectivity, and a 5000mAh battery with fast charging capabilities.',
                'long_description_ar' => 'يتميز سامسونج جالاكسي S24 ألترا بشاشة ديناميكية AMOLED 2X مقاس 6.8 بوصة بمعدل تحديث 120 هرتز، مدعومة بأحدث معالج سنابدراجون. يأتي مع كاميرا رئيسية ثورية بدقة 200 ميجابكسل، وتصوير محسن في الإضاءة المنخفضة، وميزات ذكاء اصطناعي متقدمة. يتضمن الجهاز قلم S Pen مدمج، واتصال 5G، وبطارية بسعة 5000 مللي أمبير مع إمكانيات الشحن السريع.',
                'seo_description_en' => 'Experience the ultimate Samsung Galaxy S24 Ultra with 512GB storage, 200MP camera, and advanced AI features',
                'seo_description_ar' => 'جرب سامسونج جالاكسي S24 ألترا المميز بسعة تخزين 512 جيجابايت، كاميرا بدقة 200 ميجابكسل، وميزات ذكاء اصطناعي متقدمة',
                'seo_keys_en' => 'Galaxy S24 Ultra,Samsung,smartphone,Snapdragon,200MP camera',
                'seo_keys_ar' => 'جالاكسي S24 ألترا,سامسونج,هاتف ذكي,سنابدراجون,كاميرا 200 ميجابكسل',
                'return_policy_en' => '14-day return policy. Product must be in original condition with all accessories and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 14 يومًا. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والعبوة.',
                'price' => 1299.99,
                'offer_price' => 1199.99,
                'categories' => [1, 3], // Electronics, Mobile Phones & Accessories
                'related_products' => ['IPHONE15PRO', 'SONYWH1000'], // iPhone 15 Pro, Sony Headphones
                'accessories' => ['SONYWH1000'], // Sony Headphones
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 30,
            ],

            // Electronics - Laptops & Computers
            [
                'code' => 'MACBOOKM3',
                'name_en' => 'Apple MacBook Pro 16" M3 Pro Chip',
                'name_ar' => 'أبل ماك بوك برو 16 بوصة شريحة M3 برو',
                'short_description_en' => 'Powerful MacBook Pro with M3 Pro chip, 32GB RAM, and 1TB SSD',
                'short_description_ar' => 'ماك بوك برو قوي مع شريحة M3 برو، ذاكرة وصول عشوائي 32 جيجابايت، وقرص SSD سعة 1 تيرابايت',
                'long_description_en' => 'The new MacBook Pro 16" features the groundbreaking M3 Pro chip with 12-core CPU and 19-core GPU, delivering exceptional performance and efficiency. The stunning 16.2-inch Liquid Retina XDR display offers extreme dynamic range and contrast ratio. With 32GB of unified memory and 1TB SSD storage, this MacBook Pro handles demanding workloads with ease. It includes a 1080p FaceTime HD camera, six-speaker sound system, and up to 22 hours of battery life.',
                'long_description_ar' => 'يتميز ماك بوك برو 16 بوصة الجديد بشريحة M3 برو الثورية مع وحدة معالجة مركزية 12 نواة ووحدة معالجة رسومات 19 نواة، مما يوفر أداءً وكفاءة استثنائيين. توفر شاشة Liquid Retina XDR المذهلة مقاس 16.2 بوصة نطاقًا ديناميكيًا متطرفًا ونسبة تباين. مع ذاكرة موحدة سعة 32 جيجابايت وتخزين SSD سعة 1 تيرابايت، يتعامل ماك بوك برو هذا مع أعباء العمل الشاقة بسهولة. يتضمن كاميرا FaceTime HD بدقة 1080 بكسل، ونظام صوت بستة مكبرات صوت، وعمر بطارية يصل إلى 22 ساعة.',
                'seo_description_en' => 'Buy the ultimate Apple MacBook Pro 16" with M3 Pro chip, 32GB RAM, 1TB SSD for professional-grade performance',
                'seo_description_ar' => 'اشترِ أبل ماك بوك برو 16 بوصة مع شريحة M3 برو، ذاكرة وصول عشوائي 32 جيجابايت، قرص SSD سعة 1 تيرابايت للأداء الاحترافي',
                'seo_keys_en' => 'MacBook Pro,Apple,M3 Pro,laptop,professional',
                'seo_keys_ar' => 'ماك بوك برو,أبل,M3 برو,لابتوب,احترافي',
                'return_policy_en' => '14-day return policy. Product must be in original condition with all accessories and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 14 يومًا. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والعبوة.',
                'price' => 2499.99,
                'offer_price' => 2399.99,
                'categories' => [1, 4], // Electronics, Laptops & Computers
                'related_products' => ['DELLXPS17', 'IPHONE15PRO'], // Dell XPS 17, iPhone 15 Pro
                'accessories' => ['SONYWH1000'], // Sony Headphones
                'type' => ProductTypeEnum::FEATURED,
                'stock' => 20,
            ],

            [
                'code' => 'DELLXPS17',
                'name_en' => 'Dell XPS 17 9730 4K Laptop',
                'name_ar' => 'ديل إكس بي إس 17 9730 لابتوب 4K',
                'short_description_en' => 'Premium 17-inch laptop with 4K display, Intel Core i9, RTX 4080, and 2TB SSD',
                'short_description_ar' => 'لابتوب ممتاز مقاس 17 بوصة مع شاشة 4K، معالج إنتل كور i9، RTX 4080، وقرص SSD سعة 2 تيرابايت',
                'long_description_en' => 'The Dell XPS 17 9730 features a stunning 17-inch 4K UHD+ (3840 x 2400) InfinityEdge touch display, powered by the 13th Gen Intel Core i9 processor and NVIDIA GeForce RTX 4080 graphics. With 64GB DDR5 RAM and 2TB NVMe SSD, it delivers exceptional performance for creative professionals. The laptop includes four Thunderbolt 4 ports, a full-size SD card reader, and a 6-cell 97WHr battery providing up to 14 hours of use.',
                'long_description_ar' => 'يتميز ديل إكس بي إس 17 9730 بشاشة لمس رائعة InfinityEdge مقاس 17 بوصة بدقة 4K UHD+ (3840 × 2400)، مدعومة بمعالج إنتل كور i9 الجيل 13 ورسومات NVIDIA GeForce RTX 4080. مع ذاكرة وصول عشوائي DDR5 سعة 64 جيجابايت وقرص SSD NVMe سعة 2 تيرابايت، يوفر أداءً استثنائيًا للمحترفين المبدعين. يتضمن اللابتوب أربعة منافذ Thunderbolt 4، وقارئ بطاقة SD كامل الحجم، وبطارية 6 خلايا بسعة 97 واط/ساعة توفر استخدامًا يصل إلى 14 ساعة.',
                'seo_description_en' => 'Experience the ultimate Dell XPS 17 with 4K display, Intel Core i9, RTX 4080 graphics, and massive storage',
                'seo_description_ar' => 'جرب ديل إكس بي إس 17 المميز مع شاشة 4K، معالج إنتل كور i9، رسومات RTX 4080، وتخزين هائل',
                'seo_keys_en' => 'Dell XPS 17,laptop,4K,Intel Core i9,RTX 4080',
                'seo_keys_ar' => 'ديل إكس بي إس 17,لابتوب,4K,إنتل كور i9,RTX 4080',
                'return_policy_en' => '30-day return policy. Product must be in original condition with all accessories and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والعبوة.',
                'price' => 3299.99,
                'offer_price' => 3099.99,
                'categories' => [1, 4], // Electronics, Laptops & Computers
                'related_products' => ['MACBOOKM3'], // MacBook Pro
                'accessories' => ['SONYWH1000'], // Sony Headphones
                'type' => ProductTypeEnum::FEATURED,
                'stock' => 15,
            ],

            // Electronics - Audio & Headphones
            [
                'code' => 'SONYWH1000',
                'name_en' => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones',
                'name_ar' => 'سماعات سوني WH-1000XM5 لاسلكية بخاصية إلغاء الضوضاء',
                'short_description_en' => 'Industry-leading noise cancellation headphones with 30-hour battery life',
                'short_description_ar' => 'سماعات رأس رائدة في مجال إلغاء الضوضاء مع عمر بطارية 30 ساعة',
                'long_description_en' => 'The Sony WH-1000XM5 wireless headphones offer industry-leading noise cancellation with eight microphones and two processors for unprecedented noise reduction. The 30mm driver unit delivers exceptional sound quality with enhanced clarity. Features include speak-to-chat functionality, adaptive sound control, and up to 30 hours of battery life with quick charging (3 hours of playback from 3 minutes of charge).',
                'long_description_ar' => 'توفر سماعات سوني WH-1000XM5 اللاسلكية إلغاء ضوضاء رائد في الصناعة مع ثمانية ميكروفونات ومعالجين لتقليل الضوضاء بشكل غير مسبوق. توفر وحدة المشغل مقاس 30 مم جودة صوت استثنائية مع وضوح محسن. تشمل الميزات وظيفة التحدث للدردشة، والتحكم الصوتي التكيفي، وعمر بطارية يصل إلى 30 ساعة مع الشحن السريع (3 ساعات من التشغيل من 3 دقائق من الشحن).',
                'seo_description_en' => 'Experience industry-leading noise cancellation with the Sony WH-1000XM5 wireless headphones',
                'seo_description_ar' => 'جرب إلغاء الضوضاء الرائد في الصناعة مع سماعات سوني WH-1000XM5 اللاسلكية',
                'seo_keys_en' => 'Sony,WH-1000XM5,noise cancelling,wireless headphones,premium audio',
                'seo_keys_ar' => 'سوني,WH-1000XM5,إلغاء الضوضاء,سماعات لاسلكية,صوت ممتاز',
                'return_policy_en' => '30-day return policy. Product must be in original condition with all accessories and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والعبوة.',
                'price' => 399.99,
                'offer_price' => 349.99,
                'categories' => [1, 15], // Electronics, Audio & Headphones
                'related_products' => ['IPHONE15PRO', 'MACBOOKM3'], // iPhone 15 Pro, MacBook Pro
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::BEST_SELLER,
                'stock' => 40,
            ],

            // Fashion - Men's Fashion
            [
                'code' => 'NIKEVAPOR',
                'name_en' => 'Nike Air VaporMax 2023 Flyknit',
                'name_ar' => 'نايكي اير فيبورماكس 2023 فلاينيت',
                'short_description_en' => 'Revolutionary running shoes with VaporMax Air technology and Flyknit upper',
                'short_description_ar' => 'أحذية جري ثورية مع تقنية فيبورماكس اير وطبقة علوية فلاينيت',
                'long_description_en' => 'The Nike Air VaporMax 2023 Flyknit features a revolutionary cushioning system with VaporMax Air technology providing lightweight, responsive cushioning. The Flyknit upper wraps your foot in flexible, breathable comfort, while the innovative lacing system offers a secure fit. Made with at least 20% recycled content by weight, these shoes combine sustainability with cutting-edge performance.',
                'long_description_ar' => 'تتميز أحذية نايكي اير فيبورماكس 2023 فلاينيت بنظام تخميد ثوري مع تقنية فيبورماكس اير التي توفر تخميدًا خفيف الوزن وسريع الاستجابة. تلف الطبقة العلوية فلاينيت قدمك براحة مرنة وتنفس، بينما يوفر نظام الرباط المبتكر ملاءمة آمنة. مصنوعة بنسبة 20% على الأقل من المحتوى المعاد تدويره حسب الوزن، تجمع هذه الأحذية بين الاستدامة والأداء المتطور.',
                'seo_description_en' => 'Experience the revolutionary Nike Air VaporMax 2023 Flyknit running shoes with advanced cushioning',
                'seo_description_ar' => 'جرب أحذية نايكي اير فيبورماكس 2023 فلاينيت الثورية للجري مع تخميد متقدم',
                'seo_keys_en' => 'Nike Air VaporMax,running shoes,Flyknit,premium footwear,athletic shoes',
                'seo_keys_ar' => 'نايكي اير فيبورماكس,أحذية جري,فلاينيت,أحذية ممتازة,أحذية رياضية',
                'return_policy_en' => '30-day return policy. Unworn items only with original packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. العناصر غير المستعملة فقط مع العبوة الأصلية.',
                'price' => 209.99,
                'offer_price' => 189.99,
                'categories' => [2, 5, 31], // Fashion, Men's Fashion, Exercise & Fitness
                'related_products' => ['ADIULTRA', 'UNDERARMOURSHIRT'], // Adidas Ultraboost, Under Armour Shirt
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 60,
            ],

            [
                'code' => 'ADIULTRA',
                'name_en' => 'Adidas Ultraboost Light Running Shoes',
                'name_ar' => 'أحذية أديداس ألتر ابوست لايت للجري',
                'short_description_en' => 'The lightest Ultraboost ever with responsive cushioning and sustainable materials',
                'short_description_ar' => 'أخف ألترا بوست على الإطلاق مع تخميد متجاوب ومواد مستدامة',
                'long_description_en' => 'The Adidas Ultraboost Light features the lightest Boost midsole ever, delivering ultimate energy return while reducing overall weight. The Primeknit+ upper adapts to the changing shape of your foot during runs, while the Continental™ Rubber outsole provides exceptional traction. Made with Parley Ocean Plastic, these shoes contain at least 50% recycled content as part of Adidas\' commitment to help end plastic waste.',
                'long_description_ar' => 'تتميز أحذية أديداس ألترابوست لايت بأخف نعل أوسط بوست على الإطلاق، مما يوفر عودة طاقة مثالية مع تقليل الوزن الإجمالي. تتكيف الطبقة العلوية Primeknit+ مع الشكل المتغير لقدمك أثناء الجري، بينما يوفر نعل Continental™ المطاطي الخارجي جرًا استثنائيًا. مصنوعة من بلاستيك بارلي المحيطي، تحتوي هذه الأحذية على 50% على الأقل من المحتوى المعاد تدويره كجزء من التزام أديداس بالمساعدة في إنهاء النفايات البلاستيكية.',
                'seo_description_en' => 'Experience the lightest Ultraboost ever with the Adidas Ultraboost Light running shoes',
                'seo_description_ar' => 'جرب أخف ألترابوست على الإطلاق مع أحذية أديداس ألترابوست لايت للجري',
                'seo_keys_en' => 'Adidas Ultraboost Light,running shoes,Boost cushioning,sustainable,athletic footwear',
                'seo_keys_ar' => 'أديداس ألترابوست لايت,أحذية جري,تخميد بوست,مستدام,أحذية رياضية',
                'return_policy_en' => '30-day return policy. Unworn items only with original packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. العناصر غير المستعملة فقط مع العبوة الأصلية.',
                'price' => 189.99,
                'offer_price' => 169.99,
                'categories' => [2, 5, 31], // Fashion, Men's Fashion, Exercise & Fitness
                'related_products' => ['NIKEVAPOR', 'UNDERARMOURSHIRT'], // Nike VaporMax, Under Armour Shirt
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::BEST_SELLER,
                'stock' => 50,
            ],

            // Fashion - Women's Fashion
            [
                'code' => 'GUCCIBAGMARM',
                'name_en' => 'Gucci Marmont Medium Shoulder Bag',
                'name_ar' => 'حقيبة كتف غوتشي مارمونت متوسطة',
                'short_description_en' => 'Luxurious shoulder bag with the iconic double G hardware and chevron leather',
                'short_description_ar' => 'حقيبة كتف فاخرة مع أجهزة G المزدوجة الأيقونية وجلد الشيفرون',
                'long_description_en' => 'The Gucci GG Marmont medium shoulder bag features soft matelassé chevron leather with a vintage effect. The bag is defined by the Double G hardware inspired by an archival design from the 1970s. With an internal open pocket, zip pocket, and flap closure, it offers both style and functionality. The sliding chain strap can be worn as a shoulder strap with 22" drop or as a top handle with 12" drop.',
                'long_description_ar' => 'تتميز حقيبة كتف غوتشي GG مارمونت المتوسطة بجلد شيفرون ماتيلاسيه ناعم مع تأثير قديم. تتميز الحقيبة بأجهزة G المزدوجة المستوحاة من تصميم أرشيفي من السبعينيات. مع جيب داخلي مفتوح، وجيب بسحاب، وإغلاق بغطاء، فهي توفر الأناقة والعملية. يمكن ارتداء حزام السلسلة المنزلقة كحزام كتف مع انخفاض 22 بوصة أو كمقبض علوي مع انخفاض 12 بوصة.',
                'seo_description_en' => 'Elevate your style with the iconic Gucci Marmont Medium Shoulder Bag in matelassé chevron leather',
                'seo_description_ar' => 'ارتقِ بأسلوبك مع حقيبة كتف غوتشي مارمونت المتوسطة الأيقونية المصنوعة من جلد شيفرون ماتيلاسيه',
                'seo_keys_en' => 'Gucci Marmont,shoulder bag,luxury handbag,designer bag,chevron leather',
                'seo_keys_ar' => 'غوتشي مارمونت,حقيبة كتف,حقيبة يد فاخرة,حقيبة مصمم,جلد شيفرون',
                'return_policy_en' => '14-day return policy. Product must be in original condition with all accessories, tags, and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 14 يومًا. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والعلامات والعبوة.',
                'price' => 2490.00,
                'offer_price' => 2490.00,
                'categories' => [2, 6, 21], // Fashion, Women's Fashion, Bags & Luggage
                'related_products' => ['ZARADRESS'], // ZARA Dress
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::FEATURED,
                'stock' => 10,
            ],


            [
                'code' => 'ZARADRESS',
                'name_en' => 'ZARA Floral Print Maxi Dress',
                'name_ar' => 'فستان ماكسي بنقشة زهور من زارا',
                'short_description_en' => 'Elegant floral print maxi dress with flowing design',
                'short_description_ar' => 'فستان ماكسي أنيق بنقشة زهور مع تصميم متدفق',
                'long_description_en' => 'This ZARA Floral Print Maxi Dress features a flowing silhouette with a V-neckline and short sleeves. Made from lightweight fabric with a beautiful floral pattern, this dress is perfect for spring and summer occasions. The cinched waist creates a flattering silhouette, while the maxi length adds elegance. The dress includes a concealed back zip fastening and is fully lined for comfort.',
                'long_description_ar' => 'يتميز فستان زارا ماكسي المزخرف بالزهور بتصميم متدفق مع فتحة رقبة على شكل V وأكمام قصيرة. مصنوع من نسيج خفيف الوزن مع نمط زهري جميل، هذا الفستان مثالي لمناسبات الربيع والصيف. يخلق الخصر المشدود صورة ظلية مغرية، بينما يضيف الطول الماكسي أناقة. يتضمن الفستان سحاب خلفي مخفي وهو مبطن بالكامل للراحة.',
                'seo_description_en' => 'Stay stylish with ZARA\'s Floral Print Maxi Dress, perfect for seasonal occasions',
                'seo_description_ar' => 'ابقي أنيقة مع فستان ماكسي بنقشة زهور من زارا، مثالي للمناسبات الموسمية',
                'seo_keys_en' => 'ZARA,floral dress,maxi dress,summer fashion,women\'s clothing',
                'seo_keys_ar' => 'زارا,فستان زهري,فستان ماكسي,أزياء صيفية,ملابس نسائية',
                'return_policy_en' => '30-day return policy. Unworn items only with original tags and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. العناصر غير المستعملة فقط مع العلامات والعبوة الأصلية.',
                'price' => 89.99,
                'offer_price' => 69.99,
                'categories' => [2, 6], // Fashion, Women's Fashion
                'related_products' => ['GUCCIBAGMARM'], // Gucci Bag
                'accessories' => ['GUCCIBAGMARM'], // Gucci Bag as accessory
                'type' => ProductTypeEnum::BEST_SELLER,
                'stock' => 75,
            ],

            // Home & Kitchen - Kitchen & Dining
            [
                'code' => 'DYSONV15',
                'name_en' => 'Dyson V15 Detect Absolute Cordless Vacuum',
                'name_ar' => 'مكنسة دايسون V15 ديتيكت أبسولوت اللاسلكية',
                'short_description_en' => 'Advanced cordless vacuum with laser dust detection and intelligent suction',
                'short_description_ar' => 'مكنسة كهربائية لاسلكية متطورة مع كشف الغبار بالليزر وشفط ذكي',
                'long_description_en' => 'The Dyson V15 Detect Absolute features a precisely-angled laser that makes invisible dust visible on hard floors. It includes an acoustic piezo sensor that sizes and counts dust particles, automatically increasing suction power when needed. The Anti-tangle Hair Screw tool removes hair from the brush bar, while the LCD screen displays particle count and performance data. With up to 60 minutes of run time and a 0.2-gallon bin capacity, it delivers exceptional cleaning power.',
                'long_description_ar' => 'تتميز مكنسة دايسون V15 ديتيكت أبسولوت بليزر دقيق الزاوية يجعل الغبار غير المرئي مرئيًا على الأرضيات الصلبة. تتضمن مستشعر بيزو صوتي يقيس ويعد جزيئات الغبار، مما يزيد من قوة الشفط تلقائيًا عند الحاجة. تزيل أداة مسمار الشعر المضادة للتشابك الشعر من فرشاة البار، بينما تعرض شاشة LCD عدد الجسيمات وبيانات الأداء. مع وقت تشغيل يصل إلى 60 دقيقة وسعة صندوق تبلغ 0.2 جالون، فإنها توفر قوة تنظيف استثنائية.',
                'seo_description_en' => 'Experience revolutionary cleaning with the Dyson V15 Detect Absolute Cordless Vacuum with laser technology',
                'seo_description_ar' => 'جرب التنظيف الثوري مع مكنسة دايسون V15 ديتيكت أبسولوت اللاسلكية بتقنية الليزر',
                'seo_keys_en' => 'Dyson V15 Detect,cordless vacuum,laser dust detection,home appliance,cleaning',
                'seo_keys_ar' => 'دايسون V15 ديتيكت,مكنسة لاسلكية,كشف الغبار بالليزر,جهاز منزلي,تنظيف',
                'return_policy_en' => '30-day return policy. Product must be in original condition with all accessories and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والعبوة.',
                'price' => 799.99,
                'offer_price' => 749.99,
                'categories' => [7, 22, 26], // Home & Kitchen, Kitchen & Dining, Home Appliances
                'related_products' => ['PHILIPSAIRFRYER'], // Philips Airfryer
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::FEATURED,
                'stock' => 25,
            ],

            [
                'code' => 'PHILIPSAIRFRYER',
                'name_en' => 'Philips Airfryer XXL Smart Digital',
                'name_ar' => 'فيليبس إيرفراير XXL سمارت ديجيتال',
                'short_description_en' => 'Smart digital air fryer with Fat Removal technology and connected recipes',
                'short_description_ar' => 'قلاية هوائية رقمية ذكية مع تقنية إزالة الدهون ووصفات متصلة',
                'long_description_en' => 'The Philips Airfryer XXL Smart Digital uses hot air to fry your favorite foods with little or no added oil. New Fat Removal technology separates and captures excess fat, making it the healthiest way to fry. The XXL size easily fits a whole chicken or 3 lbs of fries. With the NutriU app, you can access hundreds of recipes and control your Airfryer remotely. The digital display with 5 preset cooking programs makes cooking easier than ever.',
                'long_description_ar' => 'تستخدم فيليبس إيرفراير XXL سمارت ديجيتال الهواء الساخن لقلي أطعمتك المفضلة بقليل من الزيت المضاف أو بدونه. تفصل تقنية إزالة الدهون الجديدة الدهون الزائدة وتلتقطها، مما يجعلها الطريقة الأكثر صحة للقلي. يناسب حجم XXL بسهولة دجاجة كاملة أو 3 أرطال من البطاطس المقلية. باستخدام تطبيق NutriU، يمكنك الوصول إلى مئات الوصفات والتحكم في الإيرفراير عن بُعد. تجعل الشاشة الرقمية مع 5 برامج طهي مسبقة الضبط الطهي أسهل من أي وقت مضى.',
                'seo_description_en' => 'Cook healthier meals with the Philips Airfryer XXL Smart Digital featuring Fat Removal technology',
                'seo_description_ar' => 'اطهي وجبات أكثر صحة مع فيليبس إيرفراير XXL سمارت ديجيتال المزودة بتقنية إزالة الدهون',
                'seo_keys_en' => 'Philips Airfryer,air fryer,smart kitchen,healthy cooking,fat removal',
                'seo_keys_ar' => 'فيليبس إيرفراير,قلاية هوائية,مطبخ ذكي,طهي صحي,إزالة الدهون',
                'return_policy_en' => '30-day return policy. Product must be in original condition with all accessories and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والعبوة.',
                'price' => 349.99,
                'offer_price' => 299.99,
                'categories' => [7, 22, 26], // Home & Kitchen, Kitchen & Dining, Home Appliances
                'related_products' => ['DYSONV15'], // Dyson V15 Vacuum
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::BEST_SELLER,
                'stock' => 35,
            ],

            // Home & Kitchen - Furniture
            [
                'code' => 'IKEABILLY',
                'name_en' => 'IKEA BILLY Bookcase',
                'name_ar' => 'مكتبة إيكيا بيلي',
                'short_description_en' => 'Classic bookcase with adjustable shelves for flexible storage',
                'short_description_ar' => 'مكتبة كلاسيكية مع رفوف قابلة للتعديل للتخزين المرن',
                'long_description_en' => 'The IKEA BILLY bookcase is a modern bookcase with a timeless design that\'s been a customer favorite since 1979. It features adjustable shelves that can be arranged according to your needs, and the height extension unit allows you to make the most of the wall space. Made from wood with a laminate finish, it\'s durable and easy to clean. The bookcase measures 31.5" W x 11" D x 79.5" H and is available in multiple colors to match your home decor.',
                'long_description_ar' => 'مكتبة إيكيا بيلي هي مكتبة عصرية ذات تصميم خالد كانت المفضلة لدى العملاء منذ عام 1979. تتميز برفوف قابلة للتعديل يمكن ترتيبها وفقًا لاحتياجاتك، وتتيح لك وحدة امتداد الارتفاع الاستفادة القصوى من مساحة الحائط. مصنوعة من الخشب مع تشطيب لامينيت، فهي متينة وسهلة التنظيف. تقيس المكتبة 31.5 بوصة عرضًا × 11 بوصة عمقًا × 79.5 بوصة ارتفاعًا وهي متوفرة بألوان متعددة لتتناسب مع ديكور منزلك.',
                'seo_description_en' => 'Organize your home with the classic IKEA BILLY Bookcase featuring adjustable shelves',
                'seo_description_ar' => 'نظم منزلك مع مكتبة إيكيا بيلي الكلاسيكية المزودة برفوف قابلة للتعديل',
                'seo_keys_en' => 'IKEA BILLY,bookcase,furniture,home storage,bookshelves',
                'seo_keys_ar' => 'إيكيا بيلي,مكتبة,أثاث,تخزين منزلي,رفوف كتب',
                'return_policy_en' => '365-day return policy. Product must be in resalable condition with original packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 365 يومًا. يجب أن يكون المنتج في حالة قابلة لإعادة البيع مع العبوة الأصلية.',
                'price' => 179.99,
                'offer_price' => 179.99,
                'categories' => [7, 23, 24], // Home & Kitchen, Furniture, Home Decor
                'related_products' => [], // No related products
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::BEST_SELLER,
                'stock' => 100,
            ],

            // Beauty & Personal Care - Skincare
            [
                'code' => 'NEUTROGENAHYDRO',
                'name_en' => 'Neutrogena Hydro Boost Water Gel',
                'name_ar' => 'نيوتروجينا هيدرو بوست جل مائي',
                'short_description_en' => 'Award-winning hydrating face moisturizer with hyaluronic acid',
                'short_description_ar' => 'مرطب وجه مائي حائز على جوائز مع حمض الهيالورونيك',
                'long_description_en' => 'Neutrogena Hydro Boost Water Gel is an award-winning water-based face moisturizer that instantly quenches dry skin and keeps it looking smooth, supple, and hydrated all day. The unique gel formula contains hyaluronic acid, a hydrator found naturally in the skin that attracts moisture and locks it in. Non-comedogenic, oil-free, dye-free, and suitable for all skin types, especially dry skin. The 1.7 oz jar provides long-lasting hydration that works with your skin\'s natural moisture barrier.',
                'long_description_ar' => 'نيوتروجينا هيدرو بوست جل مائي هو مرطب وجه مائي حائز على جوائز يروي البشرة الجافة على الفور ويحافظ على مظهرها ناعمًا ومرنًا ورطبًا طوال اليوم. تحتوي تركيبة الجل الفريدة على حمض الهيالورونيك، وهو مرطب موجود بشكل طبيعي في البشرة يجذب الرطوبة ويحبسها. غير مسبب للرؤوس السوداء، خالي من الزيوت، خالي من الصبغة، ومناسب لجميع أنواع البشرة، خاصة البشرة الجافة. توفر الجرة سعة 1.7 أونصة ترطيبًا طويل الأمد يعمل مع حاجز الرطوبة الطبيعي للبشرة.',
                'seo_description_en' => 'Hydrate your skin with Neutrogena Hydro Boost Water Gel featuring hyaluronic acid',
                'seo_description_ar' => 'رطب بشرتك مع نيوتروجينا هيدرو بوست جل مائي المحتوي على حمض الهيالورونيك',
                'seo_keys_en' => 'Neutrogena Hydro Boost,water gel,moisturizer,hyaluronic acid,skincare',
                'seo_keys_ar' => 'نيوتروجينا هيدرو بوست,جل مائي,مرطب,حمض الهيالورونيك,العناية بالبشرة',
                'return_policy_en' => '30-day return policy. Unopened items only with original packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. العناصر غير المفتوحة فقط مع العبوة الأصلية.',
                'price' => 24.99,
                'offer_price' => 19.99,
                'categories' => [8, 27], // Beauty & Personal Care, Skincare
                'related_products' => ['LOREALPARIS'], // L'Oréal Paris Moisturizer
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::BEST_SELLER,
                'stock' => 150,
            ],

            [
                'code' => 'LOREALPARIS',
                'name_en' => 'L\'Oréal Paris Revitalift Triple Power Anti-Aging Moisturizer',
                'name_ar' => 'لوريال باريس ريفيتاليفت تريبل باور مرطب مضاد للشيخوخة',
                'short_description_en' => 'Anti-aging face moisturizer with Pro-Retinol, Vitamin C, and Hyaluronic Acid',
                'short_description_ar' => 'مرطب وجه مضاد للشيخوخة مع برو-ريتينول، فيتامين سي، وحمض الهيالورونيك',
                'long_description_en' => 'L\'Oréal Paris Revitalift Triple Power Anti-Aging Moisturizer helps transform the look of aging skin with a triple-action formula. It contains Pro-Retinol to reduce wrinkles, Vitamin C to brighten skin and even tone, and Hyaluronic Acid to hydrate and plump skin. After just one week, wrinkles appear visibly reduced, skin feels firmer, and complexion looks brighter. The lightweight, non-greasy cream absorbs quickly and can be used morning and night.',
                'long_description_ar' => 'يساعد مرطب لوريال باريس ريفيتاليفت تريبل باور المضاد للشيخوخة على تحويل مظهر البشرة المتقدمة في العمر بتركيبة ثلاثية الفعالية. يحتوي على برو-ريتينول لتقليل التجاعيد، وفيتامين سي لتفتيح البشرة وتوحيد لونها، وحمض الهيالورونيك لترطيب البشرة وملئها. بعد أسبوع واحد فقط، تبدو التجاعيد أقل وضوحًا، وتشعر البشرة بأنها أكثر ثباتًا، ويبدو لون البشرة أكثر إشراقًا. يمتص الكريم الخفيف وغير الدهني بسرعة ويمكن استخدامه صباحًا ومساءً.',
                'seo_description_en' => 'Fight signs of aging with L\'Oréal Paris Revitalift Triple Power Anti-Aging Moisturizer',
                'seo_description_ar' => 'حارب علامات الشيخوخة مع لوريال باريس ريفيتاليفت تريبل باور مرطب مضاد للشيخوخة',
                'seo_keys_en' => 'L\'Oréal Paris,Revitalift,anti-aging,moisturizer,skincare',
                'seo_keys_ar' => 'لوريال باريس,ريفيتاليفت,مضاد للشيخوخة,مرطب,العناية بالبشرة',
                'return_policy_en' => '30-day return policy. Unopened items only with original packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. العناصر غير المفتوحة فقط مع العبوة الأصلية.',
                'price' => 29.99,
                'offer_price' => 24.99,
                'categories' => [8, 27], // Beauty & Personal Care, Skincare
                'related_products' => ['NEUTROGENAHYDRO'], // Neutrogena Hydro Boost
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::FEATURED,
                'stock' => 120,
            ],

            // Sports & Outdoors - Exercise & Fitness
            [
                'code' => 'UNDERARMOURSHIRT',
                'name_en' => 'Under Armour Men\'s Tech 2.0 Short Sleeve T-Shirt',
                'name_ar' => 'تي شيرت رجالي قصير الأكمام تك 2.0 من أندر آرمور',
                'short_description_en' => 'Quick-drying, ultra-soft athletic training t-shirt with anti-odor technology',
                'short_description_ar' => 'تي شيرت تدريب رياضي سريع الجفاف وفائق النعومة مع تقنية مضادة للروائح',
                'long_description_en' => 'The Under Armour Men\'s Tech 2.0 Short Sleeve T-Shirt is made from 100% polyester with UA Tech™ fabric that has a softer, more natural feel. It wicks sweat away and dries really fast. The anti-odor technology prevents the growth of odor-causing microbes, keeping your gear fresher longer. With a looser, more relaxed fit and raglan sleeves for total mobility, this t-shirt is perfect for training and everyday wear. Available in multiple colors and sizes from S to 3XL.',
                'long_description_ar' => 'تي شيرت رجالي قصير الأكمام تك 2.0 من أندر آرمور مصنوع من 100% بوليستر مع قماش UA Tech™ الذي يتميز بملمس أنعم وأكثر طبيعية. يمتص العرق ويجف بسرعة كبيرة. تمنع تقنية مضادة للروائح نمو الميكروبات المسببة للرائحة، مما يحافظ على معداتك أكثر نظافة لفترة أطول. مع ملاءمة أكثر فضفاضة واسترخاءً وأكمام راجلان للحركة الكاملة، هذا التي شيرت مثالي للتدريب والارتداء اليومي. متوفر بألوان وأحجام متعددة من S إلى 3XL.',
                'seo_description_en' => 'Stay dry and comfortable with the Under Armour Men\'s Tech 2.0 Short Sleeve T-Shirt',
                'seo_description_ar' => 'ابق جافًا ومرتاحًا مع تي شيرت رجالي قصير الأكمام تك 2.0 من أندر آرمور',
                'seo_keys_en' => 'Under Armour,Tech 2.0,t-shirt,athletic wear,workout clothes',
                'seo_keys_ar' => 'أندر آرمور,تك 2.0,تي شيرت,ملابس رياضية,ملابس تمرين',
                'return_policy_en' => '60-day return policy. Unworn items only with original tags and packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 60 يومًا. العناصر غير المستعملة فقط مع العلامات والعبوة الأصلية.',
                'price' => 25.00,
                'offer_price' => 18.75,
                'categories' => [2, 5, 9, 31], // Fashion, Men's Fashion, Sports & Outdoors, Exercise & Fitness
                'related_products' => ['NIKEVAPOR', 'ADIULTRA'], // Nike VaporMax, Adidas Ultraboost
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::BEST_SELLER,
                'stock' => 200,
            ],

            // Toys & Games - Board Games & Puzzles
            [
                'code' => 'LEGOCLASSIC',
                'name_en' => 'LEGO Classic Large Creative Brick Box',
                'name_ar' => 'ليغو كلاسيك صندوق قطع إبداعي كبير',
                'short_description_en' => 'Building toy with 790 colorful pieces for creative play',
                'short_description_ar' => 'لعبة بناء مع 790 قطعة ملونة للعب الإبداعي',
                'long_description_en' => 'The LEGO Classic Large Creative Brick Box contains 790 LEGO pieces in 33 different colors, with special pieces including doors, windows, wheels, eyes, and propellers. It comes with a green baseplate (8" x 16") and features special elements and ideas to inspire creative building. The storage box keeps everything organized, and the set is compatible with all LEGO construction sets for creative building. Suitable for ages 4 and up, it\'s perfect for both individual and group play.',
                'long_description_ar' => 'يحتوي صندوق قطع ليغو كلاسيك الإبداعي الكبير على 790 قطعة ليغو بـ 33 لونًا مختلفًا، مع قطع خاصة تشمل الأبواب والنوافذ والعجلات والعيون والمراوح. يأتي مع لوحة أساس خضراء (8 بوصات × 16 بوصة) ويتميز بعناصر وأفكار خاصة لإلهام البناء الإبداعي. يحافظ صندوق التخزين على تنظيم كل شيء، والمجموعة متوافقة مع جميع مجموعات بناء ليغو للبناء الإبداعي. مناسب للأعمار من 4 سنوات وما فوق، وهو مثالي لكل من اللعب الفردي والجماعي.',
                'seo_description_en' => 'Inspire creativity with the LEGO Classic Large Creative Brick Box featuring 790 pieces',
                'seo_description_ar' => 'ألهم الإبداع مع صندوق قطع ليغو كلاسيك الإبداعي الكبير المكون من 790 قطعة',
                'seo_keys_en' => 'LEGO Classic,building blocks,creative toys,children toys,construction set',
                'seo_keys_ar' => 'ليغو كلاسيك,قطع بناء,ألعاب إبداعية,ألعاب أطفال,مجموعة بناء',
                'return_policy_en' => '90-day return policy. Product must be in original condition with all packaging.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 90 يومًا. يجب أن يكون المنتج في حالته الأصلية مع كل العبوة.',
                'price' => 59.99,
                'offer_price' => 49.99,
                'categories' => [10, 35, 36], // Toys & Games, Board Games & Puzzles, Action Figures & Collectibles
                'related_products' => [], // No related products
                'accessories' => [], // No accessories
                'type' => ProductTypeEnum::BEST_SELLER,
                'stock' => 80,
            ]

        ];
        // First pass: Create all products without relationships
        $productInstances = [];
        foreach ($products as $productData) {
            // Store categories, related products, and accessories for later use
            $categories = $productData['categories'] ?? [];
            $relatedProducts = $productData['related_products'] ?? [];
            $accessories = $productData['accessories'] ?? [];

            // Create translations for each product
            $translations = [];

            // Process translations for each supported locale
            foreach ($supportedLocales as $locale) {
                if ($locale === 'en') {
                    $translations[$locale] = [
                        'name' => $productData['name_en'],
                        'seo_description' => $productData['seo_description_en'],
                        'seo_keys' => $productData['seo_keys_en'],
                        'short_description' => $productData['short_description_en'],
                        'long_description' => $productData['long_description_en'],
                        'return_policy' => $productData['return_policy_en'],
                    ];
                } elseif ($locale === 'ar') {
                    $translations[$locale] = [
                        'name' => $productData['name_ar'],
                        'seo_description' => $productData['seo_description_ar'],
                        'seo_keys' => $productData['seo_keys_ar'],
                        'short_description' => $productData['short_description_ar'],
                        'long_description' => $productData['long_description_ar'],
                        'return_policy' => $productData['return_policy_ar'],
                    ];
                }
            }

            // Remove translation-specific keys and relationship arrays from product data
            $cleanProductData = array_diff_key($productData, array_flip([
                'name_en',
                'name_ar',
                'seo_description_en',
                'seo_description_ar',
                'seo_keys_en',
                'seo_keys_ar',
                'short_description_en',
                'short_description_ar',
                'long_description_en',
                'long_description_ar',
                'return_policy_en',
                'return_policy_ar',
                'categories',
                'related_products',
                'accessories'
            ]));

            // Set default values for common fields
            $defaultValues = [
                'image' => null,
                'video_url' => null,
                'position' => 1,
                'status' => 1,
                'currency' => 'USD',
                'approval_status' => 1,
                'offer_start_date' => now()->subDays(rand(1, 30)),
                'offer_end_date' => now()->addDays(rand(1, 30)),
            ];

            // Merge base product data with default values and translations
            $product = Product::create(array_merge($defaultValues, $cleanProductData, $translations));

            // Store the newly created product in our productInstances array
            $productInstances[$productData['code']] = $product;

            // Attach categories immediately if they exist
            if (!empty($categories)) {
                $product->categories()->attach($categories);
            }
        }

        // Second pass: Set up relationships for related products and accessories
        foreach ($products as $productData) {
            if (empty($productData['related_products']) && empty($productData['accessories'])) {
                continue; // Skip if no relationships to process
            }

            $product = $productInstances[$productData['code']];

            // Attach related products
            if (!empty($productData['related_products'])) {
                $relatedProductIds = [];
                foreach ($productData['related_products'] as $relatedProductCode) {
                    if (isset($productInstances[$relatedProductCode])) {
                        $relatedProductIds[] = $productInstances[$relatedProductCode]->id;
                    }
                }
                if (!empty($relatedProductIds)) {
                    $product->relatedProducts()->attach($relatedProductIds);
                }
            }

            // Attach accessories
            if (!empty($productData['accessories'])) {
                $accessoryIds = [];
                foreach ($productData['accessories'] as $accessoryCode) {
                    if (isset($productInstances[$accessoryCode])) {
                        $accessoryIds[] = $productInstances[$accessoryCode]->id;
                    }
                }
                if (!empty($accessoryIds)) {
                    $product->accessories()->attach($accessoryIds);
                }
            }
        }
    }
}