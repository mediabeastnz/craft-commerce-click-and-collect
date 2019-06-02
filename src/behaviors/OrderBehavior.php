<?php
/**
 * Click And Collect plugin for Craft CMS 3.x
 *
 * Click And Collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect\behaviors;

use mediabeastnz\ClickAndCollect\ClickAndCollect;

use Craft;
use yii\base\Behavior;

class OrderBehavior extends Behavior
{

    public $shippingAddressId;

    public $storeFieldHandle;

    public function setSelectedStore($selectedStore) {
        $this->owner->setFieldValue(ClickAndCollect::$plugin->getSettings()->storeFieldHandle, $selectedStore);
    }

    public function updateShippingAddress($selectedStore = null) {
        $this->owner->shippingAddressId = null;
        // TODO: bypass Commerce\elements\Order validation and save Store's address as the orders shipping address
        // if ($selectedStore) {
        //     $store = ClickAndCollect::$plugin->clickAndCollectService->getStoreById($selectedStore);
        //     if ($store) {
        //         $this->owner->shippingAddressId = $store->addressId;
        //     }
        // }
    }

    public function getSelectedStore() {
        $this->owner->getFieldValue(ClickAndCollect::$plugin->getSettings()->storeFieldHandle);
    }

    public function save() {
        Craft::$app->getElements()->saveElement($this->owner);
    }
    
}