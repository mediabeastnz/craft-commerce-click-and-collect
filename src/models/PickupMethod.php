<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace mediabeastnz\ClickAndCollect\models;

use Craft;
use craft\commerce\base\Model;
use craft\commerce\base\ShippingMethodInterface;
use craft\commerce\elements\Order;
use craft\commerce\errors\NotImplementedException;

/**
 * Base ShippingMethod
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class PickupMethod extends Model implements ShippingMethodInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string Name
     */
    public $name;

    /**
     * @var string Handle
     */
    public $handle;

    /**
     * @var bool Enabled
     */
    public $enabled;

    /**
     * @var float Price
     */
    public $price;

    /**
     * @var bool Is this the shipping method for the lite edition.
     */
    public $isLite = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return "Click And Collect";
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @inheritdoc
     */
    public function getCpEditUrl(): string
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritdoc
     */
    public function getShippingRules(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getIsEnabled(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function matchOrder(Order $order): bool
    {
        // /** @var ShippingRuleInterface $rule */
        // foreach ($this->getShippingRules() as $rule) {
        //     if ($rule->matchOrder($order)) {
        //         return true;
        //     }
        // }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getMatchingShippingRule(Order $order)
    {
        // foreach ($this->getShippingRules() as $rule) {
        //     /** @var ShippingRuleInterface $rule */
        //     if ($rule->matchOrder($order)) {
        //         return $rule;
        //     }
        // }

        return null;
    }

    /**
     * @param Order $order
     * @return float
     */
    public function getPriceForOrder(Order $order)
    {
        return $this->price;
    }

}
