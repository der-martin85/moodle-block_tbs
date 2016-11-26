<?php
// This file is part of the TBS block for Moodle
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

defined('MOODLE_INTERNAL') || die();
global $CFG;

// The following couple of lines stop a warning message when setting up PHPUnit.
if (!isset($CFG->supportname)) {
    $CFG->supportname = '';
}
if (!isset($CFG->supportemail)) {
    $CFG->supportemail = '';
}

$cfg_tbs = get_config('block/tbs');

$options = array(0 => get_string('pagewindow', 'block_tbs'), 1 => get_string('newwindow', 'block_tbs'));
$settings->add(new admin_setting_configselect('newwindow', get_string('config_new_window', 'block_tbs'), get_string('config_new_window2', 'block_tbs'), 1, $options));
$settings->settings->newwindow->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('serverpath', get_string('serverpath', 'block_tbs'),
                                            get_string('adminview', 'block_tbs'), $CFG->wwwroot.'/blocks/tbs/web', PARAM_URL));
$settings->settings->serverpath->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('admin', get_string('config_admin', 'block_tbs'), get_string('config_admin2', 'block_tbs'), $CFG->supportname, PARAM_TEXT));
$settings->settings->admin->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('admin_email', get_string('config_admin_email', 'block_tbs'), get_string('config_admin_email2', 'block_tbs'), $CFG->supportemail, PARAM_TEXT));
$settings->settings->admin_email->plugin = 'block/tbs';

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('enable_periods', get_string('config_enable_periods', 'block_tbs'), get_string('config_enable_periods2', 'block_tbs'), 1, $options));
$settings->settings->enable_periods->plugin = 'block/tbs';

if (isset($cfg_tbs->enable_periods)) {
    if ($cfg_tbs->enable_periods == 0) {

        // Resolution

        unset($options);
        $strunits = get_string('resolution_units', 'block_tbs');
        $options = array(
            '900' => '15'.$strunits, '1800' => '30'.$strunits, '2700' => '45'.$strunits, '3600' => '60'.$strunits,
            '4500' => '75'.$strunits, '5400' => '90'.$strunits, '6300' => '105'.$strunits, '7200' => '120'.$strunits
        );
        $settings->add(new admin_setting_configselect('resolution', get_string('config_resolution', 'block_tbs'), get_string('config_resolution2', 'block_tbs'), '1800', $options));
        $settings->settings->resolution->plugin = 'block/tbs';

        // Start Time (Hours)
        unset($options);
        $options = array(
            1 => '01', 2 => '02', 3 => '03', 4 => '04', 5 => '05', 6 => '06', 7 => '07', 8 => '08', 9 => '09', 10 => '10',
            11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20',
            21 => '21', 22 => '22', 23 => '23'
        );
        $settings->add(new admin_setting_configselect('morningstarts', get_string('config_morningstarts', 'block_tbs'), get_string('config_morningstarts2', 'block_tbs'), 7, $options));
        $settings->settings->morningstarts->plugin = 'block/tbs';

        // Start Time (Min)
        unset($options);
        $options = array(
            0 => '00', 5 => '05', 10 => '10', 15 => '15', 20 => '20', 25 => '25', 30 => '30', 35 => '35', 40 => '40', 45 => '45',
            50 => '50', 55 => '55'
        );
        $settings->add(new admin_setting_configselect('morningstarts_min', get_string('config_morningstarts_min', 'block_tbs'), get_string('config_morningstarts_min2', 'block_tbs'), 0, $options));
        $settings->settings->morningstarts_min->plugin = 'block/tbs';
        // End Time (Hours)
        unset($options);
        $options = array(
            1 => '01', 2 => '02', 3 => '03', 4 => '04', 5 => '05', 6 => '06', 7 => '07', 8 => '08', 9 => '09', 10 => '10',
            11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20',
            21 => '21', 22 => '22', 23 => '23'
        );
        $settings->add(new admin_setting_configselect('eveningends', get_string('config_eveningends', 'block_tbs'), get_string('config_eveningends2', 'block_tbs'), 19, $options));
        $settings->settings->eveningends->plugin = 'block/tbs';
        // End Time Time (Min)
        unset($options);
        $options = array(
            0 => '00', 5 => '05', 10 => '10', 15 => '15', 20 => '20', 25 => '25', 30 => '30', 35 => '35', 40 => '40', 45 => '45',
            50 => '50', 55 => '55'
        );
        $settings->add(new admin_setting_configselect('eveningends_min', get_string('config_eveningends_min', 'block_tbs'), get_string('config_eveningends_min2', 'block_tbs'), 0, $options));
        $settings->settings->eveningends_min->plugin = 'block/tbs';
    } else {  //Use Custom Periods

        $settings->add(new admin_setting_configtextarea('periods', get_string('config_periods', 'block_tbs'), get_string('config_periods2', 'block_tbs'), ''));
        $settings->settings->periods->plugin = 'block/tbs';
    }
}

