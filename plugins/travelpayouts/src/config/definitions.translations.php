<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

use Travelpayouts\components\Translator;

$localeList = [
    Translator::BULGARIAN => [
        'localeName' => 'Bulgarian',
        'code_min' => Translator::BULGARIAN,
        'code' => 'bg_BG',
    ],
    Translator::CHINESE_SIMPLIFIED => [
        'localeName' => 'Chinese Simplified',
        'code_min' => Translator::CHINESE_SIMPLIFIED,
        'code' => 'zh_CN',
    ],
    Translator::CHINESE_TRADITIONAL => [
        'localeName' => 'Chinese Traditional',
        'code_min' => Translator::CHINESE_TRADITIONAL,
        'code' => 'zh_TW',
    ],
    Translator::CZECH => [
        'localeName' => 'Czech',
        'code_min' => Translator::CZECH,
        'code' => 'cs_CZ',
    ],
    Translator::DANISH => [
        'localeName' => 'Danish',
        'code_min' => Translator::DANISH,
        'code' => 'da_DK',
    ],
    Translator::DUTCH => [
        'localeName' => 'Dutch',
        'code_min' => Translator::DUTCH,
        'code' => 'nl_NL',
    ],
    Translator::ENGLISH => [
        'localeName' => 'English',
        'code_min' => Translator::ENGLISH,
        'code' => 'en_US',
    ],
    Translator::FINNISH => [
        'localeName' => 'Finnish',
        'code_min' => Translator::FINNISH,
        'code' => 'fi_FI',
    ],
    Translator::FRENCH => [
        'localeName' => 'French',
        'code_min' => Translator::FRENCH,
        'code' => 'fr_FR',
    ],
    Translator::GEORGIAN => [
        'localeName' => 'Georgian',
        'code_min' => Translator::GEORGIAN,
        'code' => 'ka_GE',
    ],
    Translator::GERMAN => [
        'localeName' => 'German',
        'code_min' => Translator::GERMAN,
        'code' => 'de_DE',
    ],
    Translator::GREEK => [
        'localeName' => 'Greek',
        'code_min' => Translator::GREEK,
        'code' => 'el_GR',
    ],
    Translator::HEBREW => [
        'localeName' => 'Hebrew',
        'code_min' => Translator::HEBREW,
        'code' => 'he_IL',
    ],
    Translator::HUNGARIAN => [
        'localeName' => 'Hungarian',
        'code_min' => Translator::HUNGARIAN,
        'code' => 'hu_HU',
    ],
    Translator::ITALIAN => [
        'localeName' => 'Italian',
        'code_min' => Translator::ITALIAN,
        'code' => 'it_IT',
    ],
    Translator::JAPANESE => [
        'localeName' => 'Japanese',
        'code_min' => Translator::JAPANESE,
        'code' => 'ja_JP',
    ],
    Translator::KOREAN => [
        'localeName' => 'Korean',
        'code_min' => Translator::KOREAN,
        'code' => 'ko_KR',
    ],
    Translator::LATVIAN => [
        'localeName' => 'Latvian',
        'code_min' => Translator::LATVIAN,
        'code' => 'lv_LV',
    ],
    Translator::LITHUANIAN => [
        'localeName' => 'Lithuanian',
        'code_min' => Translator::LITHUANIAN,
        'code' => 'lt_LT',
    ],
    Translator::MALAY => [
        'localeName' => 'Malay',
        'code_min' => Translator::MALAY,
        'code' => 'ms_MY',
    ],
    Translator::NORWEGIAN => [
        'localeName' => 'Norwegian',
        'code_min' => Translator::NORWEGIAN,
        'code' => 'no_NO',
    ],
    Translator::POLISH => [
        'localeName' => 'Polish',
        'code_min' => Translator::POLISH,
        'code' => 'pl_PL',
    ],
    Translator::PORTUGUESE => [
        'localeName' => 'Portuguese',
        'code_min' => Translator::PORTUGUESE,
        'code' => 'pt_PT',
    ],
    Translator::PORTUGUESE_BRAZILIAN => [
        'localeName' => 'Portuguese, Brazilian',
        'code_min' => Translator::PORTUGUESE_BRAZILIAN,
        'code' => 'pt_BR',
    ],
    Translator::ROMANIAN => [
        'localeName' => 'Romanian',
        'code_min' => Translator::ROMANIAN,
        'code' => 'ro_RO',
    ],
    Translator::RUSSIAN => [
        'localeName' => 'Russian',
        'code_min' => Translator::RUSSIAN,
        'code' => 'ru_RU',
    ],
    Translator::SERBIAN_LATIN => [
        'localeName' => 'Serbian (Latin)',
        'code_min' => Translator::SERBIAN_LATIN,
        'code' => 'sr_CS',
    ],
    Translator::SLOVAK => [
        'localeName' => 'Slovak',
        'code_min' => Translator::SLOVAK,
        'code' => 'sk_SK',
    ],
    Translator::SLOVENIAN => [
        'localeName' => 'Slovenian',
        'code_min' => Translator::SLOVENIAN,
        'code' => 'sl_SI',
    ],
    Translator::SPANISH => [
        'localeName' => 'Spanish',
        'code_min' => Translator::SPANISH,
        'code' => 'es_ES',
    ],
    Translator::SWEDISH => [
        'localeName' => 'Swedish',
        'code_min' => Translator::SWEDISH,
        'code' => 'sv_SE',
    ],
    Translator::TURKISH => [
        'localeName' => 'Turkish',
        'code_min' => Translator::TURKISH,
        'code' => 'tr_TR',
    ],
    Translator::UKRAINIAN => [
        'localeName' => 'Ukrainian',
        'code_min' => Translator::UKRAINIAN,
        'code' => 'uk_UA',
    ],
    Translator::VIETNAMESE => [
        'localeName' => 'Vietnamese',
        'code_min' => Translator::VIETNAMESE,
        'code' => 'vi_VN',
    ],
    // Fallback locales
    Translator::TAJIK => [
        'localeName' => 'Tajik',
        'code_min' => Translator::TAJIK,
        'code' => 'tg_TJ',
        'fallback' => Translator::RUSSIAN,
    ],
    Translator::CATALAN => [
        'localeName' => 'Catalan',
        'code_min' => Translator::CATALAN,
        'code' => 'ca_ES',
        'fallback' => Translator::SPANISH,
    ],
    Translator::BOSNIAN => [
        'localeName' => 'Bosnian',
        'code_min' => Translator::BOSNIAN,
        'code' => 'bs_BA',
        'fallback' => Translator::CROATIAN,
    ],
    Translator::CHECHEN => [
        'localeName' => 'Chechen',
        'code_min' => Translator::CHECHEN,
        'code' => 'ce_RU',
        'fallback' => Translator::RUSSIAN,
    ],
    Translator::KAZAKH => [
        'localeName' => 'Kazakh',
        'code_min' => Translator::KAZAKH,
        'code' => 'kk_KZ',
        'fallback' => Translator::RUSSIAN,
    ],
    Translator::UZBEK => [
        'localeName' => 'Uzbek',
        'code_min' => Translator::UZBEK,
        'code' => 'uz_UZ',
        'fallback' => Translator::RUSSIAN,
    ],
    Translator::MONTENEGRIN => [
        'localeName' => 'Montenegrin (Latin)',
        'code_min' => Translator::MONTENEGRIN,
        'code' => 'sr_ME',
        'fallback' => Translator::SERBIAN_LATIN,
    ],
    Translator::THAI => [
        'localeName' => 'Thai',
        'code_min' => Translator::THAI,
        'code' => 'th_TH',
        'fallback' => Translator::ENGLISH,
    ],
];

