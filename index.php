<? 
/**
 * PHP Application Boilerplate. Replace this text with whatever you wish.
 *
 * @author            Max Olsson <max@newseed.se>
 * @link              https://newseed.se
 * @since             1.0.0a
 * @package           Package
 *
 * Application Name:    An application name.
 * Plugin URI:          https://newseed.se
 * Description:         An application description.
 * Version:             1.0.0a
 * Author:              NewSeed IT-Solutions
 * Author URI:          https://newseed.se
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * The Application's name.
 * 
 * @since   1.0.0a
 */
define('APP_NAME',  'An application name.');

/**
 * The Application's description.
 * 
 * @since   1.0.0a
 */
define('APP_DESCRIPTION',  'An application description.');

/**
 * The Application's author.
 * 
 * @since   1.0.0a
 */
define('APP_AUTHOR',  'NewSeed IT-Solutions');

/**
 * The Application-author's website.
 * 
 * @since   1.0.0a
 */
define('APP_AUTHOR_URI',  'https://newseed.se');

/**
 * The Application's version.
 * 
 * @since   1.0.0a
 */
define('APP_VERSION',  '1.0.0a');

/**
 * The Application's license.
 * 
 * @since   1.0.0a
 */
define('APP_LICENSE',  'GPL-2.0+');

/**
 * The License website.
 * 
 * @since   1.0.0a
 */
define('APP_LICENSE_URI',  'http://www.gnu.org/licenses/gpl-2.0.txt');

/**
 * The Application's parent directory.
 * 
 * @since   1.0.0a
 */
define('ABSPATH',  __DIR__);

/**
 * The prefix of root classes. Not used anywhere else.
 * 
 * @since   1.0.0a
 */
define('PREFIX',  'ns-');

/**
 * The suffix of PHP files (.php)
 * 
 * @since   1.0.0a
 */
define('SUFFIX',  '.php');

/**
 * The suffix of PHP class-files (.class.php)
 * 
 * @since   1.0.0a
 */
define('CLASS_SUFFIX',  '.class.php');

/**
 * The Application's content directory.
 * 
 * @since   1.0.0a
 */
define('CONTENT',  ABSPATH . '/content');

/*== Custom Constants ==*/
/**
 * Add custom constants here to effect the application in different ways.
 * One example constant has been added below and it's "DEBUG_ENABLED".
 * For now, there's only one other constant you can add and it's
 * "DEBUG_SHOW_ERRORS", but expect more to come if I continue developing
 * this boilerplate.
 * @since   1.0.0a
 */

define('DEBUG_ENABLED', true);
define('DEBUG_SHOW_ERRORS', true);

/*==                  ==*/

/**
 * The global variable acting as an entrypoint into the application.
 * @since   1.0.0a
 */
global $app;
$app = null;

try {
    require_once ABSPATH . PREFIX . 'configuration' . CLASS_SUFFIX;
    $app = new ApplicationConfiguration();
} catch (Exception $e) {
    error_log('Error occured during configuration and the process has been terminated.');
    $app->display_exception($e);
}

/**
 * The Application's ready-state.
 * 
 * @since   1.0.0a
 */
define('CONFIGURED',  true);

try {
    /*== Application Entry Point ==*/
    // Add your code here!

    // Add the route "/test"
    $app->routes()->add('/test', 'test.php');

    // Last thing you should do is call $app->routes()->direct();
    // This "Directs" the user to the proper page based on the 
    // incomming request. (Simply loads a file with "require".)
    /*==                         ==*/
    $app->routes()->direct();
} catch (Exception $e) {
    error_log('Error occured during execution and the process has been terminated.');
    $app->display_exception($e);
}