// Date Information

//Start of Week
unset($options);
$options = array(
    0 => get_string('sunday', 'calendar'), 1 => get_string('monday', 'calendar'), 2 => get_string('tuesday', 'calendar'),
    3 => get_string('wednesday', 'calendar'), 4 => get_string('thursday', 'calendar'), 5 => get_string('friday', 'calendar'),
    6 => get_string('saturday', 'calendar')
);
$settings->add(new admin_setting_configselect('weekstarts', get_string('config_weekstarts', 'block_tbs'), get_string('config_weekstarts2', 'block_tbs'), 0, $options));
$settings->settings->weekstarts->plugin = 'block/tbs';
//Length of week
$settings->add(new admin_setting_configtext('weeklength', get_string('config_weeklength', 'block_tbs'), get_string('config_weeklength2', 'block_tbs'), 7, PARAM_INT));
$settings->settings->weeklength->plugin = 'block/tbs';
//Date Format
unset($options);
$options = array(0 => get_string('config_date_mmddyy', 'block_tbs'), 1 => get_string('config_date_ddmmyy', 'block_tbs'));
$settings->add(new admin_setting_configselect('dateformat', get_string('config_dateformat', 'block_tbs'), get_string('config_dateformat2', 'block_tbs'), 0, $options));
$settings->settings->dateformat->plugin = 'block/tbs';
//Time format
unset($options);
$options = array(0 => get_string('timeformat_12', 'calendar'), 1 => get_string('timeformat_24', 'calendar'));
$settings->add(new admin_setting_configselect('timeformat', get_string('config_timeformat', 'block_tbs'), get_string('config_timeformat2', 'block_tbs'), 1, $options));
$settings->settings->timeformat->plugin = 'block/tbs';

// $settings = new admin_settingpage('block_tbs_misc', get_string('block_tbs_misc','block_tbs')); // it would be good to be able to break this page up somehow
// Misc Settings
$settings->add(new admin_setting_configtext('max_rep_entrys', get_string('config_max_rep_entrys', 'block_tbs'), get_string('config_max_rep_entrys2', 'block_tbs'), 365, PARAM_INT));
$settings->settings->max_rep_entrys->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('max_advance_days', get_string('config_max_advance_days', 'block_tbs'), get_string('config_max_advance_days2', 'block_tbs'), -1, PARAM_INT));
$settings->settings->max_advance_days->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('default_report_days', get_string('config_default_report_days', 'block_tbs'), get_string('config_default_report_days2', 'block_tbs'), 60, PARAM_INT));
$settings->settings->default_report_days->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('search_count', get_string('config_search_count', 'block_tbs'), get_string('config_search_count2', 'block_tbs'), 20, PARAM_INT));
$settings->settings->search_count->plugin = 'block/tbs';

/*
$settings->add(new admin_setting_configtext('refresh_rate', get_string('config_refresh_rate', 'block_tbs'), get_string('config_refresh_rate2', 'block_tbs'), 0, PARAM_INT));
$settings->settings->refresh_rate->plugin='block/tbs';
*/

$options = array('list' => get_string('list'), 'select' => get_string('select'));
$settings->add(new admin_setting_configselect('area_list_format', get_string('config_area_list_format', 'block_tbs'), get_string('config_area_list_format2', 'block_tbs'), 'list', $options));
$settings->settings->area_list_format->plugin = 'block/tbs';

