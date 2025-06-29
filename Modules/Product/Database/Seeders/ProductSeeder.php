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
            // T-shirts Category
            [
                'code' => 'GRADIENT-TSHIRT',
                'name_en' => 'Gradient Graphic T-shirt',
                'name_ar' => 'تي شيرت بتدرج لوني مع رسمة',
                'short_description_en' => 'Stylish gradient graphic t-shirt with modern design and comfortable fit',
                'short_description_ar' => 'تي شيرت أنيق بتدرج لوني مع تصميم عصري وملائم مريح',
                'long_description_en' => 'This gradient graphic t-shirt features a unique color transition design that makes it stand out. Made from high-quality cotton blend fabric that ensures comfort throughout the day. The modern graphic design adds a contemporary touch to your casual wardrobe. Perfect for everyday wear, weekend outings, or casual social gatherings.',
                'long_description_ar' => 'يتميز هذا التي شيرت بتصميم انتقال لوني فريد يجعله مميزاً. مصنوع من قماش قطني مخلوط عالي الجودة يضمن الراحة طوال اليوم. التصميم الجرافيكي العصري يضيف لمسة معاصرة لخزانة ملابسك غير الرسمية. مثالي للارتداء اليومي، والنزهات في نهاية الأسبوع، أو التجمعات الاجتماعية غير الرسمية.',
                'seo_description_en' => 'Buy stylish gradient graphic t-shirt with modern design, comfortable fit, and premium cotton blend fabric',
                'seo_description_ar' => 'اشترِ تي شيرت أنيق بتدرج لوني مع تصميم عصري، ملائم مريح، وقماش قطني مميز',
                'seo_keys_en' => 'gradient t-shirt,graphic tee,casual wear,cotton t-shirt,modern design',
                'seo_keys_ar' => 'تي شيرت متدرج,تي شيرت بطباعة,ملابس كاجوال,تي شيرت قطني,تصميم عصري',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 145.00,
                'offer_price' => 125.00,
                'categories' => [1], // T-shirts
                'related_products' => ['BLACK-STRIPED-TSHIRT', 'SLEEVE-STRIPED-TSHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 25,
            ],

            [
                'code' => 'POLO-TIPPING-DETAILS',
                'name_en' => 'Polo with Tipping Details',
                'name_ar' => 'بولو مع تفاصيل الحواف',
                'short_description_en' => 'Classic polo shirt with elegant tipping details on collar and cuffs',
                'short_description_ar' => 'قميص بولو كلاسيكي مع تفاصيل أنيقة على الياقة والأكمام',
                'long_description_en' => 'This sophisticated polo shirt combines classic styling with modern details. Features contrasting tipping on the collar and cuffs for an elevated look. Made from premium pique cotton that offers breathability and durability. The tailored fit ensures a polished appearance suitable for both casual and semi-formal occasions.',
                'long_description_ar' => 'يجمع قميص البولو المتطور هذا بين التصميم الكلاسيكي والتفاصيل العصرية. يتميز بحواف متباينة على الياقة والأكمام لمظهر أنيق. مصنوع من قطن البيكيه المميز الذي يوفر التهوية والمتانة. القصة المفصلة تضمن مظهراً أنيقاً مناسب للمناسبات غير الرسمية وشبه الرسمية.',
                'seo_description_en' => 'Shop premium polo shirt with tipping details, classic design, and comfortable pique cotton fabric',
                'seo_description_ar' => 'تسوق قميص بولو مميز مع تفاصيل الحواف، تصميم كلاسيكي، وقماش قطني بيكيه مريح',
                'seo_keys_en' => 'polo shirt,tipping details,pique cotton,classic polo,elegant shirt',
                'seo_keys_ar' => 'قميص بولو,تفاصيل الحواف,قطن بيكيه,بولو كلاسيكي,قميص أنيق',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 180.00,
                'offer_price' => 155.00,
                'categories' => [2], // Polo
                'related_products' => ['CHECKERED-SHIRT', 'GRADIENT-TSHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 18,
            ],

            [
                'code' => 'BLACK-STRIPED-TSHIRT',
                'name_en' => 'Black Striped T-shirt',
                'name_ar' => 'تي شيرت مخطط أسود',
                'short_description_en' => 'Classic black and white striped t-shirt with timeless appeal',
                'short_description_ar' => 'تي شيرت مخطط أسود وأبيض كلاسيكي بجاذبية خالدة',
                'long_description_en' => 'A wardrobe essential featuring classic black and white horizontal stripes. This versatile t-shirt is crafted from soft cotton jersey fabric for maximum comfort and easy care. The timeless stripe pattern makes it perfect for layering or wearing alone. Ideal for creating effortless casual looks that never go out of style.',
                'long_description_ar' => 'قطعة أساسية في الخزانة تتميز بخطوط أفقية كلاسيكية باللونين الأسود والأبيض. هذا التي شيرت متعدد الاستخدامات مصنوع من قماش جيرسي القطني الناعم لأقصى راحة وسهولة العناية. نمط الخطوط الخالد يجعله مثالياً للطبقات أو الارتداء منفرداً. مثالي لإنشاء إطلالات كاجوال بسيطة لا تخرج عن الموضة أبداً.',
                'seo_description_en' => 'Get the classic black striped t-shirt with timeless design, soft cotton fabric, and versatile styling',
                'seo_description_ar' => 'احصل على التي شيرت المخطط الأسود الكلاسيكي بتصميم خالد، قماش قطني ناعم، وتنسيق متعدد الاستخدامات',
                'seo_keys_en' => 'striped t-shirt,black white stripes,cotton jersey,classic tee,casual wear',
                'seo_keys_ar' => 'تي شيرت مخطط,خطوط سوداء بيضاء,جيرسي قطني,تي شيرت كلاسيكي,ملابس كاجوال',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 120.00,
                'offer_price' => 99.00,
                'categories' => [1], // T-shirts
                'related_products' => ['GRADIENT-TSHIRT', 'SLEEVE-STRIPED-TSHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 30,
            ],

            [
                'code' => 'SKINNY-FIT-JEANS',
                'name_en' => 'Skinny Fit Jeans',
                'name_ar' => 'جينز ضيق',
                'short_description_en' => 'Modern skinny fit jeans with premium denim fabric and contemporary styling',
                'short_description_ar' => 'جينز ضيق عصري بقماش دنيم مميز وتصميم معاصر',
                'long_description_en' => 'These skinny fit jeans offer a sleek, modern silhouette that\'s perfect for contemporary fashion. Crafted from premium stretch denim that provides comfort and freedom of movement while maintaining the fitted look. Features classic five-pocket styling with subtle distressing details. The versatile dark wash makes them suitable for both casual and dressed-up occasions.',
                'long_description_ar' => 'يوفر هذا الجينز الضيق صورة ظلية أنيقة وعصرية مثالية للموضة المعاصرة. مصنوع من دنيم مطاطي مميز يوفر الراحة وحرية الحركة مع الحفاظ على المظهر الملائم. يتميز بتصميم الجيوب الخمسة الكلاسيكي مع تفاصيل تآكل خفيفة. الغسلة الداكنة متعددة الاستخدامات تجعلها مناسبة للمناسبات غير الرسمية والأنيقة.',
                'seo_description_en' => 'Shop premium skinny fit jeans with stretch denim, modern styling, and versatile dark wash',
                'seo_description_ar' => 'تسوق جينز ضيق مميز مع دنيم مطاطي، تصميم عصري، وغسلة داكنة متعددة الاستخدامات',
                'seo_keys_en' => 'skinny jeans,stretch denim,fitted jeans,dark wash,modern jeans',
                'seo_keys_ar' => 'جينز ضيق,دنيم مطاطي,جينز ملائم,غسلة داكنة,جينز عصري',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 240.00,
                'offer_price' => 200.00,
                'categories' => [3], // Jeans
                'related_products' => ['CHECKERED-SHIRT', 'POLO-TIPPING-DETAILS'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 15,
            ],

            [
                'code' => 'CHECKERED-SHIRT',
                'name_en' => 'Checkered Shirt',
                'name_ar' => 'قميص مربعات',
                'short_description_en' => 'Classic checkered shirt with bold pattern and comfortable cotton fabric',
                'short_description_ar' => 'قميص مربعات كلاسيكي بنمط جريء وقماش قطني مريح',
                'long_description_en' => 'This bold checkered shirt makes a statement with its eye-catching red and navy pattern. Crafted from pure cotton flannel for exceptional softness and warmth. Features classic shirt details including button-down collar, chest pockets, and curved hem. Perfect for layering or wearing as a standalone piece during cooler weather.',
                'long_description_ar' => 'يلفت هذا القميص المربع الجريء الانتباه بنمطه الأحمر والأزرق البحري الملفت للنظر. مصنوع من فانيلا القطن الخالص لنعومة ودفء استثنائيين. يتميز بتفاصيل القميص الكلاسيكية بما في ذلك الياقة المغلقة بأزرار، والجيوب الصدرية، والحافة المنحنية. مثالي للطبقات أو الارتداء كقطعة منفردة خلال الطقس البارد.',
                'seo_description_en' => 'Buy classic checkered shirt with bold pattern, pure cotton flannel, and timeless styling',
                'seo_description_ar' => 'اشترِ قميص مربعات كلاسيكي بنمط جريء، فانيلا قطن خالص، وتصميم خالد',
                'seo_keys_en' => 'checkered shirt,flannel shirt,cotton shirt,red navy pattern,casual shirt',
                'seo_keys_ar' => 'قميص مربعات,قميص فانيلا,قميص قطني,نمط أحمر أزرق,قميص كاجوال',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 180.00,
                'offer_price' => 149.00,
                'categories' => [4], // Shirts
                'related_products' => ['SKINNY-FIT-JEANS', 'POLO-TIPPING-DETAILS'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 22,
            ],

            [
                'code' => 'SLEEVE-STRIPED-TSHIRT',
                'name_en' => 'Sleeve Striped T-shirt',
                'name_ar' => 'تي شيرت مخطط الأكمام',
                'short_description_en' => 'Vibrant orange t-shirt with contrasting striped sleeves for a sporty look',
                'short_description_ar' => 'تي شيرت برتقالي نابض بالحياة مع أكمام مخططة متباينة لمظهر رياضي',
                'long_description_en' => 'This energetic t-shirt features a bright orange body with contrasting black and white striped sleeves. Made from moisture-wicking athletic fabric that keeps you comfortable during activities. The color-blocked design adds a modern sporty aesthetic perfect for casual wear, gym sessions, or outdoor activities. Offers excellent durability and easy care.',
                'long_description_ar' => 'يتميز هذا التي شيرت النشط بجسم برتقالي مشرق مع أكمام مخططة متباينة باللونين الأسود والأبيض. مصنوع من قماش رياضي ماص للرطوبة يبقيك مرتاحاً أثناء الأنشطة. التصميم متعدد الألوان يضيف جمالية رياضية عصرية مثالية للملابس غير الرسمية، جلسات الصالة الرياضية، أو الأنشطة الخارجية. يوفر متانة ممتازة وسهولة العناية.',
                'seo_description_en' => 'Get the vibrant sleeve striped t-shirt with moisture-wicking fabric, sporty design, and comfortable fit',
                'seo_description_ar' => 'احصل على التي شيرت المخطط الأكمام النابض بالحياة مع قماش ماص للرطوبة، تصميم رياضي، وملائم مريح',
                'seo_keys_en' => 'striped sleeve shirt,orange t-shirt,athletic wear,moisture wicking,sporty tee',
                'seo_keys_ar' => 'قميص مخطط الأكمام,تي شيرت برتقالي,ملابس رياضية,ماص للرطوبة,تي شيرت رياضي',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 130.00,
                'offer_price' => 110.00,
                'categories' => [1], // T-shirts
                'related_products' => ['GRADIENT-TSHIRT', 'BLACK-STRIPED-TSHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 28,
            ],

            [
                'code' => 'CLASSIC-WHITE-TSHIRT',
                'name_en' => 'Classic White T-shirt',
                'name_ar' => 'تي شيرت أبيض كلاسيكي',
                'short_description_en' => 'Essential white t-shirt made from premium cotton for everyday comfort',
                'short_description_ar' => 'تي شيرت أبيض أساسي مصنوع من القطن المميز للراحة اليومية',
                'long_description_en' => 'The perfect wardrobe essential, this classic white t-shirt is crafted from 100% premium cotton for superior comfort and durability. Features a timeless crew neck design with reinforced seams. The breathable fabric makes it ideal for layering or wearing alone. A versatile piece that pairs well with jeans, shorts, or under jackets.',
                'long_description_ar' => 'القطعة الأساسية المثالية للخزانة، هذا التي شيرت الأبيض الكلاسيكي مصنوع من 100% قطن مميز للراحة والمتانة الفائقة. يتميز بتصميم رقبة دائرية خالدة مع خياطة معززة. القماش القابل للتنفس يجعله مثالياً للطبقات أو الارتداء منفرداً. قطعة متعددة الاستخدامات تتناسب جيداً مع الجينز أو الشورتس أو تحت الجاكيت.',
                'seo_description_en' => 'Shop classic white t-shirt made from premium cotton, perfect for everyday wear and layering',
                'seo_description_ar' => 'تسوق التي شيرت الأبيض الكلاسيكي المصنوع من القطن المميز، مثالي للارتداء اليومي والطبقات',
                'seo_keys_en' => 'white t-shirt,classic tee,cotton t-shirt,basic white shirt,everyday wear',
                'seo_keys_ar' => 'تي شيرت أبيض,تي شيرت كلاسيكي,تي شيرت قطني,قميص أبيض أساسي,ملابس يومية',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 95.00,
                'offer_price' => 79.00,
                'categories' => [1], // T-shirts
                'related_products' => ['BLACK-STRIPED-TSHIRT', 'GRADIENT-TSHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 40,
            ],

            [
                'code' => 'NAVY-POLO-SHIRT',
                'name_en' => 'Navy Polo Shirt',
                'name_ar' => 'قميص بولو أزرق بحري',
                'short_description_en' => 'Smart navy polo shirt with classic fit and premium pique cotton',
                'short_description_ar' => 'قميص بولو أزرق بحري أنيق بقصة كلاسيكية وقطن بيكيه مميز',
                'long_description_en' => 'This sophisticated navy polo shirt offers timeless elegance with its classic fit and rich navy color. Made from premium pique cotton that provides excellent breathability and maintains its shape wash after wash. Features a ribbed collar and cuffs with a three-button placket. Perfect for business casual, weekend wear, or smart casual occasions.',
                'long_description_ar' => 'يوفر قميص البولو الأزرق البحري المتطور هذا أناقة خالدة بقصته الكلاسيكية ولونه الأزرق البحري الغني. مصنوع من قطن البيكيه المميز الذي يوفر تهوية ممتازة ويحافظ على شكله غسلة بعد غسلة. يتميز بياقة وأكمام مضلعة مع فتحة ثلاثة أزرار. مثالي للأعمال غير الرسمية، وملابس نهاية الأسبوع، أو المناسبات الأنيقة غير الرسمية.',
                'seo_description_en' => 'Buy navy polo shirt with classic fit, premium pique cotton, and timeless style',
                'seo_description_ar' => 'اشترِ قميص بولو أزرق بحري بقصة كلاسيكية، قطن بيكيه مميز، وأسلوب خالد',
                'seo_keys_en' => 'navy polo,classic polo shirt,pique cotton,smart casual,business casual',
                'seo_keys_ar' => 'بولو أزرق بحري,قميص بولو كلاسيكي,قطن بيكيه,أنيق غير رسمي,عمل غير رسمي',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 165.00,
                'offer_price' => 139.00,
                'categories' => [2], // Polo
                'related_products' => ['POLO-TIPPING-DETAILS', 'CHECKERED-SHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 25,
            ],

            [
                'code' => 'SLIM-FIT-DARK-JEANS',
                'name_en' => 'Slim Fit Dark Jeans',
                'name_ar' => 'جينز داكن ضيق',
                'short_description_en' => 'Modern slim fit jeans in dark wash with comfortable stretch denim',
                'short_description_ar' => 'جينز ضيق عصري بغسلة داكنة مع دنيم مطاطي مريح',
                'long_description_en' => 'These modern slim fit jeans feature a sophisticated dark wash that works perfectly for both casual and smart casual occasions. Crafted from premium stretch denim that offers exceptional comfort and freedom of movement. The tailored fit flatters all body types while maintaining a contemporary silhouette. Features classic five-pocket styling with subtle fading details.',
                'long_description_ar' => 'يتميز هذا الجينز الضيق العصري بغسلة داكنة متطورة تعمل بشكل مثالي للمناسبات غير الرسمية والأنيقة غير الرسمية. مصنوع من دنيم مطاطي مميز يوفر راحة استثنائية وحرية الحركة. القصة المفصلة تناسب جميع أنواع الجسم مع الحفاظ على صورة ظلية معاصرة. يتميز بتصميم الجيوب الخمسة الكلاسيكي مع تفاصيل بهتان خفيفة.',
                'seo_description_en' => 'Shop slim fit dark jeans with stretch denim, modern styling, and comfortable fit',
                'seo_description_ar' => 'تسوق الجينز الداكن الضيق مع دنيم مطاطي، تصميم عصري، وملائم مريح',
                'seo_keys_en' => 'slim fit jeans,dark wash jeans,stretch denim,modern jeans,casual wear',
                'seo_keys_ar' => 'جينز ضيق,جينز غسلة داكنة,دنيم مطاطي,جينز عصري,ملابس كاجوال',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 220.00,
                'offer_price' => 185.00,
                'categories' => [3], // Jeans
                'related_products' => ['SKINNY-FIT-JEANS', 'CLASSIC-WHITE-TSHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 20,
            ],

            [
                'code' => 'DENIM-CASUAL-SHIRT',
                'name_en' => 'Denim Casual Shirt',
                'name_ar' => 'قميص دنيم كاجوال',
                'short_description_en' => 'Versatile denim shirt with classic Western styling and comfortable fit',
                'short_description_ar' => 'قميص دنيم متعدد الاستخدامات بتصميم غربي كلاسيكي وملائم مريح',
                'long_description_en' => 'This versatile denim shirt combines classic Western styling with modern comfort. Made from soft-washed denim that gets better with age. Features snap buttons, chest pockets with flaps, and a tailored fit that looks great worn open as a jacket or buttoned up as a shirt. The medium blue wash pairs perfectly with both light and dark bottoms.',
                'long_description_ar' => 'يجمع قميص الدنيم متعدد الاستخدامات هذا بين التصميم الغربي الكلاسيكي والراحة العصرية. مصنوع من دنيم مغسول ناعم يتحسن مع العمر. يتميز بأزرار كبس، وجيوب صدرية مع أغطية، وقصة مفصلة تبدو رائعة عند ارتدائها مفتوحة كجاكيت أو مغلقة كقميص. الغسلة الزرقاء المتوسطة تتناسب تماماً مع القيعان الفاتحة والداكنة.',
                'seo_description_en' => 'Get the versatile denim casual shirt with Western styling, soft-washed fabric, and tailored fit',
                'seo_description_ar' => 'احصل على قميص الدنيم الكاجوال متعدد الاستخدامات بتصميم غربي، قماش مغسول ناعم، وقصة مفصلة',
                'seo_keys_en' => 'denim shirt,casual shirt,western style,soft washed denim,versatile shirt',
                'seo_keys_ar' => 'قميص دنيم,قميص كاجوال,أسلوب غربي,دنيم مغسول ناعم,قميص متعدد الاستخدامات',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 195.00,
                'offer_price' => 165.00,
                'categories' => [4], // Shirts
                'related_products' => ['CHECKERED-SHIRT', 'SLIM-FIT-DARK-JEANS'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 18,
            ],

            [
                'code' => 'GRAPHIC-PRINT-TSHIRT',
                'name_en' => 'Graphic Print T-shirt',
                'name_ar' => 'تي شيرت بطباعة جرافيكية',
                'short_description_en' => 'Cool graphic print t-shirt with modern artwork and comfortable cotton blend',
                'short_description_ar' => 'تي شيرت بطباعة جرافيكية رائعة مع عمل فني عصري وخليط قطني مريح',
                'long_description_en' => 'Express your style with this eye-catching graphic print t-shirt featuring contemporary artwork. Made from a soft cotton blend that ensures comfort throughout the day. The high-quality screen printing technique ensures the design stays vibrant wash after wash. Perfect for casual outings, festivals, or whenever you want to make a statement.',
                'long_description_ar' => 'عبر عن أسلوبك مع هذا التي شيرت ذو الطباعة الجرافيكية الملفتة للنظر والذي يتميز بعمل فني معاصر. مصنوع من خليط قطني ناعم يضمن الراحة طوال اليوم. تقنية الطباعة الشاشية عالية الجودة تضمن بقاء التصميم نابضاً بالحياة غسلة بعد غسلة. مثالي للنزهات غير الرسمية، والمهرجانات، أو كلما أردت إدلاء بيان.',
                'seo_description_en' => 'Shop graphic print t-shirt with contemporary artwork, soft cotton blend, and vibrant colors',
                'seo_description_ar' => 'تسوق التي شيرت بالطباعة الجرافيكية مع عمل فني معاصر، خليط قطني ناعم، وألوان نابضة بالحياة',
                'seo_keys_en' => 'graphic tee,print t-shirt,cotton blend,contemporary art,casual wear',
                'seo_keys_ar' => 'تي شيرت جرافيكي,تي شيرت مطبوع,خليط قطني,فن معاصر,ملابس كاجوال',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 115.00,
                'offer_price' => 95.00,
                'categories' => [1], // T-shirts
                'related_products' => ['SLEEVE-STRIPED-TSHIRT', 'CLASSIC-WHITE-TSHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 35,
            ],

            [
                'code' => 'VINTAGE-WASH-JEANS',
                'name_en' => 'Vintage Wash Jeans',
                'name_ar' => 'جينز بغسلة عتيقة',
                'short_description_en' => 'Stylish vintage wash jeans with relaxed fit and authentic distressing',
                'short_description_ar' => 'جينز أنيق بغسلة عتيقة مع قصة مريحة وتآكل أصيل',
                'long_description_en' => 'These vintage-inspired jeans feature an authentic washed look with carefully crafted distressing details. The relaxed fit provides all-day comfort while the vintage wash gives them a lived-in feel. Made from durable cotton denim that ages beautifully. Perfect for creating effortless casual looks with a retro vibe.',
                'long_description_ar' => 'يتميز هذا الجينز المستوحى من الطراز العتيق بمظهر مغسول أصيل مع تفاصيل تآكل مصنوعة بعناية. القصة المريحة توفر راحة طوال اليوم بينما الغسلة العتيقة تعطيها شعوراً بالاستخدام. مصنوع من دنيم قطني متين يتقادم بشكل جميل. مثالي لإنشاء إطلالات كاجوال بسيطة مع جو ريترو.',
                'seo_description_en' => 'Buy vintage wash jeans with relaxed fit, authentic distressing, and timeless style',
                'seo_description_ar' => 'اشترِ جينز بغسلة عتيقة مع قصة مريحة، تآكل أصيل، وأسلوب خالد',
                'seo_keys_en' => 'vintage jeans,washed denim,relaxed fit,distressed jeans,retro style',
                'seo_keys_ar' => 'جينز عتيق,دنيم مغسول,قصة مريحة,جينز متآكل,أسلوب ريترو',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 210.00,
                'offer_price' => 175.00,
                'categories' => [3], // Jeans
                'related_products' => ['DENIM-CASUAL-SHIRT', 'GRAPHIC-PRINT-TSHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 22,
            ],

            [
                'code' => 'OXFORD-BUTTON-DOWN',
                'name_en' => 'Oxford Button Down Shirt',
                'name_ar' => 'قميص أوكسفورد بأزرار',
                'short_description_en' => 'Classic Oxford button-down shirt with premium cotton and timeless design',
                'short_description_ar' => 'قميص أوكسفورد كلاسيكي بأزرار مع قطن مميز وتصميم خالد',
                'long_description_en' => 'This timeless Oxford button-down shirt is a wardrobe essential crafted from premium cotton Oxford cloth. Features the classic button-down collar, chest pocket, and back box pleat for comfort and style. The crisp white color makes it versatile for both business and casual wear. Easy to care for and maintains its shape and color wash after wash.',
                'long_description_ar' => 'قميص الأوكسفورد الخالد هذا هو قطعة أساسية في الخزانة مصنوعة من قماش أوكسفورد القطني المميز. يتميز بالياقة الكلاسيكية ذات الأزرار، والجيب الصدري، والطية الخلفية المربعة للراحة والأناقة. اللون الأبيض الواضح يجعله متعدد الاستخدامات للأعمال والملابس غير الرسمية. سهل العناية ويحافظ على شكله ولونه غسلة بعد غسلة.',
                'seo_description_en' => 'Shop classic Oxford button-down shirt with premium cotton, versatile styling, and timeless appeal',
                'seo_description_ar' => 'تسوق قميص أوكسفورد الكلاسيكي بأزرار مع قطن مميز، تنسيق متعدد الاستخدامات، وجاذبية خالدة',
                'seo_keys_en' => 'oxford shirt,button down,cotton shirt,business casual,classic shirt',
                'seo_keys_ar' => 'قميص أوكسفورد,قميص بأزرار,قميص قطني,عمل غير رسمي,قميص كلاسيكي',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 175.00,
                'offer_price' => 145.00,
                'categories' => [4], // Shirts
                'related_products' => ['NAVY-POLO-SHIRT', 'SLIM-FIT-DARK-JEANS'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 28,
            ],

            [
                'code' => 'HENLEY-LONG-SLEEVE',
                'name_en' => 'Henley Long Sleeve Shirt',
                'name_ar' => 'قميص هينلي بأكمام طويلة',
                'short_description_en' => 'Comfortable henley shirt with long sleeves and soft cotton fabric',
                'short_description_ar' => 'قميص هينلي مريح بأكمام طويلة وقماش قطني ناعم',
                'long_description_en' => 'This comfortable henley shirt features a classic button placket and long sleeves perfect for layering or wearing alone. Made from soft, breathable cotton that feels great against the skin. The relaxed fit and versatile design make it suitable for lounging, casual outings, or as a base layer. Available in a rich navy color that pairs well with jeans or chinos.',
                'long_description_ar' => 'يتميز قميص الهينلي المريح هذا بفتحة أزرار كلاسيكية وأكمام طويلة مثالية للطبقات أو الارتداء منفرداً. مصنوع من قطن ناعم وقابل للتنفس يشعر بالراحة على البشرة. القصة المريحة والتصميم متعدد الاستخدامات يجعله مناسباً للاسترخاء، والنزهات غير الرسمية، أو كطبقة أساسية. متوفر بلون أزرق بحري غني يتناسب جيداً مع الجينز أو التشينو.',
                'seo_description_en' => 'Get the comfortable henley long sleeve shirt with soft cotton, relaxed fit, and versatile styling',
                'seo_description_ar' => 'احصل على قميص الهينلي المريح بأكمام طويلة مع قطن ناعم، قصة مريحة، وتنسيق متعدد الاستخدامات',
                'seo_keys_en' => 'henley shirt,long sleeve,cotton shirt,casual wear,layering piece',
                'seo_keys_ar' => 'قميص هينلي,أكمام طويلة,قميص قطني,ملابس كاجوال,قطعة طبقات',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 135.00,
                'offer_price' => 115.00,
                'categories' => [1], // T-shirts (long sleeve category)
                'related_products' => ['OXFORD-BUTTON-DOWN', 'VINTAGE-WASH-JEANS'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 30,
            ],

            [
                'code' => 'STRETCH-CHINO-PANTS',
                'name_en' => 'Stretch Chino Pants',
                'name_ar' => 'بنطلون تشينو مطاطي',
                'short_description_en' => 'Modern stretch chino pants with slim fit and versatile khaki color',
                'short_description_ar' => 'بنطلون تشينو مطاطي عصري بقصة ضيقة ولون كاكي متعدد الاستخدامات',
                'long_description_en' => 'These modern chino pants combine classic styling with contemporary comfort through stretch fabric technology. The slim fit flatters while allowing freedom of movement. Crafted from premium cotton-blend twill with added stretch for all-day comfort. The versatile khaki color works perfectly for both casual and smart casual occasions.',
                'long_description_ar' => 'يجمع بنطلون التشينو العصري هذا بين التصميم الكلاسيكي والراحة المعاصرة من خلال تقنية القماش المطاطي. القصة الضيقة تناسب مع السماح بحرية الحركة. مصنوع من تويل مخلوط قطني مميز مع إضافة مطاطية للراحة طوال اليوم. اللون الكاكي متعدد الاستخدامات يعمل تماماً للمناسبات غير الرسمية والأنيقة غير الرسمية.',
                'seo_description_en' => 'Shop stretch chino pants with slim fit, premium cotton blend, and versatile khaki color',
                'seo_description_ar' => 'تسوق بنطلون التشينو المطاطي بقصة ضيقة، خليط قطني مميز، ولون كاكي متعدد الاستخدامات',
                'seo_keys_en' => 'chino pants,stretch pants,slim fit,khaki pants,casual pants',
                'seo_keys_ar' => 'بنطلون تشينو,بنطلون مطاطي,قصة ضيقة,بنطلون كاكي,بنطلون كاجوال',
                'return_policy_en' => '30-day return policy. Product must be unworn with original tags attached.',
                'return_policy_ar' => 'سياسة إرجاع لمدة 30 يومًا. يجب أن يكون المنتج غير مستعمل مع العلامات الأصلية.',
                'price' => 185.00,
                'offer_price' => 155.00,
                'categories' => [3], // Jeans (pants category)
                'related_products' => ['OXFORD-BUTTON-DOWN', 'NAVY-POLO-SHIRT'],
                'accessories' => [],
                'type' => ProductTypeEnum::NEW_ARRIVAL,
                'stock' => 25,
            ],
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