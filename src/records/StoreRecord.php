<?php
/**
 * Click And Collect plugin for Craft CMS 3.x
 *
 * Click And Collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect\records;

use mediabeastnz\ClickAndCollect\ClickAndCollect;

use Craft;
use craft\db\ActiveRecord;
use craft\commerce\Plugin as Commerce;

/**
 * @author    Myles Derham
 * @package   ClickAndCollect
 */
class StoreRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%commerce_stores}}';
    }

    public function getAddress()
    {
        return Commerce::getInstance()->getAddresses()->getAddressById($this->addressId);
    }
}
