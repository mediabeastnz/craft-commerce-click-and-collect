<?php
/**
 * Click And Collect plugin for Craft CMS 3.x
 *
 * Click And Collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect\models;

use mediabeastnz\ClickAndCollect\ClickAndCollect;

use Craft;
use craft\base\Model;

/**
 * @author    Myles Derham
 * @package   ClickAndCollect
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $pluginName = 'Click & Collect';

    public $shippingMethodEnabled = 1;

    public $shippingMethodName = 'Pickup in-store';

    public $shippingMethodHandle = 'pickup-in-store';

    public $storeFieldHandle = 'selectedCommerceStore';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pluginName','shippingMethodName', 'shippingMethodHandle', 'storeFieldHandle'], 'string'],
            [['pluginName','shippingMethodName','shippingMethodHandle', 'storeFieldHandle'], 'required']
        ];
    }
}
