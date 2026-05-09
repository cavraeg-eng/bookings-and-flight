<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

/**
 * Class Rights
 * @package Travelpayouts\components
 * @property-read bool $switch_themes
 * @property-read bool $edit_themes
 * @property-read bool $activate_plugins
 * @property-read bool $edit_plugins
 * @property-read bool $edit_users
 * @property-read bool $edit_files
 * @property-read bool $manage_options
 * @property-read bool $moderate_comments
 * @property-read bool $manage_categories
 * @property-read bool $manage_links
 * @property-read bool $upload_files
 * @property-read bool $import
 * @property-read bool $unfiltered_html
 * @property-read bool $edit_posts
 * @property-read bool $edit_others_posts
 * @property-read bool $edit_published_posts
 * @property-read bool $publish_posts
 * @property-read bool $edit_pages
 * @property-read bool $read
 * @property-read bool $edit_others_pages
 * @property-read bool $edit_published_pages
 * @property-read bool $publish_pages
 * @property-read bool $delete_pages
 * @property-read bool $delete_others_pages
 * @property-read bool $delete_published_pages
 * @property-read bool $delete_posts
 * @property-read bool $delete_others_posts
 * @property-read bool $delete_published_posts
 * @property-read bool $delete_private_posts
 * @property-read bool $edit_private_posts
 * @property-read bool $read_private_posts
 * @property-read bool $delete_private_pages
 * @property-read bool $edit_private_pages
 * @property-read bool $read_private_pages
 * @property-read bool $delete_users
 * @property-read bool $create_users
 * @property-read bool $unfiltered_upload
 * @property-read bool $edit_dashboard
 * @property-read bool $update_plugins
 * @property-read bool $delete_plugins
 * @property-read bool $install_plugins
 * @property-read bool $update_themes
 * @property-read bool $install_themes
 * @property-read bool $update_core
 * @property-read bool $list_users
 * @property-read bool $remove_users
 * @property-read bool $promote_users
 * @property-read bool $edit_theme_options
 * @property-read bool $delete_themes
 * @property-read bool $export
 * @property-read bool $manage_woocommerce
 * @property-read bool $view_woocommerce_reports
 * @property-read bool $edit_product
 * @property-read bool $read_product
 * @property-read bool $delete_product
 * @property-read bool $edit_products
 * @property-read bool $edit_others_products
 * @property-read bool $publish_products
 * @property-read bool $read_private_products
 * @property-read bool $delete_products
 * @property-read bool $delete_private_products
 * @property-read bool $delete_published_products
 * @property-read bool $delete_others_products
 * @property-read bool $edit_private_products
 * @property-read bool $edit_published_products
 * @property-read bool $manage_product_terms
 * @property-read bool $edit_product_terms
 * @property-read bool $delete_product_terms
 * @property-read bool $assign_product_terms
 * @property-read bool $edit_shop_order
 * @property-read bool $read_shop_order
 * @property-read bool $delete_shop_order
 * @property-read bool $edit_shop_orders
 * @property-read bool $edit_others_shop_orders
 * @property-read bool $publish_shop_orders
 * @property-read bool $read_private_shop_orders
 * @property-read bool $delete_shop_orders
 * @property-read bool $delete_private_shop_orders
 * @property-read bool $delete_published_shop_orders
 * @property-read bool $delete_others_shop_orders
 * @property-read bool $edit_private_shop_orders
 * @property-read bool $edit_published_shop_orders
 * @property-read bool $manage_shop_order_terms
 * @property-read bool $edit_shop_order_terms
 * @property-read bool $delete_shop_order_terms
 * @property-read bool $assign_shop_order_terms
 * @property-read bool $edit_shop_coupon
 * @property-read bool $read_shop_coupon
 * @property-read bool $delete_shop_coupon
 * @property-read bool $edit_shop_coupons
 * @property-read bool $edit_others_shop_coupons
 * @property-read bool $publish_shop_coupons
 * @property-read bool $read_private_shop_coupons
 * @property-read bool $delete_shop_coupons
 * @property-read bool $delete_private_shop_coupons
 * @property-read bool $delete_published_shop_coupons
 * @property-read bool $delete_others_shop_coupons
 * @property-read bool $edit_private_shop_coupons
 * @property-read bool $edit_published_shop_coupons
 * @property-read bool $manage_shop_coupon_terms
 * @property-read bool $edit_shop_coupon_terms
 * @property-read bool $delete_shop_coupon_terms
 * @property-read bool $assign_shop_coupon_terms
 * @property-read bool $manage_everest_forms
 * @property-read bool $edit_everest_form
 * @property-read bool $read_everest_form
 * @property-read bool $delete_everest_form
 * @property-read bool $edit_everest_forms
 * @property-read bool $edit_others_everest_forms
 * @property-read bool $publish_everest_forms
 * @property-read bool $read_private_everest_forms
 * @property-read bool $delete_everest_forms
 * @property-read bool $delete_private_everest_forms
 * @property-read bool $delete_published_everest_forms
 * @property-read bool $delete_others_everest_forms
 * @property-read bool $edit_private_everest_forms
 * @property-read bool $edit_published_everest_forms
 * @property-read bool $manage_everest_form_terms
 * @property-read bool $edit_everest_form_terms
 * @property-read bool $delete_everest_form_terms
 * @property-read bool $assign_everest_form_terms
 * @property-read bool $loco_admin
 * @property-read bool $administrator
 */
