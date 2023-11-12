<?php

namespace kr37\drycalendar\migrations;

use Craft;
use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{

    public function safeUp(): bool
    {
        // Place installation code here...
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Create drycalendar table
        $tableName = $this->db->tablePrefix . 'drycalendar';
        if (!Craft::$app->getDb()->tableExists($tableName)) {
            //$this->createTable('{{%drycalendar}}', [
            $this->createTable($tableName, [
                'id'         => $this->primaryKey(),
                'event_id'   => $this->Integer(),
                'dateYmd'    => $this->String(),
                'timestr'    => $this->String(),
                'alt_text'   => $this->String(),
                'css_class'  => $this->String(),
                'url'        => $this->String(),
                'streamed'   => $this->String(),
                'userJson'   => $this->String()
            ], $tableOptions);
            $this->createIndex(null, $tableName, ['event_id', 'dateYmd'], false);
        }

        // Create drycalendar_subsets table
        $tableName = $this->db->tablePrefix . 'drycalendar_subsets';
        if (!Craft::$app->getDb()->tableExists($tableName)) {
            $this->createTable($tableName, [
                'id'                  => $this->primaryKey(),
                'handle'              => $this->String(),
                'title'               => $this->String(),
                'categoriesToInclude' => $this->String(),
                'categoriesToExclude' => $this->String()
            ], $tableOptions);
            $this->createIndex(null, $tableName, ['handle'], true); // true for unique
        }

        // Create drycalendar_views table
        $tableName = $this->db->tablePrefix . 'drycalendar_views';
        if (!Craft::$app->getDb()->tableExists($tableName)) {
            $this->createTable($tableName, [
                'id'           => $this->primaryKey(),
                'subsetId'     => $this->String(),
                'startDateYmd' => $this->String(),
                'endDateYmd'   => $this->String(),
                'htmlBefore'   => $this->String(),
                'htmlAfter'    => $this->String()
            ], $tableOptions);
            $this->createIndex(null, $tableName, ['subsetId', 'startDateYmd'], false);
        }

        return true;
    }


    public function safeDown(): bool
    {
        // Place uninstallation code here...

        // Drop tables
        $this->dropTable($this->db->tablePrefix . 'drycalendar');
        $this->dropTable($this->db->tablePrefix . 'drycalendar_subsets');
        $this->dropTable($this->db->tablePrefix . 'drycalendar_views');
        return true;
    }
}