$availableLocaleNames = [
    Translator::CZECH,
    Translator::DANISH,
    Translator::GERMAN,
    Translator::GREEK,
    Translator::ENGLISH,
    Translator::SPANISH,
    Translator::FINNISH,
    Translator::FRENCH,
    Translator::HEBREW,
    Translator::HUNGARIAN,
    Translator::ITALIAN,
    Translator::JAPANESE,
    Translator::KOREAN,
    Translator::DUTCH,
    Translator::NORWEGIAN,
    Translator::POLISH,
    Translator::PORTUGUESE_BRAZILIAN,
    Translator::PORTUGUESE,
    Translator::ROMANIAN,
    Translator::RUSSIAN,
    Translator::SWEDISH,
    Translator::TURKISH,
    Translator::UKRAINIAN,
    Translator::VIETNAMESE,
    //Translator::BELARUSIAN,
    Translator::GEORGIAN,
    //Translator::LATVIAN,
    //Translator::LITHUANIAN,
    //Translator::SLOVENIAN,
    Translator::SLOVAK,
    Translator::BULGARIAN,
    Translator::CHINESE_SIMPLIFIED,
    Translator::CHINESE_TRADITIONAL,
    Translator::MALAY,
    Translator::SERBIAN_LATIN,

    // Fallback locales
    Translator::TAJIK,
    Translator::CATALAN,
    Translator::BOSNIAN,
    Translator::CHECHEN,
    Translator::KAZAKH,
    Translator::UZBEK,
    Translator::MONTENEGRIN,
    Translator::THAI,
];

$supportedLocales = [];

foreach ($localeList as $localeCode => $locale) {
    // Добавляем только те локали, которые есть в списке доступных
    if (in_array($locale['code_min'], $availableLocaleNames, true)) {
        $supportedLocales[$localeCode] = $locale;
    }
}
asort($supportedLocales);

return [
    'supportedLocales' => $supportedLocales,
];