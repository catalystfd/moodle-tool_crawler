<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *  Unit tests for link crawler robot
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_crawler\robot\crawler;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden');

/**
 *  Unit tests for link crawler robot
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_crawler_robot_crawler_test extends advanced_testcase {

    protected function setUp() {
        parent::setup();
        $this->resetAfterTest(true);

        $this->robot = new \tool_crawler\robot\crawler();

    }

    /**
     * @return array of test cases
     *
     * Combinations of base and relative parts of URL
     */
    public function absolute_urls_provider() {
        return array(
            array(
                'base' => 'http://test.com/sub/',
                'links' => array(
                    'mailto:me@test.com' => 'mailto:me@test.com',
                    '/file.php' => 'http://test.com/file.php',
                    'file.php' => 'http://test.com/sub/file.php',
                    '../sub2/file.php' => 'http://test.com/sub2/file.php',
                    'http://elsewhere.com/path/' => 'http://elsewhere.com/path/'
                )
            ),
            array(
                'base' => 'http://test.com/sub1/sub2/',
                'links' => array(
                    'mailto:me@test.com' => 'mailto:me@test.com',
                    '../../file.php' => 'http://test.com/file.php',
                    'file.php' => 'http://test.com/sub1/sub2/file.php',
                    '../sub3/file.php' => 'http://test.com/sub1/sub3/file.php',
                    'http://elsewhere.com/path/' => 'http://elsewhere.com/path/'
                )
            ),
            array(
                'base' => 'http://test.com/sub1/sub2/$%^/../../../',
                'links' => array(
                    'mailto:me@test.com' => 'mailto:me@test.com',
                    '/file.php' => 'http://test.com/file.php',
                    '/sub3/sub4//$%^/../../../file.php' => 'http://test.com/file.php',
                    'http://elsewhere.com/path/' => 'http://elsewhere.com/path/'
                    )
            ),
            array(
                'base' => 'http://test.com/sub1/sub2/file1.php',
                'links' => array(
                    'mailto:me@test.com' => 'mailto:me@test.com',
                    'file2.php' => 'http://test.com/sub1/sub2/file2.php',
                    '../file2.php' => 'http://test.com/sub1/file2.php',
                    'sub3/file2.php' => 'http://test.com/sub1/sub2/sub3/file2.php'
                )
            ),
            array(
                'base' => 'http://test.com/sub1/foo.php?id=12',
                'links' => array(
                    '/sub2/bar.php?id=34' => 'http://test.com/sub2/bar.php?id=34',
                    '/sub2/bar.php?id=34&foo=bar' => 'http://test.com/sub2/bar.php?id=34&foo=bar',
                ),
            ),
        );
    }

    /**
     * @dataProvider absolute_urls_provider
     *
     * Executing test cases returned by function provider()
     *
     * @param string $base Base part of URL
     * @param array $links Combinations of relative paths of URL and expected result
     */
    public function test_absolute_urls($base, $links) {
        foreach ($links as $key => $value) {
            $this->assertEquals($value, $this->robot->absolute_url($base, $key));
        }
    }

    /**
     * Tests existence of new plugin parameter 'retentionperiod'
     */
    public function test_param_retention_exists() {
        $param = get_config('tool_crawler', 'retentionperiod');
        $this->assertNotEmpty($param);
    }

    /** Regression test for Issue #17  */
    public function test_reset_queries() {
        global $DB;

        $node = [
            'url' => 'http://crawler.test/course/index.php',
            'external' => 0,
            'createdate' => strtotime("16-05-2016 10:00:00"),
            'lastcrawled' => strtotime("16-05-2016 11:20:00"),
            'needscrawl' => strtotime("17-05-2017 10:00:00"),
            'httpcode' => 200,
            'mimetype' => 'text/html',
            'title' => 'Crawler Test',
            'downloadduration' => 0.23,
            'filesize' => 44003,
            'redirect' => null,
            'courseid' => 1,
            'contextid' => 1,
            'cmid' => null,
            'ignoreduserid' => null,
            'ignoredtime' => null,
            'httpmsg' => 'OK'
        ];
        $nodeid = $DB->insert_record('tool_crawler_url', $node);

        $crawler = new crawler();
        $crawler->reset_for_recrawl($nodeid);

        // Record should not exist anymore.
        $found = $DB->record_exists('tool_crawler_url', ['id' => $nodeid]);
        self::assertFalse($found);
    }
}
