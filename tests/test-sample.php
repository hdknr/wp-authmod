<?php class SampleTest extends WP_UnitTestCase {
    protected $backupGlobalsBlacklist = ['wp_filter'];

    private $plugin = null;             // plugin 

    function setUp() {
        parent::setUp();
        // $this->plugin = $GLOBALS['wp-authmod-plugin'];

    }

    function expected_errors($error_messages) {
        $this->expected_error_list = (array) $error_messages;
        set_error_handler(
            array(&$this, 'expected_errors_handler'));
    }

    function expected_errors_handler($errno, $errstr) {
        foreach ($this->expected_error_list as $expect) {
            if (strpos($errstr, $expect) !== false) {
                $this->expected_errors_found = true;
                return true;
            }
        }
        return false;
    }

    function were_expected_errors_found() {
        restore_error_handler();
        return $this->expected_errors_found;
    }

	function test_filter_the_title() {
        /*1
        $title = "hoge";
		$this->assertEquals( 
            $this->plugin->filter_the_title($title),
            "$title (debugging)"
        );
        */
	}

    /*
       phpunit --filter 'SampleTest::test_getpost'

       https://develop.svn.wordpress.org/trunk/tests/phpunit/tests/query/conditionals.php
       `test_page_trackback`
    */
    function test_getpost(){
        $user_id = $this->factory->user->create();
        $post_id = $this->factory->post->create(
            array('post_author' => $user_id,
                  'post_title' => 'Post test',
                  'post_type' => 'page',
                  'post_status'  => 'publish')); 

        // TODO: MUST use `get_permalink` call,
        // which generate a url like `http://example.org/?page_id=3` , 
        // not `?p=3`.
        $url = get_permalink($post_id);

        // TODO: I don't know why this is blank.
        $this->assertEquals('', get_option('permalink_structure'));

        // Access the URL
        $this->go_to($url);
        
        global $wp_query;
        $post = $wp_query->get_queried_object();
        $this->assertEquals($post->ID, $post_id); 

        // or simpley with wrapper function
        $this->assertEquals(get_queried_object()->ID, $post_id); 
    }

    function test_tables(){
        global $wpdb;
        $tables = $wpdb->get_var("SHOW TABLES");
        var_dump($tables);
    }

    /**
     */
    function test_authcookie() {
        /* phpunit --filter 'SampleTest::test_authcookie'
        */
        $this->expected_errors('Cannot modify header information');

        $user_id = $this->factory->user->create();
       
        // https://codex.wordpress.org/Function_Reference/is_user_logged_in
        // $this->assertFalse(is_user_logged_in());

        // https://codex.wordpress.org/Function_Reference/wp_set_auth_cookie
        //wp_set_auth_cookie($user_id);
        wp_set_current_user($user_id, 'user');

        $this->assertTrue(is_user_logged_in());
    }
}
