<?php
/**
 * Click And Collect plugin for Craft CMS 3.x
 *
 * Click And Collect Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\ClickAndCollect\migrations;

use mediabeastnz\ClickAndCollect\ClickAndCollect;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Myles Derham
 * @package   ClickAndCollect
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%commerce_stores}}')) {
            $this->createTables();
            $this->createIndexes();
            $this->addForeignKeys();
        }
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->removeTables();
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%commerce_stores}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%commerce_stores}}',
                [
                    'id' => $this->primaryKey(),
                    'addressId' => $this->integer()->notNull(),
                    'title' => $this->string()->notNull()->defaultValue(''),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }


    /**
     * @return void
     */
    public function createIndexes()
    {
        //$this->createIndex(null, '{{%commerce_stores}}', ['someId'], false);
    }

    
    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(null, '{{%commerce_stores}}', ['addressId'], '{{%commerce_addresses}}', ['id'], 'CASCADE', null);
    }


    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%commerce_stores}}');
    }
}
