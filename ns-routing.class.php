<?php 
// Exit if called directly. Security meassure.
defined('CONFIG_INITIALIZATION') || exit;

/**
 * Handles routing using CRUD operations.
 *
 * @author          Max Olsson <max@newseed.se>
 * @link            https://newseed.se
 * @since           1.0.0a
 * @package         Package
 * @license         GPL-2.0+
 * @license         http://www.gnu.org/licenses/gpl-2.0.txt
 */
class Routing {

    /**
     * All stored routes.
     * 
     * A route is stored as array(
     *      'destination'   => '',
     *      'file'          => '',
     *      'priority'      => 0,
     *      'id'            => 0
     * ))
     * 
     * @since   1.0.0a
     */
    private array $routes = array(
        array(
            'destination'   => '',
            'file'          => 'home',
            'priority'      => 10,
            'id'            => 0
        ),
        array(
            'destination'   => '/',
            'file'          => 'home',
            'priority'      => 10,
            'id'            => 1
        ),
    );


    /**
     * Indicates if an instance of this class 
     * already exists.
     * @since   1.0.0a
     */
    public static array $Current_Pages = array();

    /**
     * Indicates if an instance of this class 
     * already exists.
     * @since   1.0.0a
     */
    public static array $Current_Page_IDs = array();

    /**
     * Indicates if an instance of this class 
     * already exists.
     * @since   1.0.0a
     */
    public static bool $Exists = false;


    /**
     * Constructs the routing handler instance.
     * @since   1.0.0a
     */
    function __construct() {

        global $app;
        if (defined('CONFIGURED')) throw $app->gracefully('Attempt to initialize Duplicate Instances of Routing detected and forcefully stopped.');

    }

    /**
     * Get all stored routes.
     * @since   1.0.0a
     * @return  array[array]
     */
    public function get_all() {
        return $this->routes;
    }

    /**
     * Gets all routes that has a parameter matching $mixed (destination, file or ID)
     * @since   1.0.0a
     * @return  array
     */
    public function get( $mixed ) {

        $matched = array();

        foreach($this->routes as $route) {
            if ( $route['destination'] == $mixed ||
                 $route['file']        == $mixed ||
                 $route['id']          == $mixed ) 
            {
                $matched[] = $route;
            }
        }

        return $route;

    }

    /**
     * Gets the route with ID (int) $id
     * @since   1.0.0a
     * @return  array|false
     */
    public function get_by_id( int $id ) {

        if ( !$id ) return false;
        foreach($this->routes as $route) {
            if ( $route['id'] == $id ) 
            {
                return $route;
            }
        }

        return false;

    }

    /**
     * Adds a route to the routing manager. @see Description for formatted details.
     * 
     * A route is stored as array(
     *      'destination'   => '',
     *      'file'          => '',
     *      'priority'      => 0,
     *      'id'            => 0    (Auto-increments, pk)
     * ))
     * 
     * @param   string  $destination    The destination (URL) of your route.
     * @param   string  $file           The file to get required in.
     * @param   int     $priority       The priority of the route.
     *                                  Lowest file priority takes priority
     *                                  in the loading order so multiple
     *                                  scripts can be queued up for 
     *                                  identical destinations.
     * @since   1.0.0a
     * @return  int
     */
    public function add( string $destination, string $file, int $priority = 10 ) {

        global $app;
        if ( !isset($destination) || !$destination ||
             !isset($file)        || !$file        ||
             !$priority           ) 
        {
            throw $app->gracefully('Attempting to add a route using bad parameters.');
            return false;
        }

        // Generate a unique Route ID.
        $id = count($this->routes);
        while ($this->get_by_id($id)) {
            $id++;
        }

        $this->routes[] = array(
            'destination'   => $destination,
            'file'          => $file,
            'priority'      => $priority,
            'id'            => $id
        );

        return $id;

    }

    /**
     * Modifies the route with ID (int) $id
     * @since   1.0.0a 
     * @return  bool
     */
    public function modify( int $id, array $new_route ) {

        global $app;
        if ( !isset($new_route) || !$id ) return false;
        if ( !isset($new_route['destination']) || 
             !isset($new_route['file'])        || 
             !isset($new_route['priority'])    ) 
        {
            throw $app->gracefully('Attempting to modify a route using a poorly-formatted $new_route');
            return false;
        }

        for($i = 0; $i < count($this->routes); $i++) {
            if ( $this->routes[$i]['id'] == $id ) 
            {
                $this->routes[$i] = $new_route;
                return true;
            }
        }

        return false;

    }

    /**
     * Removes the route with ID (int) $id
     * @since   1.0.0a 
     * @return  bool
     */
    public function remove( int $id ) {

        global $app;
        if ( !$id ) return false;

        for($i = 0; $i < count($this->routes); $i++) {
            if ( $this->routes[$i]['id'] == $id ) 
            {
                unset($this->routes[$i]);
                return true;
            }
        }

        return false;

    }

