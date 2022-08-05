<?php 
// Exit if called directly. Security meassure.
defined('CONFIG_INITIALIZATION') || exit;

/**
 * Configures a Database Connection and defines methods for interacting
 * with the database.
 *
 * @author          Max Olsson <max@newseed.se>
 * @link            https://newseed.se
 * @since           1.0.0a
 * @package         Package
 * @license         GPL-2.0+
 * @license         http://www.gnu.org/licenses/gpl-2.0.txt
 */
class DatabaseConnection {

    /**
     * The Database's name.
     * @since   1.0.0a
     */
    public string $db_name;

    /**
     * The Database's IP-Address.
     * @since   1.0.0a
     */
    public string $db_address;

    /**
     * The Collation used by the Database.
     * @since   1.0.0a
     */
    public string $db_collation;

    /**
     * The DB user to be used by the Application.
     * @since   1.0.0a
     */
    public string $db_user;

    /**
     * The Password of the DB user to be used by 
     * the Application.
     * @since   1.0.0a
     * @access  private
     */
    private string $db_password;

    /**
     * The prepared query.
     * @since   1.0.0a
     * @access  private
     */
    private string $db_query = '';


    /**
     * Indicates if there's an active connection to
     * the database.
     * @since   1.0.0a
     */
    public static bool $Connected;


    /**
     * Constructs the database connection.
     * @since   1.0.0a
     */
    function __construct( array $details, bool $connect = true ) {

        DatabaseConnection::$Connected = false;

        /**==   Store Database Details    ==**/
        $this->db_name      =   $details['database_name'];
        $this->db_address   =   $details['database_address'];
        $this->db_collation =   $details['database_collation'];
        $this->db_user      =   $details['database_user'];
        $this->db_password  =   $details['database_password'];
        /**==                             ==**/

        if ($connect) {
            $this->connect();
        }

    }

    /**
     * Destructs the database connection.
     * @since   1.0.0a
     */
    function __destruct() {
        
        /**== Flush Database Details ==**/
        unset($this->db_name);
        unset($this->db_address);
        unset($this->db_collation);
        unset($this->db_user);
        unset($this->db_password);
        unset($this->db_query);
        /**==                        ==**/

        $this->drop_connection();
    }

    /**
     * Establishes a connection to the database.
     * @since   1.0.0a
     */
    public function connect() {
        if (DatabaseConnection::$Connected) return;

        // Establish connection here.

        DatabaseConnection::$Connected = true;
    }

    /**
     * Drops a connection to the database.
     * @since   1.0.0a
     */
    public function drop_connection() {
        if (!DatabaseConnection::$Connected) return;

        // Drop connection here.

        DatabaseConnection::$Connected = false;
    }

    
    /**
     * Creates/Prepares an SQL-Query
     * 
     * @param   string  $query  The query. Using "{}" in your query
     *                          will replace it with one of the 
     *                          additional parameters passed to the
     *                          function (used in order passed).
     * @since   1.0.0a
     * @return  DatabaseConnection
     */
    public function query( string $query, ...$params ) {
        global $app;
        if ($this->db_query) throw $app->gracefully('DatabaseConnection does not support preparation/execution of multiple queries at once. Create a new instance of DatabaseConnection after the first has been run.');
        if (!$this->query) return;

        if ( count($params) ) {
            $pieces = explode('{}', $query, count($params));
            $this->db_query = $pieces[0];
            for( $i = 0; $i < count($params); $i++ ) {
                $this->db_query .= strval($params[$i]) . $pieces[$i + 1];
            }
        }
        else {
            $this->db_query = $query;
        }

        // Return instance so user can continue chaining methods.
        return $this;

    }

    
    /**
     * Executes a prepared SQL-Query
     * 
     * @param   string  $query  The query. Use "{<index>}" in your
     *                          query where you want to add a param.
     *                          For Example: '{0}' represents the
     *                          first entry in the array $params.
     * @since   1.0.0a
     */
    public function execute() {
        global $app;
        if (!$this->db_query) throw $app->gracefully('Caught attempt to execute a query when there\'s no query prepared.');
        if (!DatabaseConnection::$Connected) throw $app->gracefully('Caught attempt to execute a query without being connected to a database.');
        
        // Execute query.

        // Drop connection.
        $this->drop_connection();

        // Return results.
        return $this;

    }

}