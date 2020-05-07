<?php
/**
 * DryCalendar plugin for Craft CMS 3.x
 *
 * Event calendar with unrestricted repetition
 *
 * @link      http://drycalendar.blogspot.com/
 * @copyright Copyright (c) 2018 KR37
 */

namespace kr37\drycalendar;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\services\Fields;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

use kr37\drycalendar\models\Settings;
use kr37\drycalendar\variables\DryCalendarVariable;
use kr37\drycalendar\fields\CalendarOccurrences;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    KR37
 * @package   DryCalendar
 * @since     3.0.0-alpha+20180429
 *
 */
class DryCalendar extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * DryCalendar::$plugin
     *
     * @var DryCalendar
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '0.0.15338.2';
    public $hasCpSection = true;
    public $hasCpSettings = true;

    // Constants
    // =========================================================================
    const CALENDAR_TABLE = 'craft_drycalendar';
    const VIEWS_TABLE    = 'craft_drycalendar_views'; 

    // Public Methods
    // =========================================================================

    protected function createSettingsModel()
    {
        return new \kr37\drycalendar\models\Settings();
    }

    public function settingsHtml() 
    {
       return \Craft::$app->getView()->renderTemplate('DryCalendar/settings', [
           'settings' => $this->getSettings()
       ]);
    }

    public function getVariableDefinition()
    {
        return new DryCalendarVariable();
    }

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * DryCalendar::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

	// Register the Calendar Occurrences ajax field
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = CalendarOccurrences::class;
        });

        // Register our services
        $this->setComponents([
            'services' => services\MainService::class,
        ]);

        // Register our variables
        Event::on(
            CraftVariable::class, 
            CraftVariable::EVENT_INIT, 
            function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('DryCalendar', DryCalendarVariable::class);
             }
        );

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'drycalendar',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
