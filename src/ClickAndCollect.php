<?php
/**
 * Click And Collect plugin for Craft CMS 3.x
 *
 * Click And Collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect;

use mediabeastnz\ClickAndCollect\services\ClickAndCollectService;
use mediabeastnz\ClickAndCollect\models\Settings;
use mediabeastnz\ClickAndCollect\models\PickupMethod;
use mediabeastnz\ClickAndCollect\behaviors\OrderBehavior;
use mediabeastnz\ClickAndCollect\web\twig\ClickAndCollectVariable;
use mediabeastnz\ClickAndCollect\fields\StoresField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\helpers\UrlHelper;
use craft\events\RegisterUrlRulesEvent;
use craft\commerce\events\RegisterAvailableShippingMethodsEvent;
use craft\commerce\services\ShippingMethods;
use craft\elements\db\ElementQuery;
use craft\events\PopulateElementEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class ClickAndCollect
 *
 * @author    Myles Derham
 * @package   ClickAndCollect
 *
 * @property  ClickAndCollectService $clickAndCollectService
 */
class ClickAndCollect extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var ClickAndCollect
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    public $hasCpSettings = true;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'clickAndCollectService' => ClickAndCollectService::class,
        ]);

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['click-and-collect'] = 'click-and-collect/base/index';
            $event->rules['click-and-collect/settings'] = 'click-and-collect/base/settings';
            $event->rules['click-and-collect/stores/new'] = 'click-and-collect/base/edit';
            $event->rules['click-and-collect/stores/<id:\d+>'] = 'click-and-collect/base/edit';

        });

        Event::on(ShippingMethods::class, ShippingMethods::EVENT_REGISTER_AVAILABLE_SHIPPING_METHODS, function(RegisterAvailableShippingMethodsEvent $event) {
            $event->shippingMethods[] = new PickupMethod([
                'name' => $this->getSettings()->shippingMethodName,
                'handle' => $this->getSettings()->shippingMethodHandle,
                'price' => 0.00,
                'enabled' => $this->getSettings()->shippingMethodEnabled
            ]);
        });

        Event::on(ElementQuery::class, ElementQuery::EVENT_AFTER_POPULATE_ELEMENT, function(PopulateElementEvent $event) {
            $element = $event->element;
            if (get_class($element) == 'craft\commerce\elements\Order' && $element->shippingMethodHandle == $this->getSettings()->shippingMethodHandle) {
                $request = Craft::$app->getRequest();
                $element->attachBehavior('OrderBehavior', OrderBehavior::className());
                $element->updateShippingAddress();
                // check for request of updated selected store
                if($selectedStore = $request->getParam($this->getSettings()->storeFieldHandle)){
                    $element->setSelectedStore($selectedStore);
                }
                $element->save();
            } 
        });

        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = StoresField::class;
        });

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('clickandcollect', ClickAndCollectVariable::class);
        });

        Craft::info(
            Craft::t(
                'click-and-collect',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

    }

    public function getPluginName()
    {
        return Craft::t('click-and-collect', $this->getSettings()->pluginName);
    }

    public function getCpNavItem()
    {
        $navItem = parent::getCpNavItem();
        $navItem['label'] = $this->getPluginName();

        // $navItem['subnav']['dashboard'] = [
        //     'label' => Craft::t('app', 'Dashboard'),
        //     'url' => 'click-and-collect'
        // ];

        $navItem['subnav']['stores'] = [
            'label' => Craft::t('app', 'Stores'),
            'url' => 'click-and-collect'
        ];

        $navItem['subnav']['settings'] = [
            'label' => Craft::t('app', 'Settings'),
            'url' => 'click-and-collect/settings'
        ];

        return $navItem;
    }

    public function getSettingsResponse()
    {
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('click-and-collect/settings'));
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'click-and-collect/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
