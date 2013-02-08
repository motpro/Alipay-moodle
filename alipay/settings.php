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
 * Alipay enrolments plugin settings and presets.
 *
 * @package    enrol
 * @subpackage alipay 
 * @copyright  mot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- settings ------------------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_alipay_settings', '', get_string('pluginname_desc', 'enrol_alipay')));

    $settings->add(new admin_setting_configtext('enrol_alipay/alipaybusiness', get_string('businessemail', 'enrol_alipay'), get_string('businessemail_desc', 'enrol_alipay'), '', PARAM_EMAIL));
    $settings->add(new admin_setting_configtext('enrol_alipay/alipaybusinessid', get_string('businessid', 'enrol_alipay'), get_string('businessid_desc', 'enrol_alipay'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('enrol_alipay/alipaybusinesskey', get_string('businesskey', 'enrol_alipay'), get_string('businesskey_desc', 'enrol_alipay'), '', PARAM_TEXT));

    $settings->add(new admin_setting_configcheckbox('enrol_alipay/mailstudents', get_string('mailstudents', 'enrol_alipay'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_alipay/mailteachers', get_string('mailteachers', 'enrol_alipay'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_alipay/mailadmins', get_string('mailadmins', 'enrol_alipay'), '', 0));

    //--- enrol instance defaults ----------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_alipay_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                     ENROL_INSTANCE_DISABLED => get_string('no'));
    $settings->add(new admin_setting_configselect('enrol_alipay/status',
        get_string('status', 'enrol_alipay'), get_string('status_desc', 'enrol_alipay'), ENROL_INSTANCE_DISABLED, $options));

    $settings->add(new admin_setting_configtext('enrol_alipay/cost', get_string('cost', 'enrol_alipay'), '', 0, PARAM_FLOAT, 4));

    $alipaycurrencies = array(
                              'RMB' => 'China Yuan',
                             );
    $settings->add(new admin_setting_configselect('enrol_alipay/currency', get_string('currency', 'enrol_alipay'), '', 'USD', $alipaycurrencies));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(get_context_instance(CONTEXT_SYSTEM));
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_alipay/roleid',
            get_string('defaultrole', 'enrol_alipay'), get_string('defaultrole_desc', 'enrol_alipay'), $student->id, $options));
    }

    $settings->add(new admin_setting_configtext('enrol_alipay/enrolperiod',
        get_string('enrolperiod', 'enrol_alipay'), get_string('enrolperiod_desc', 'enrol_alipay'), 0, PARAM_INT));
}