$options = array(
    'both' => get_string('both', 'block_tbs'), 'description' => get_string('description'),
    'slot' => get_string('slot', 'block_tbs')
);
$settings->add(new admin_setting_configselect('monthly_view_entries_details', get_string('config_monthly_view_entries_details', 'block_tbs'), get_string('config_monthly_view_entries_details2', 'block_tbs'), 'both', $options));
$settings->settings->monthly_view_entries_details->plugin = 'block/tbs';

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('view_week_number', get_string('config_view_week_number', 'block_tbs'), get_string('config_view_week_number2', 'block_tbs'), 0, $options));
$settings->settings->view_week_number->plugin = 'block/tbs';

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('times_right_side', get_string('config_times_right_side', 'block_tbs'), get_string('config_times_right_side2', 'block_tbs'), 0, $options));
$settings->settings->times_right_side->plugin = 'block/tbs';

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('javascript_cursor', get_string('config_javascript_cursor', 'block_tbs'), get_string('config_javascript_cursor2', 'block_tbs'), 1, $options));
$settings->settings->javascript_cursor->plugin = 'block/tbs';

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('show_plus_link', get_string('config_show_plus_link', 'block_tbs'), get_string('config_show_plus_link2', 'block_tbs'), 1, $options));
$settings->settings->show_plus_link->plugin = 'block/tbs';

$options = array(
    'bgcolor' => get_string('bgcolor', 'block_tbs'), 'class' => get_string('class', 'block_tbs'),
    'hybrid' => get_string('hybrid', 'block_tbs')
);
$settings->add(new admin_setting_configselect('highlight_method', get_string('config_highlight_method', 'block_tbs'), get_string('config_highlight_method2', 'block_tbs'), 'hybrid', $options));
$settings->settings->highlight_method->plugin = 'block/tbs';

$options = array('day' => get_string('day'), 'month' => get_string('month', 'block_tbs'), 'week' => get_string('week'));
$settings->add(new admin_setting_configselect('default_view', get_string('config_default_view', 'block_tbs'), get_string('config_default_view2', 'block_tbs'), 'day', $options));
$settings->settings->default_view->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('default_room', get_string('config_default_room', 'block_tbs'), get_string('config_default_room2', 'block_tbs'), 0, PARAM_INT));
$settings->settings->default_room->plugin = 'block/tbs';

// should this be the same as the Moodle Site cookie path?
// $settings->add(new admin_setting_configtext('cookie_path_override', get_string('config_cookie_path_override', 'block_tbs'), get_string('config_cookie_path_override2', 'block_tbs'), '', PARAM_LOCALURL));
// $settings->settings->cookie_path_override->plugin='block/tbs';

/*

//select
$options = array('' => get_string('', 'block_tbs'), '' => get_string('', 'block_tbs'));
$settings->add(new admin_setting_configselect('', get_string('config_', 'block_tbs'), get_string('config_2', 'block_tbs'), '', $options));
$settings->settings->->plugin='block/tbs';

//text or int
$settings->add(new admin_setting_configtext('', get_string('config_', 'block_tbs'), get_string('config_2', 'block_tbs'), 0, PARAM_INT));
$settings->settings->->plugin='block/tbs';
*/

