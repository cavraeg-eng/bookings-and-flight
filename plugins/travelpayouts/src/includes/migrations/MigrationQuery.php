<?php

namespace Travelpayouts\includes\migrations;

use Travelpayouts\components\db\Query;

/**
 * Class TablesMigration
 * @package Travelpayouts\includes\migrations
 */
class MigrationQuery extends Query
{
    const TABLE_SEARCH_FORM = 'tp_search_shortcodes';
    const TABLE_LINKS = 'tp_auto_replac_links';

    /**
     * @return array|object|null
     */
    public function getSearchForms()
    {
        if ($this->tableExists(self::TABLE_SEARCH_FORM)) {
            return $this->select(
                [
                    'id',
                    'title',
                    // TODO search_form and other from const
                    'CASE WHEN type_shortcode = \'0\' THEN \'search_form\' ELSE \'other\' END as type_shortcode',
                    'code_form',
                    'from_city',
                    'to_city',
                    'hotel_city',
                    'slug',
                    'date_add',
                ],
                self::TABLE_SEARCH_FORM
            );
        }

        return null;
    }

    public function getSearchFormsCount()
    {
        return $this->count(self::TABLE_SEARCH_FORM);
    }

    /**
     * @return array|object|null
     */
    public function getLinksForms()
    {
        if ($this->tableExists(self::TABLE_LINKS)) {
            return $this->select(
                [
                    'arl_url',
                    'arl_anchor',
                    'arl_event',
                    'arl_nofollow',
                    'CASE WHEN arl_nofollow = 0 THEN 0 ELSE \'on\' END as arl_nofollow',
                    'CASE WHEN arl_replace = 0 THEN 0 ELSE \'on\' END as arl_replace',
                    'CASE WHEN arl_target_blank = 0 THEN 0 ELSE \'on\' END as arl_target_blank',
                ],
                self::TABLE_LINKS
            );
        }

        return null;
    }
}


