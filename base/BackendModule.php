<?php

namespace smart\base;

use Yii;

/**
 * Backend CMS module
 * 
 * Every backend CMS modules supports:
 * - prepare database with [[database]] function
 * - making menu for CMS backend with [[menu]] function
 * - prepare roles and permissions with [[security]] function
 */
class BackendModule extends BaseModule
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        static::database();
        static::security();
    }

    /**
     * Prepare database
     * 
     * The database schema is placed in [[/moduleName/schema]] directory
     * Filename of the schema is complies to [[yii\db\Connection::driverName]] with `.sql` extension
     * 
     * @return void
     */
    protected static function database()
    {
        $db = Yii::$app->db;

        $filename = static::getDirname() . '/schema/' . $db->driverName . '.sql';
        $content = @file_get_contents($filename);
        if ($content === false) {
            return;
        }

        foreach (explode(';', $content) as $s) {
            if (trim($s) !== '') {
                $db->createCommand($s)->execute();
            }
        }
    }

    /**
     * Prepare roles and permissions
     * @return void
     */
    protected static function security()
    {
    }

    /**
     * Making module menu for CMS
     * @param array &$items CMS menu items
     * @return void
     */
    protected function menu(&$items)
    {
    }

}
