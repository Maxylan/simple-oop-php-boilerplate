<? 
/**
 * Configures the application using stored values, definitions and
 * references to other scrtipts.
 *
 * @author          Max Olsson <max@newseed.se>
 * @link            https://newseed.se
 * @since           1.0.0a
 * @package         Package
 * @license         GPL-2.0+
 * @license         http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if called directly. Security meassure.
defined('ABSPATH') || exit;

/**
 * Flag to indicate config-initialization is underway.
 * 
 * @since   1.0.0a
 */
define('CONFIG_INITIALIZATION',  true);

// Require additional files.
require_once ABSPATH . PREFIX . 'database' . CLASS_SUFFIX;
require_once ABSPATH . PREFIX . 'routing' . CLASS_SUFFIX;

/**
 * The core of the web application.
 * 
 * Configures the application using stored values, definitions and
 * references to other scrtipts.
 *
 * @author          Max Olsson <max@newseed.se>
 * @link            https://newseed.se
 * @since           1.0.0a
 * @package         Package
 * @license         GPL-2.0+
 * @license         http://www.gnu.org/licenses/gpl-2.0.txt
 */
class ApplicationConfiguration {

    /**
     * Welcome developer!
     * 
     * This is where you'll enter all of the details to your
     * database connection!
     * 
     * Your application will then use this to establish a
     * connection to the database whenever you construct
     * a DatabaseConnection object and all of the details
     * entered below (except pswd) will be available 
     * in the rest of the application by calling referencing
     * $app->db();
     * 
     * @since   1.0.0a
     */
    private array $database_details = array(
        'database_name'      => '',
        'database_address'   => '',
        'database_collation' => '',
        'database_user'      => '',
        'database_password'  => '',
    ); 

    /**
     * The Application's name.
     * @since   1.0.0a
     */
    public readonly string $name;

    /**
     * The Application's description.
     * @since   1.0.0a
     */
    public readonly string $description;

    /**
     * The Application's author.
     * @since   1.0.0a
     */
    public readonly string $author;

    /**
     * The Application-author's website.
     * @since   1.0.0a
     */
    public readonly string $author_uri;

    /**
     * The Application's version.
     * @since   1.0.0a
     */
    public readonly string $version;

    /**
     * The Application's license.
     * @since   1.0.0a
     */
    public readonly string $license;

    /**
     * The License website.
     * @since   1.0.0a
     */
    public readonly string $license_uri;

    /**
     * The Routing Engine of the Application.
     * @since   1.0.0a
     */
    private static readonly Routing $routes;

    /**
     * Effectively starts execution of the web app.
     * @since   1.0.0a
     */
    function __construct() {

        /**== Load definitions into memory ==**/
        $this->name         =   APP_NAME;
        $this->description  =   APP_DESCRIPTION;
        $this->author       =   APP_AUTHOR;
        $this->author_uri   =   APP_AUTHOR_URI;
        $this->version      =   APP_VERSION;
        $this->license      =   APP_LICENSE;
        $this->license_uri  =   APP_LICENSE_URI;
        /**==                              ==**/

        global $app;
        if ( isset($app) || defined('CONFIGURED') ) throw $this->gracefully('Attempt to initialize Duplicate Instances of ApplicationConfiguration detected and forcefully stopped.');

        ApplicationConfiguration::$routes = new Routing();

    }

    /**
     * Returns an Exception with the message of your choice formatted
     * in an easily "debuggable" way.
     * 
     * @since   1.0.0a
     */
    public function gracefully( string $message, int $code = 0 ) {
        throw new Exception( sprintf((('("%s %s" %s) ') . (($code) ? 'Code '.$code.': ' : '') . ('%s')), 
            $this->name,
            $this->version,
            date("d/m - H:i:s"),
            $message
        ), $code);
    }

    /**
     * Establishes a connection to the database and returns it to the
     * caller in the form of a DatabaseConnection object.
     * 
     * @param   bool    $connect    Connect automatically to the database
     *                              during construction of the object.
     * @since   1.0.0a
     */
    public function db( bool $connect = true ) {
        return new DatabaseConnection( $this->database_details, $connect );
    }

    /**
     * Gets instance of the class managing routing.
     * @since   1.0.0a
     */
    public function routes() {
        return ApplicationConfiguration::$routes;
    }
    
}

$app = new ApplicationConfiguration();