$settings->add(new admin_setting_configtext('entry_type_a', get_string('config_entry_type', 'block_tbs', 'A'), get_string('config_entry_type2', 'block_tbs', 'A'), null, PARAM_TEXT));
$settings->settings->entry_type_a->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_b', get_string('config_entry_type', 'block_tbs', 'B'), get_string('config_entry_type2', 'block_tbs', 'B'), null, PARAM_TEXT));
$settings->settings->entry_type_b->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_c', get_string('config_entry_type', 'block_tbs', 'C'), get_string('config_entry_type2', 'block_tbs', 'C'), null, PARAM_TEXT));
$settings->settings->entry_type_c->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_d', get_string('config_entry_type', 'block_tbs', 'D'), get_string('config_entry_type2', 'block_tbs', 'D'), null, PARAM_TEXT));
$settings->settings->entry_type_d->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_e', get_string('config_entry_type', 'block_tbs', 'E'), get_string('config_entry_type2', 'block_tbs', 'E'), get_string('external', 'block_tbs'), PARAM_TEXT));
$settings->settings->entry_type_e->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_f', get_string('config_entry_type', 'block_tbs', 'F'), get_string('config_entry_type2', 'block_tbs', 'F'), null, PARAM_TEXT));
$settings->settings->entry_type_f->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_g', get_string('config_entry_type', 'block_tbs', 'G'), get_string('config_entry_type2', 'block_tbs', 'G'), null, PARAM_TEXT));
$settings->settings->entry_type_g->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_h', get_string('config_entry_type', 'block_tbs', 'H'), get_string('config_entry_type2', 'block_tbs', 'H'), null, PARAM_TEXT));
$settings->settings->entry_type_h->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_i', get_string('config_entry_type', 'block_tbs', 'I'), get_string('config_entry_type2', 'block_tbs', 'I'), get_string('internal', 'block_tbs'), PARAM_TEXT));
$settings->settings->entry_type_i->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('entry_type_j', get_string('config_entry_type', 'block_tbs', 'J'), get_string('config_entry_type2', 'block_tbs', 'J'), null, PARAM_TEXT));
$settings->settings->entry_type_j->plugin = 'block/tbs';

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('mail_admin_on_bookings', get_string('config_mail_admin_on_bookings', 'block_tbs'), get_string('config_mail_admin_on_bookings2', 'block_tbs'), '0', $options));
$settings->settings->mail_admin_on_bookings->plugin = 'block/tbs';

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('mail_area_admin_on_bookings', get_string('config_mail_area_admin_on_bookings', 'block_tbs'), get_string('config_mail_area_admin_on_bookings2', 'block_tbs'), 0, $options));
$settings->settings->mail_area_admin_on_bookings->plugin = 'block/tbs';

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('mail_room_admin_on_bookings', get_string('config_mail_room_admin_on_bookings', 'block_tbs'), get_string('config_mail_room_admin_on_bookings2', 'block_tbs'), 0, $options));
$settings->settings->mail_room_admin_on_bookings->plugin = 'block/tbs';

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('mail_admin_on_delete', get_string('config_mail_admin_on_delete', 'block_tbs'), get_string('config_mail_admin_on_delete2', 'block_tbs'), 0, $options));
$settings->settings->mail_admin_on_delete->plugin = 'block/tbs';

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('mail_admin_all', get_string('config_mail_admin_all', 'block_tbs'), get_string('config_mail_admin_all2', 'block_tbs'), 0, $options));
$settings->settings->mail_admin_all->plugin = 'block/tbs';

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('mail_details', get_string('config_mail_details', 'block_tbs'), get_string('config_mail_details2', 'block_tbs'), 0, $options));
$settings->settings->mail_details->plugin = 'block/tbs';

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('mail_booker', get_string('config_mail_booker', 'block_tbs'), get_string('config_mail_booker2', 'block_tbs'), 0, $options));
$settings->settings->mail_booker->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('mail_from', get_string('config_mail_from', 'block_tbs'), get_string('config_mail_from2', 'block_tbs'), $CFG->supportemail, PARAM_TEXT));
$settings->settings->mail_from->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('mail_recipients', get_string('config_mail_recipients', 'block_tbs'), get_string('config_mail_recipients2', 'block_tbs'), $CFG->supportemail, PARAM_TEXT));
$settings->settings->mail_recipients->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('mail_cc', get_string('config_mail_cc', 'block_tbs'), get_string('config_mail_cc2', 'block_tbs'), null, PARAM_TEXT));
$settings->settings->mail_cc->plugin = 'block/tbs';

$settings->add(new admin_setting_configtext('cronfile', get_string('cronfile', 'block_tbs'), get_string('cronfiledesc', 'block_tbs'), null, PARAM_TEXT));
$settings->settings->cronfile->plugin = 'block/tbs';
