<?php

namespace Travelpayouts\components;

/**
 * Class Menu
 * @package Travelpayouts\src\components
 */
class Menu
{
    private $_page;
    private $_slug;
    private $_page_title;
    private $_menu_title;

    /**
     * Menu constructor.
     * @param $page
     * @param $slug
     * @param $page_title
     * @param $menu_title
     */
    public function __construct($page, $slug, $page_title, $menu_title)
    {
        $this->_page = $page;
        $this->_slug = $slug;
        $this->_page_title = $page_title;
        $this->_menu_title = $menu_title;
    }

    public function add_page()
    {
        add_options_page(
            $this->_page_title,
            $this->_menu_title,
            'manage_options',
            $this->_slug,
            [$this->_page, 'render']
        );
    }
}
