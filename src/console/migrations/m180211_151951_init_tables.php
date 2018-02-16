<?php

namespace bulldozer\catalog\console\migrations;

use bulldozer\App;
use bulldozer\users\rbac\DbManager;
use yii\base\InvalidConfigException;
use yii\db\Migration;

/**
 * Class m180211_151951_init_tables
 */
class m180211_151951_init_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%catalog_currencies}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(11)->unsigned(),
            'updated_at' => $this->integer(11)->unsigned(),
            'creator_id' => $this->integer(11)->unsigned(),
            'updater_id' => $this->integer(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'code' => $this->string(255)->notNull(),
            'short_name' => $this->string(10),
        ], $tableOptions);

        $this->batchInsert('{{%catalog_currencies}}', ['name', 'code', 'short_name', 'created_at', 'updated_at'], [
            ['Рубли', 'RUB', 'руб', time(), time()],
        ]);

        $this->createTable('{{%catalog_discounts}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(11)->unsigned()->notNull(),
            'price_id' => $this->integer(11)->unsigned()->notNull(),
            'value' => $this->decimal(10, 2)->unsigned(),
        ], $tableOptions);

        $this->createIndex('idx_product_price', '{{%catalog_discounts}}', ['product_id', 'price_id'], true);

        $this->createTable('{{%catalog_prices}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(11)->unsigned(),
            'updated_at' => $this->integer(11)->unsigned(),
            'creator_id' => $this->integer(11)->unsigned(),
            'updater_id' => $this->integer(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'base' => $this->boolean()->defaultValue(0),
            'currency_id' => $this->integer(11)->unsigned()->notNull(),
            'active' => $this->boolean()->defaultValue(1),
        ], $tableOptions);

        $this->insert('{{%catalog_prices}}', [
            'name' => 'Базовая',
            'base' => true,
            'currency_id' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->createIndex('idx_active', '{{%catalog_prices}}', 'active');

        $this->createTable('{{%catalog_products}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(11)->unsigned(),
            'updated_at' => $this->integer(11)->unsigned(),
            'creator_id' => $this->integer(11)->unsigned(),
            'updater_id' => $this->integer(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(500)->notNull(),
            'section_id' => $this->integer(11)->notNull()->unsigned(),
            'description' => $this->text(),
            'sort' => $this->integer(11)->unsigned()->defaultValue(100),
            'active' => $this->boolean()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('{{%catalog_product_images}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(11)->unsigned()->notNull(),
            'file_id' => $this->integer(11)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_product_id', '{{%catalog_product_images}}', 'product_id');

        $this->createTable('{{%catalog_product_lists}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(11)->unsigned(),
            'updated_at' => $this->integer(11)->unsigned(),
            'creator_id' => $this->integer(11)->unsigned(),
            'updater_id' => $this->integer(11)->unsigned(),
            'active' => $this->boolean()->defaultValue(1),
            'name' => $this->string(255)->notNull(),
            'more_url' => $this->string(600),
            'products' => $this->text(),
        ], $tableOptions);

        $this->createIndex('idx_active', '{{%catalog_product_lists}}', 'active');

        $this->createTable('{{%catalog_product_prices}}', [
            'id' => $this->primaryKey(),
            'price_id' => $this->integer(11)->unsigned()->notNull(),
            'product_id' => $this->integer(11)->unsigned()->notNull(),
            'value' => $this->decimal(10, 2),
        ], $tableOptions);

        $this->createIndex('idx_product_prices', '{{%catalog_product_prices}}', ['price_id', 'product_id']);

        $this->createTable('{{%catalog_product_property_values}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(11)->unsigned()->notNull(),
            'property_id' => $this->integer(11)->unsigned()->notNull(),
            'value' => $this->text(),
        ], $tableOptions);

        $this->createIndex('idx_product_id', '{{%catalog_product_property_values}}', 'product_id');

        $this->createTable('{{%catalog_properties}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(11)->unsigned(),
            'updated_at' => $this->integer(11)->unsigned(),
            'creator_id' => $this->integer(11)->unsigned(),
            'updater_id' => $this->integer(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'type' => $this->smallInteger(2)->unsigned()->notNull(),
            'multiple' => $this->boolean()->defaultValue(0),
            'sort' => $this->integer(11)->unsigned()->defaultValue(100),
            'group_id' => $this->integer(11)->unsigned(),
            'filtered' => $this->boolean()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx_sort', '{{%catalog_properties}}', 'sort');
        $this->createIndex('idx_filtered', '{{%catalog_properties}}', 'filtered');

        $this->createTable('{{%catalog_properties_enum}}', [
            'id' => $this->primaryKey(),
            'property_id' => $this->integer(11)->unsigned()->notNull(),
            'value' => $this->string(500)->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_property_id', '{{%catalog_properties_enum}}', 'property_id');

        $this->createTable('{{%catalog_property_groups}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(11)->unsigned(),
            'updated_at' => $this->integer(11)->unsigned(),
            'creator_id' => $this->integer(11)->unsigned(),
            'updater_id' => $this->integer(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'sort' => $this->integer(11)->unsigned()->defaultValue(100),
        ], $tableOptions);

        $this->createIndex('idx_sort', '{{%catalog_property_groups}}', 'sort');

        $this->createTable('{{%catalog_sections}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer(11)->unsigned(),
            'updated_at' => $this->integer(11)->unsigned(),
            'creator_id' => $this->integer(11)->unsigned(),
            'updater_id' => $this->integer(11)->unsigned(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(500)->notNull(),
            'left' => $this->integer(11)->unsigned(),
            'right' => $this->integer(11)->unsigned(),
            'depth' => $this->integer(11)->unsigned(),
            'tree' => $this->integer(11)->unsigned()->defaultValue(0),
            'sort' => $this->integer(11)->unsigned()->defaultValue(100),
            'image_id' => $this->integer(11)->unsigned(),
            'active' => $this->boolean()->defaultValue(1),
            'watermark_id' => $this->integer(11)->unsigned(),
            'watermark_position' => $this->smallInteger(3)->unsigned(),
            'watermark_transparency' => $this->smallInteger(3)->unsigned(),
        ], $tableOptions);

        $this->createTable('{{%catalog_section_properties}}', [
            'id' => $this->primaryKey(),
            'section_id' => $this->integer(11)->unsigned()->notNull(),
            'property_id' => $this->integer(11)->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_section_property', '{{%catalog_section_properties}}', ['section_id', 'property_id'], true);
        $this->createIndex('idx_section', '{{%catalog_section_properties}}', 'section_id');

        $authManager = $this->getAuthManager();

        $manageCatalog = $authManager->createPermission('catalog_manage');
        $manageCatalog->name = 'Управление каталогом';
        $authManager->add($manageCatalog);

        $admin = $authManager->getRole('admin');
        $authManager->addChild($admin, $manageCatalog);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%catalog_currencies}}');
        $this->dropTable('{{%catalog_discounts}}');
        $this->dropTable('{{%catalog_prices}}');
        $this->dropTable('{{%catalog_products}}');
        $this->dropTable('{{%catalog_product_images}}');
        $this->dropTable('{{%catalog_product_lists}}');
        $this->dropTable('{{%catalog_product_prices}}');
        $this->dropTable('{{%catalog_product_property_values}}');
        $this->dropTable('{{%catalog_properties}}');
        $this->dropTable('{{%catalog_properties_enum}}');
        $this->dropTable('{{%catalog_property_groups}}');
        $this->dropTable('{{%catalog_sections}}');
        $this->dropTable('{{%catalog_section_properties}}');

        $authManager = $this->getAuthManager();

        $managePages = $authManager->getPermission('catalog_manage');
        $authManager->remove($managePages);
    }

    /**
     * @throws InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = App::$app->getAuthManager();

        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        return $authManager;
    }
}
