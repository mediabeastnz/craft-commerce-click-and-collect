<?php
/**
 * Click And Collect plugin for Craft CMS 3.x
 *
 * Click And Collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect\web\twig;

use mediabeastnz\ClickAndCollect\ClickAndCollect;
use mediabeastnz\ClickAndCollect\records\StoreRecord;

use Craft;
use yii\db\ActiveQuery;

class ClickAndCollectVariable
{

    public $clickandcollect;

    public function init()
    {
        parent::init();

        // Point `craft.clickandcollect` to the mediabeastnz\ClickAndCollect\ClickAndCollect instance
        $this->clickandcollect = ClickAndCollect::$plugin->getInstance();
    }

    /**
     * Returns a new Query instance.
     *
     * @param mixed $criteria
     * @return Query
     */
    public function stores($criteria = null): ActiveQuery
    {
        $query = StoreRecord::find();
            
        if ($criteria) {
            Craft::configure($query, $criteria);
        }

        return $query;
    }


    public function settings() 
    {
        return ClickAndCollect::$plugin->getInstance()->getSettings();
    }

}
