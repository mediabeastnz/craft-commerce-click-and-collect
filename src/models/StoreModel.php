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
use craft\commerce\Plugin as Commerce;

/**
 * @author    Myles Derham
 * @package   ClickAndCollect
 */
class StoreModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $id;

    public $addressId;

    public $title;
    
    public $uid;

    public $dateCreated;

    public $dateUpdated;

    
    // Static Methods
    // =========================================================================
    public static function hasStatuses(): bool
    {
        return true;
    }


    // Public Methods
    // =========================================================================

    public function getAddress()
    {
        return Commerce::getInstance()->getAddresses()->getAddressById($this->addressId);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'addressId'], 'number', 'integerOnly' => true],
            [['title', 'addressId'], 'required']
        ];
    }


    // Protected Methods
    // =========================================================================

    


}
