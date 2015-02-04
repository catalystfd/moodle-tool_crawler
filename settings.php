<?php
/* This file is part of Moodle - http://moodle.org/
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
 *  link checker robot local plugin settings
 *
 * @package    local_linkchecker_robot
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    // Site admin reports

    $cat =  new admin_category('local_linkchecker_cat', 'Link checker');
    $ADMIN->add('reports', $cat);

    $ADMIN->add('local_linkchecker_cat', new admin_externalpage('local_linkchecker_robot_status',
                                           get_string('status', 'local_linkchecker_robot'),
                                           $CFG->wwwroot . '/local/linkchecker_robot/index.php') );

    $ADMIN->add('local_linkchecker_cat', new admin_externalpage('local_linkchecker_robot_broken',
                                           get_string('broken', 'local_linkchecker_robot'),
                                           $CFG->wwwroot . '/local/linkchecker_robot/report.php?report=broken') );

    $ADMIN->add('local_linkchecker_cat', new admin_externalpage('local_linkchecker_robot_oversize',
                                           get_string('oversize', 'local_linkchecker_robot'),
                                           $CFG->wwwroot . '/local/linkchecker_robot/report.php?report=oversize') );


    // Local plugin settings

    $settings = new admin_settingpage('local_linkchecker_robot', get_string('pluginname', 'local_linkchecker_robot'));
    // Add the admin page to the menu tree if it ain't exist.
    if (!$ADMIN->locate('localplugins')) {
        $ADMIN->add('root', new admin_category('localplugins', 'Local Plugins'));
    }

    $ADMIN->add('localplugins', $settings);
    if (!during_initial_install()) {

        // link to report
        // link to site tools, eg crawl as

        $settings->add(new admin_setting_heading('linkchecker',
                                                    '',
                                                    new lang_string('checker_help',      'local_linkchecker_robot',
                                                    array('url' => '/local/linkchecker_robot/index.php') )));

        $settings->add(new admin_setting_configtext('local_linkchecker_robot/seedurl',
                                                    new lang_string('seedurl',           'local_linkchecker_robot'),
                                                    new lang_string('seedurldesc',       'local_linkchecker_robot'),
                                                    '/' ));

        $settings->add(new admin_setting_configtext('local_linkchecker_robot/botusername',
                                                    new lang_string('botusername',       'local_linkchecker_robot'),
                                                    new lang_string('botusernamedesc',   'local_linkchecker_robot'),
                                                    'moodlebot' ));

        $settings->add(new admin_setting_configpasswordunmask('local_linkchecker_robot/botpassword',
                                                    new lang_string('botpassword',       'local_linkchecker_robot'),
                                                    new lang_string('botpassworddesc',   'local_linkchecker_robot'),
                                                    'moodlebot' ));

        $settings->add(new admin_setting_configtext('local_linkchecker_robot/useragent',
                                                    new lang_string('useragent',         'local_linkchecker_robot'),
                                                    new lang_string('useragentdesc',     'local_linkchecker_robot'),
                                                    'MoodleLinkChecker' ));

        $settings->add(new admin_setting_configtextarea('local_linkchecker_robot/excludeexturl',
                                                    new lang_string('excludeexturl',     'local_linkchecker_robot'),
                                                    new lang_string('excludeexturldesc', 'local_linkchecker_robot'),
                                                    'http://moodle.org/' ));

        $settings->add(new admin_setting_configtextarea('local_linkchecker_robot/excludemdlurl',
                                                    new lang_string('excludemdlurl',     'local_linkchecker_robot'),
                                                    new lang_string('excludemdlurldesc', 'local_linkchecker_robot'),
                                                    "sesskey
grading
/admin
/blog
/badges
/blocks/quickmail
/calendar
/enrol
/login
/message
/report
/user
/tag/" ));

        $settings->add(new admin_setting_configtextarea('local_linkchecker_robot/excludemdldom',
                                                    new lang_string('excludemdldom',     'local_linkchecker_robot'),
                                                    new lang_string('excludemdldomdesc', 'local_linkchecker_robot'),
                                                    ".block.block_settings
.block.block_book_toc
.block.block_navigation
.block.block_cqu_assessment" ));

        $settings->add(new admin_setting_configtext('local_linkchecker_robot/maxtime',
                                                    new lang_string('maxtime',           'local_linkchecker_robot'),
                                                    new lang_string('maxtimedesc',       'local_linkchecker_robot'),
                                                    '10' ));

        $settings->add(new admin_setting_configtext('local_linkchecker_robot/maxcrontime',
                                                    new lang_string('maxcrontime',       'local_linkchecker_robot'),
                                                    new lang_string('maxcrontimedesc',   'local_linkchecker_robot'),
                                                    '60' ));

    }
}