class Rights extends BaseObject
{
    protected $switch_themes = false;
    protected $edit_themes = false;
    protected $activate_plugins = false;
    protected $edit_plugins = false;
    protected $edit_users = false;
    protected $edit_files = false;
    protected $manage_options = false;
    protected $moderate_comments = false;
    protected $manage_categories = false;
    protected $manage_links = false;
    protected $upload_files = false;
    protected $import = false;
    protected $unfiltered_html = false;
    protected $edit_posts = false;
    protected $edit_others_posts = false;
    protected $edit_published_posts = false;
    protected $publish_posts = false;
    protected $edit_pages = false;
    protected $read = false;
    protected $edit_others_pages = false;
    protected $edit_published_pages = false;
    protected $publish_pages = false;
    protected $delete_pages = false;
    protected $delete_others_pages = false;
    protected $delete_published_pages = false;
    protected $delete_posts = false;
    protected $delete_others_posts = false;
    protected $delete_published_posts = false;
    protected $delete_private_posts = false;
    protected $edit_private_posts = false;
    protected $read_private_posts = false;
    protected $delete_private_pages = false;
    protected $edit_private_pages = false;
    protected $read_private_pages = false;
    protected $delete_users = false;
    protected $create_users = false;
    protected $unfiltered_upload = false;
    protected $edit_dashboard = false;
    protected $update_plugins = false;
    protected $delete_plugins = false;
    protected $install_plugins = false;
    protected $update_themes = false;
    protected $install_themes = false;
    protected $update_core = false;
    protected $list_users = false;
    protected $remove_users = false;
    protected $promote_users = false;
    protected $edit_theme_options = false;
    protected $delete_themes = false;
    protected $export = false;
    protected $manage_woocommerce = false;
    protected $view_woocommerce_reports = false;
    protected $edit_product = false;
    protected $read_product = false;
    protected $delete_product = false;
    protected $edit_products = false;
    protected $edit_others_products = false;
    protected $publish_products = false;
    protected $read_private_products = false;
    protected $delete_products = false;
    protected $delete_private_products = false;
    protected $delete_published_products = false;
    protected $delete_others_products = false;
    protected $edit_private_products = false;
    protected $edit_published_products = false;
    protected $manage_product_terms = false;
    protected $edit_product_terms = false;
    protected $delete_product_terms = false;
    protected $assign_product_terms = false;
    protected $edit_shop_order = false;
    protected $read_shop_order = false;
    protected $delete_shop_order = false;
    protected $edit_shop_orders = false;
    protected $edit_others_shop_orders = false;
    protected $publish_shop_orders = false;
    protected $read_private_shop_orders = false;
    protected $delete_shop_orders = false;
    protected $delete_private_shop_orders = false;
    protected $delete_published_shop_orders = false;
    protected $delete_others_shop_orders = false;
    protected $edit_private_shop_orders = false;
    protected $edit_published_shop_orders = false;
    protected $manage_shop_order_terms = false;
    protected $edit_shop_order_terms = false;
    protected $delete_shop_order_terms = false;
    protected $assign_shop_order_terms = false;
    protected $edit_shop_coupon = false;
    protected $read_shop_coupon = false;
    protected $delete_shop_coupon = false;
    protected $edit_shop_coupons = false;
    protected $edit_others_shop_coupons = false;
    protected $publish_shop_coupons = false;
    protected $read_private_shop_coupons = false;
    protected $delete_shop_coupons = false;
    protected $delete_private_shop_coupons = false;
    protected $delete_published_shop_coupons = false;
    protected $delete_others_shop_coupons = false;
    protected $edit_private_shop_coupons = false;
    protected $edit_published_shop_coupons = false;
    protected $manage_shop_coupon_terms = false;
    protected $edit_shop_coupon_terms = false;
    protected $delete_shop_coupon_terms = false;
    protected $assign_shop_coupon_terms = false;
    protected $manage_everest_forms = false;
    protected $edit_everest_form = false;
    protected $read_everest_form = false;
    protected $delete_everest_form = false;
    protected $edit_everest_forms = false;
    protected $edit_others_everest_forms = false;
    protected $publish_everest_forms = false;
    protected $read_private_everest_forms = false;
    protected $delete_everest_forms = false;
    protected $delete_private_everest_forms = false;
    protected $delete_published_everest_forms = false;
    protected $delete_others_everest_forms = false;
    protected $edit_private_everest_forms = false;
    protected $edit_published_everest_forms = false;
    protected $manage_everest_form_terms = false;
    protected $edit_everest_form_terms = false;
    protected $delete_everest_form_terms = false;
    protected $assign_everest_form_terms = false;
    protected $loco_admin = false;
    protected $administrator = false;

    protected $_isReady = false;

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            if (!$this->_isReady) {
                if ($currentUser = wp_get_current_user()) {
                    self::configure($this, $currentUser->get_role_caps());
                    $this->_isReady = true;
                } else {
                    throw new \Exception('You want to get rights property is to early. Rights module is not ready yet');
                }
            }
            return $this->$name;
        }
        return null;
    }
}
