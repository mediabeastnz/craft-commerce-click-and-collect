<?php
/**
 * Click And Collect plugin for Craft CMS 3.x
 *
 * Click And Collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect\controllers;

use mediabeastnz\ClickAndCollect\ClickAndCollect;
use mediabeastnz\ClickAndCollect\models\StoreModel;
use craft\commerce\models\Address;
use craft\commerce\Plugin as Commerce;

use Craft;
use craft\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
 * @author    Myles Derham
 * @package   ClickAndCollect
 */
class BaseController extends Controller
{

    // Protected Properties
    // =========================================================================

    protected $allowAnonymous = [];

    
    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $stores = ClickAndCollect::$plugin->clickAndCollectService->getAllStores();
        return $this->renderTemplate('click-and-collect/index', compact('stores'));
    }

    public function actionSettings()
    {
        $this->requireAdmin();

        $settings = ClickAndCollect::$plugin->getSettings();

        return $this->renderTemplate('click-and-collect/settings', array(
            'title' => 'Settings',
            'settings' => $settings,
        ));
    }


    /**
     * @param int|null $id
     * @param Store|null $store
     * @return Response
     * @throws HttpException
     */
    public function actionEdit(int $id = null, StoreModel $store = null): Response
    {
        $variables = compact('id', 'store');

        // edit
        if ($variables['id']) {
            $store = ClickAndCollect::$plugin->clickAndCollectService->getStoreById($variables['id']);
            $variables['store'] = Commerce::getInstance()->getAddresses()->getAddressById($store->addressId);

            if (!$variables['store']) {
                throw new HttpException(404);
            }

        } else {
            $variables['store'] = new Address();
        }

        $this->_populateVariables($variables);
        return $this->renderTemplate('click-and-collect/_edit', $variables);
    }


    /**
     * @param $variables
     * @throws \yii\base\InvalidConfigException
     */
    private function _populateVariables(&$variables)
    {
        /** @var Store $store */
        $store = $variables['store'];

        if ($store->id) {
            $variables['title'] = $store->title;
        } else {
            $variables['title'] = Craft::t('commerce', 'Create a new store');
        }

    }


    public function actionSave()
    {

        $address = new Address();

        if($addressId = Craft::$app->getRequest()->getParam('addressId')) {
            $address->id = $addressId;
        }

        // Shared attributes
        $attributes = [
            'title',
            'address1',
            'address2',
            'city',
            'zipCode',
            'countryId',
            'stateValue',
            'phone'
        ];
        foreach ($attributes as $attr) {
            if($address->$attr == 'firstName' || $address->$attr == 'lastName') {
                $address->$attr = "-";
            }
            $address->$attr = Craft::$app->getRequest()->getParam($attr);
        }

        $address->isStoreLocation = true;
        if (Commerce::getInstance()->getAddresses()->saveAddress($address, false)) {

            // save actual store record;
            $store = new StoreModel();

            if($id = Craft::$app->getRequest()->getParam('id')) {
                $store->id = $id;
            }    

            $store->addressId = $address->id;
            $store->title = $address->title;

            if (ClickAndCollect::$plugin->clickAndCollectService->saveStore($store)) {
                Craft::$app->getSession()->setNotice(Craft::t('commerce', 'Store saved.'));
                return $this->redirectToPostedUrl();
            }
        }

        Craft::$app->getSession()->setError(Craft::t('commerce', 'Couldn’t save Store.'));

        return $this->redirectToPostedUrl();

    }


    /**
     * @throws HttpException
     */
    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $id = Craft::$app->getRequest()->getRequiredBodyParam('id');
        $store = ClickAndCollect::$plugin->clickAndCollectService->getStoreById($id);
        // delete related addresses
        if($store) {
            Commerce::getInstance()->getAddresses()->deleteAddressById($store->addressId);
        }

        // delete store
        ClickAndCollect::$plugin->clickAndCollectService->deleteStoreById($id);
        
        return $this->asJson(['success' => true]);
    }

}
