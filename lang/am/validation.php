<?php

declare(strict_types=1);

return [
    'required' => ':attribute መሞላት ያስፈልጋል።',
    'email' => ':attribute ትክክለኛ ኢሜይል መሆን አለበት።',
    'max' => [
        'string' => ':attribute ከ:max ቁምፊዎች መብለጥ አይችልም።',
        'file' => ':attribute ከ:max KB መብለጥ አይችልም።',
    ],
    'min' => [
        'string' => ':attribute ቢያንስ :min ቁምፊዎች ሊኖሩት ይገባል።',
        'numeric' => ':attribute ቢያንስ :min መሆን አለበት።',
    ],
    'unique' => ':attribute አስቀድሞ ተይዟል።',
    'confirmed' => 'የ:attribute ማረጋገጫ አይዛመድም።',
    'image' => ':attribute ምስል መሆን አለበት።',
    'mimes' => ':attribute ከእነዚህ ዓይነቶች መሆን አለበት: :values.',
    'array' => ':attribute ዝርዝር መሆን አለበት።',
    'exists' => 'የተመረጠው :attribute ልክ አይደለም።',
    'attributes' => [
        'name' => 'ስም',
        'email' => 'ኢሜይል',
        'password' => 'የይለፍ ቃል',
        'role' => 'ሚና',
        'permission' => 'ፈቃድ',
        'system_name' => 'የስርዓት ስም',
        'company_email' => 'የድርጅት ኢሜይል',
        'system_logo' => 'የስርዓት ሎጎ',
        'system_favicon' => 'የስርዓት ፋቪኮን',
    ],
];