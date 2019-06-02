<?php
/**
 * Click And collect plugin for Craft CMS 3.x
 *
 * Click And collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect\services;

use mediabeastnz\ClickAndCollect\ClickAndCollect;
use mediabeastnz\ClickAndCollect\models\StoreModel;
use mediabeastnz\ClickAndCollect\records\StoreRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;
use yii\base\Exception;


/**
 * @property Store[] $allStores an array of all stores
 * @property array $allStoresAsList
 * @author    Myles Derham
 * @package   ClickAndCollect
 */
class ClickAndCollectService extends Component
{

    // Properties
    // =========================================================================

    /**
     * @var bool
     */
    private $_fetchedAllStores = false;

    /**
     * @var Store[]
     */
    private $_storesById = [];

    /**
     * @var Store[]
     */
    private $_storesOrderedByName = [];

    
    // Public Methods
    // =========================================================================

    /**
     * Returns a store by its ID.
     *
     * @param int $id the store's ID
     * @return Store|null
     */
    public function getStoreById(int $id)
    {
        if (isset($this->_storesById[$id])) {
            return $this->_storesById[$id];
        }

        if ($this->_fetchedAllStores) {
            return null;
        }

        $result = $this->_createStoresQuery()
            ->where(['id' => $id])
            ->one();

        if (!$result) {
            return null;
        }

        return $this->_storesById[$id] = new StoreModel($result);
    }

    /**
     * Returns an array of all stores.
     *
     * @return Store[] An array of all stores.
     */
    public function getAllStores(): array
    {
        if (!$this->_fetchedAllStores) {
            $results = $this->_createStoresQuery()->all();

            foreach ($results as $row) {
                $store = new StoreModel($row);
                $this->_storesById[$row['id']] = $store;
                $this->_storesOrderedByName[] = $store;
            }

            $this->_fetchedAllStores;
        }

        return $this->_storesOrderedByName;
    }

    /**
     * Returns an array of all stores.
     *
     * @return Store[] An array of all stores.
     */
    public function getAllStoresForField(): array
    {
        $results = $this->_createStoresQuery()->all();
        return $results;
    }


    public function saveStore(StoreModel $model, bool $runValidation = true): bool
    {
        if ($model->id) {
            $record = StoreRecord::findOne($model->id);

            if (!$record) {
                throw new Exception(Craft::t('commerce', 'No store exists with the ID “{id}”',
                    ['id' => $model->id]));
            }
        } else {
            $record = new StoreRecord();
        }

        if ($runValidation && !$model->validate()) {
            Craft::info('Store not saved due to validation error.', __METHOD__);

            return false;
        }

        $record->title = $model->title;
        $record->addressId = $model->addressId;

        // Save it!
        $record->save(false);

        // Now that we have a record ID, save it on the model
        $model->id = $record->id;

        Craft::$app->getSession()->setNotice(Craft::t('click-and-collect', 'Store saved.'));

        return true;
    }


    /**
     * Deletes a store by its ID.
     *
     * @param int $id the store's ID
     * @return bool whether the store was deleted successfully
     */
    public function deleteStoreById(int $id): bool
    {
        $record = StoreRecord::findOne($id);

        if ($record) {
            return (bool)$record->delete();
        }

        return false;
    }


    // Private methods
    // =========================================================================
    /**
     * Returns a Query object prepped for retrieving Stores.
     *
     * @return Query The query object.
     */
    private function _createStoresQuery(): Query
    {
        return (new Query())
            ->from(['{{%commerce_stores}} stores'])
            ->orderBy(['title' => SORT_ASC]);
    }


}
