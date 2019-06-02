<?php
/**
 * Click And Collect plugin for Craft CMS 3.x
 *
 * Click And Collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect\fields;

use mediabeastnz\ClickAndCollect\ClickAndCollect;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\fields\data\OptionData;
use craft\fields\Dropdown;

class StoresField extends Dropdown
{
    // Properties
    // =========================================================================


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('commerce', 'Click & Collect Stores');
    }

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        if (!($element instanceof craft\commerce\elements\Order)) {
            return '<span style="color: #da5a47">' . Craft::t('click-and-collect', 'Click & Collect Stores field is for CMS use only.') . '</span>';
        }

        if ($element->shippingMethodHandle != ClickAndCollect::$plugin->getSettings()->shippingMethodHandle) {
            return '<span style="color: #da5a47">' . Craft::t('click-and-collect', 'Orders shipping method is') . ' <strong>' . $element->getShippingMethod()->name . '</strong> ' . Craft::t('click-and-collect', "which isn't applicable to this field.") . '</span>';
        }

        $stores = ClickAndCollect::$plugin->clickAndCollectService->getAllStoresForField();
        $options = [];
        if ($stores) {
            foreach ($stores as $option) {
                $options[] = [
                    'label' => $option['title'],
                    'value' => $option['id']
                ];
            }
        } else {
            return '<span style="color: #da5a47">' . Craft::t('click-and-collect', 'No stores exists or existing store has since been removed.') . '</span>';
        }

        return Craft::$app->getView()->renderTemplate('_includes/forms/select', [
            'name' => $this->handle,
            'value' => $value,
            'options' => $options,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        // Get all of the acceptable values
        $range = [];
        $stores = ClickAndCollect::$plugin->clickAndCollectService->getAllStoresForField();

        if ($stores) {
            foreach ($stores as $option) {
                $range[] = $option['id'];
            }

            return [
                ['in', 'range' => $range, 'allowArray' => true],
            ];
        }

        // can't really validate if there are no stores...
        return [];
    }


    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
    }

}
