<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\snowplow;

class TrackerDummy extends Tracker
{
    /**
     * @inheritdoc
     */
    public function trackPageView($page_url, $page_title = null, $referrer = null, $context = null, $tstamp = null)
    {
        $this->log('trackPageView', [
            'page_url' => $page_url,
            'page_title' => $page_title,
            'referrer' => $referrer,
            'context' => $this->mergeContext($context),
            'tstamp' => $tstamp,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function trackStructEvent($category,
                                     $action,
                                     $label = null,
                                     $property = null,
                                     $value = null,
                                     $context = [],
                                     $tstamp = null)
    {
        $this->log('trackStructEvent', [
            'category' => $category,
            'action' => $action,
            'label' => $label,
            'property' => $property,
            'value' => $value,
            'context' => $this->mergeContext($context),
            'tstamp' => $tstamp,
        ]);
        exit;
    }

    /**
     * @inheritdoc
     */
    public function trackUnstructEvent($event_json,
                                       $context = [],
                                       $tstamp = null)
    {
        $this->log('trackUnstructEvent', [
            'event_json' => $event_json,
            'context' => $this->mergeContext($context),
            'tstamp' => $tstamp,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function trackEcommerceTransaction($order_id,
                                              $total_value,
                                              $currency = null,
                                              $affiliation = null,
                                              $tax_value = null,
                                              $shipping = null,
                                              $city = null,
                                              $state = null,
                                              $country = null,
                                              $items,
                                              $context = [],
                                              $tstamp = null)
    {
        $this->log('trackEcommerceTransaction', [
            'order_id' => $order_id,
            'total_value' => $total_value,
            'currency' => $currency,
            'affiliation' => $affiliation,
            'tax_value' => $tax_value,
            'shipping' => $shipping,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'items' => $items,
            'context' => $this->mergeContext($context),
            'tstamp' => $tstamp,
        ]);
    }

    /**
     * @param $methodName
     * @param mixed $params
     */
    protected function log($methodName, $params)
    {
        print_r([
            'trackerInstance'=> get_class($this),
            'methodName' => $methodName,
            'params'=> $params,
        ]);
    }


}
