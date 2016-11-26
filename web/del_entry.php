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

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
global $PAGE, $DB, $USER;
include "config.inc.php";
include "functions.php";
require_once('tbs_auth.php');
include "tbs_sql.php";

$id = required_param('id', PARAM_INT);
$series = optional_param('series', 0, PARAM_INT);

$PAGE->set_url('/blocks/tbs/web/del_entry.php', array('id' => $id));
require_login();

require_sesskey();

if (getAuthorised(1) && ($info = tbsGetEntryInfo($id))) {
    $day = userdate($info->start_time, "%d");
    $month = userdate($info->start_time, "%m");
    $year = userdate($info->start_time, "%Y");
    $area = tbsGetRoomArea($info->room_id);

    if (MAIL_ADMIN_ON_DELETE) { // Gather all fields values for use in emails.
        $mail_previous = getPreviousEntryData($id, $series);
    }
    $roomadmin = false;
    $context = context_system::instance();
    if (has_capability('block/tbs:edittbsunconfirmed', $context, null, false)) {
        $adminemail = $DB->get_field('block_tbs_room', 'room_admin_email', array('id' => $info->room_id));
        if ($adminemail == $USER->email) {
            $roomadmin = true;
        }
    }
    $result = tbsDelEntry(getUserName(), $id, $series, 1, $roomadmin);

    if ($result) {
        // Send a mail to the Administrator
        (MAIL_ADMIN_ON_DELETE) ? $result = notifyAdminOnDelete($mail_previous) : '';
        $desturl = new moodle_url('/blocks/tbs/web/day.php', array(
            'day' => $day, 'month' => $month, 'year' => $year, 'area' => $area
        ));
        redirect($desturl);
        exit();
    }
}

// If you got this far then we got an access denied.
showAccessDenied($day, $month, $year, $area);
