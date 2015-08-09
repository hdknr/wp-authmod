<?php

class SampleTest extends WP_UnitTestCase {

    private $plugin = null;
    function setUp() {
        parent::setUp();
        $this->plugin = $GLOBALS['wp-authmod-plugin'];
    }

	function test_filter_the_title() {
        $title = "hoge";
		$this->assertEquals( 
            $this->plugin->filter_the_title($title),
            "$title (debugging)"
        );
	}
}