    /**
     * Checks what routes should get loaded, and then loads the required files.
     * @since   1.0.0a 
     */
    public function direct() {
        global $app;
        $request = $_SERVER['REQUEST_URI'];
        $results = array();

        // Get every route matching $request and then queue 
        // them into $results based on priority.
        foreach( $this->routes as $route ) {
            if ( $route['destination'] == $request ) {
                if (!count($results)) $matched[] = $route;
                else {
                    $splice_index = 0;
                    foreach( $results as $stored_route ) {
                        if ( $route['priority'] < $stored_route['priority'] ) break;
                        else $splice_index++;
                    }
                    array_splice($results, $splice_index, 0, array($route));
                }
            }
        }

        // If no results are found that means no route matches the request. 404.
        if ( !count($results) ) {
            require ABSPATH . CONTENT . PAGES . '/404' . SUFFIX;
            http_response_code(404);
            return;
        }

        // Try a few possible combinations of paths to the file in route.
        foreach ($results as $route) {
            $filepath = ''; 
            if ( file_exists($route['file']) ) {
                $filepath = $route['file'];
            } else if ( file_exists(ABSPATH . $route['file']) ) {
                $filepath = ABSPATH . $route['file'];
            } else if ( file_exists(ABSPATH . $route['file'] . CLASS_SUFFIX) ) {
                $filepath = ABSPATH . $route['file'] . CLASS_SUFFIX;
            } else if ( file_exists(ABSPATH . $route['file'] . SUFFIX) ) {
                $filepath = ABSPATH . $route['file'] . SUFFIX;
            } else if ( file_exists(ABSPATH . CONTENT . $route['file']) ) {
                $filepath = ABSPATH . CONTENT . $route['file'];
            }  else if ( file_exists(ABSPATH . CONTENT . $route['file'] . CLASS_SUFFIX) ) {
                $filepath = ABSPATH . CONTENT . $route['file'] . CLASS_SUFFIX;
            } else if ( file_exists(ABSPATH . CONTENT . $route['file'] . SUFFIX) ) {
                $filepath = ABSPATH . CONTENT . $route['file'] . SUFFIX;
            } else if ( file_exists(ABSPATH . CONTENT . PAGES . $route['file']) ) {
                $filepath = ABSPATH . CONTENT . PAGES . $route['file'];
            }  else if ( file_exists(ABSPATH . CONTENT . PAGES . $route['file'] . CLASS_SUFFIX) ) {
                $filepath = ABSPATH . CONTENT . PAGES . $route['file'] . CLASS_SUFFIX;
            } else if ( file_exists(ABSPATH . CONTENT . PAGES . $route['file'] . SUFFIX) ) {
                $filepath = ABSPATH . CONTENT . PAGES . $route['file'] . SUFFIX;
            } else {
                throw $app->gracefully('Routing Manager could not find file "'.$route['file'].'" as specified in route ('.$route['id'].') to destination "'.$route['destination'].'"');
            }

            Routing::$Current_Pages[] = $route['destination'];
            Routing::$Current_Page_IDs[] = $route['id'];
            include $filepath;
        }

    }

    /**
     * Determine what part should be used in the HTML document.
     * @since   1.0.0a
     */
    public function html_part( string $part_name, string $part ) {
        global $app;

        // Establish what the default equivalent of the requested part is.
        switch( $part ) {
            case 'head':
            case 'header':
            case 'footer':
                break;
            default:
                $app->gracefully('Routing Manager : html_part() - Provided with an invalid HTML part.');
                break;
        }
        $default = ABSPATH . CONTENT . PARTS . DEFAULT_PARTS . '/default-'.$part . SUFFIX;

        // Attempt to find a value for $part_name if there is none.
        // If no value can be found from what's given in parms or stored
        // in routes then return previously calculated default part.
        if ( !$part_name ) {
            foreach (Routing::$Current_Page_IDs as $id) {
                if ( isset($this->get_by_id($id)[$part]) ) {
                    $part_name = $this->get_by_id($id)[$part];
                    break;
                }
            }
            if ( !$part_name ) {
                include $default;
                return;
            }
        }

        // Then, determine if part even exists.
        $filepath = '';
        if ( file_exists(ABSPATH . CONTENT . PARTS . $part_name) ) {
            $filepath = ABSPATH . CONTENT . PARTS . $part_name;
        } else if ( file_exists(ABSPATH . CONTENT . PARTS . $part_name . CLASS_SUFFIX) ) {
            $filepath = ABSPATH . CONTENT . PARTS . $part_name . CLASS_SUFFIX;
        } else if ( file_exists(ABSPATH . CONTENT . PARTS . $part_name . SUFFIX) ) {
            $filepath = ABSPATH . CONTENT . PARTS . $part_name . SUFFIX;
        } else {
            include $default;
            return;
        }

        include $filepath;

    }

}