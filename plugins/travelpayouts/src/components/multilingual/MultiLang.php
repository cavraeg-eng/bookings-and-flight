<?php

namespace Travelpayouts\components\multilingual;

class MultiLang
{
    public $data;

    public function cacheKey()
    {
        if (!empty($this->data)) {
            return md5(json_encode($this->data));
        }

        return '';
    }

    protected function polylangData()
    {
        if (
            function_exists('pll_languages_list') &&
            function_exists('pll_current_language') &&
            function_exists('pll_default_language')
        ) {
            return [
                'languagesList' => pll_languages_list(),
                'current' => pll_current_language(),
                'default' => pll_default_language(),
            ];
        }

        return null;
    }

    protected function wpmlData()
    {
        if (function_exists('icl_get_languages')) {
            global $sitepress;

            $languagesList = [];
            $languages = icl_get_languages('skip_missing=1');
            foreach ($languages as $language) {
                $languagesList[] = $language['code'];
            }

            return [
                'languagesList' => $languagesList,
                'current' => $sitepress->get_current_language(),
                'default' => $sitepress->get_default_language(),
            ];
        }

        return null;
    }

    /**
     * Получает необходимые данные из плагинов мультиязычности, если такие есть
     *
     * @return array|null
     */
    public function multiLangData()
    {
        $polylangData = $this->polylangData();
        if (!empty($polylangData)) {
            return $polylangData;
        }

        $wpmlData = $this->wpmlData();
        if (!empty($wpmlData)) {
            return $wpmlData;
        }

        return null;
    }
}
