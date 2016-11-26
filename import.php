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

/**
 * This file imports bookings via CSV flatfile; it is intended to allow tbs to be
 * populated with bookings from any other timetable sytem. It is intended to run
 *  regularly, it will replace any non-edited imported bookings with a new copy but
 * not any that have been edited.
 *
 * It is included by the blocks cron() function each time it runs
 */

//TODO:maybe set it up like tutorlink etc so that it can take uploaded files directly?
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

global $DB;

//record time for time taken stat
$script_start_time = time();

$cfg_tbs = get_config('block/tbs'); //get Moodle config settings for the TBS block
if (!isset($cfg_tbs->periods) or empty($cfg_tbs->periods)) {
    $cfg_tbs->periods = array();
    $cfg_tbs->periods[] = "Period&nbsp;1";
    $cfg_tbs->periods[] = "Period&nbsp;2";
    $cfg_tbs->periods[] = "Period&nbsp;3";
    $cfg_tbs->periods[] = "Period&nbsp;4";
    $cfg_tbs->periods[] = "Period&nbsp;5";
    $cfg_tbs->periods[] = "Period&nbsp;6";
    $cfg_tbs->periods[] = "Period&nbsp;7";
    $cfg_tbs->periods[] = "Period&nbsp;8";
    $cfg_tbs->periods[] = "Period&nbsp;9";
    $cfg_tbs->periods[] = "Period&nbsp;10";
    $cfg_tbs->periods[] = "Period&nbsp;11";
    $cfg_tbs->periods[] = "Period&nbsp;12";
} else {
    $pds = explode("\n", $cfg_tbs->periods);
    $cfg_tbs->periods = array();
    foreach ($pds as $pd) {
        $pd = trim($pd);
        $cfg_tbs->periods[] = $pd;
    }
}
$output = '';
if (!empty($cfg_tbs->cronfile) && file_exists($cfg_tbs->cronfile)) {
    if ($tbs_sessions = fopen($cfg_tbs->cronfile, 'r')) {
        $output .= get_string('startedimport', 'block_tbs')."\n";
        $now = time();
        $DB->set_field_select('block_tbs_entry', 'type', 'M', 'type=\'K\' and start_time > ?', array($now)); // Change old imported (type K) records to temporary type M
        while ($array = fgetcsv($tbs_sessions)) { //import timetable into tbs
            $csvrow = new stdClass();
            $csvrow->start_time = clean_param($array[0], PARAM_TEXT);
            $csvrow->end_time = clean_param($array[1], PARAM_TEXT);
            $csvrow->first_date = clean_param($array[2], PARAM_TEXT);
            $csvrow->weekpattern = clean_param($array[3], PARAM_TEXT);
            $csvrow->room_name = clean_param($array[4], PARAM_TEXT);
            $csvrow->username = clean_param($array[5], PARAM_TEXT);
            $csvrow->name = clean_param($array[6], PARAM_TEXT);
            $csvrow->description = clean_param($array[7], PARAM_TEXT);

            list($year, $month, $day) = explode('/', $csvrow->first_date);
            $date = mktime(00, 00, 00, $month, $day, $year);
            $room = room_id_lookup($csvrow->room_name);
            $weeks = str_split($csvrow->weekpattern);
            foreach ($weeks as $week) {
                if (($week == 1) and ($date > $now)) {
                    $start_time = time_to_datetime($date, $csvrow->start_time);
                    $end_time = time_to_datetime($date, $csvrow->end_time);
                    if (!is_timetabled($csvrow->name, $start_time)) { ////only timetable class if it isn't already timetabled elsewhere (class been moved)
                        $entry = new stdClass();
                        $entry->start_time = $start_time;
                        $entry->end_time = $end_time;
                        $entry->room_id = $room;
                        $entry->timestamp = $now;
                        $entry->create_by = $csvrow->username;
                        $entry->name = $csvrow->name;
                        $entry->type = 'K';
                        $entry->description = $csvrow->description;
                        $newentryid = $DB->insert_record('block_tbs_entry', $entry);

                        //If there is another non-imported booking there, send emails. It is assumed that simultanious imported classes are intentional
                        $sql = "SELECT *
                                FROM {block_tbs_entry} AS e
                                WHERE
                                    ((e.start_time < ? AND e.end_time > ?)
                                  OR (e.start_time < ? AND e.end_time > ?)
                                  OR (e.start_time >= ? AND e.end_time <= ? ))
                                AND e.room_id = ? AND type <>'K'";

                        //limit to 1 to keep this simpler- if there is a 3-way clash it will be noticed by one of the 2 teachers notified
                        if ($existingclass = $DB->get_record_sql($sql, array(
                            $start_time, $start_time, $end_time,
                            $end_time, $start_time, $end_time, $room
                        ))
                        ) {
                            $hr_start_time = date("j F, Y", $start_time).", ".to_hr_time($start_time);
                            $a = new stdClass();
                            $a->oldbooking = $existingclass->description.'('.$existingclass->id.')';
                            $a->newbooking = $csvrow->description.'('.$newentryid.')';
                            $a->time = $hr_start_time;
                            $a->room = $csvrow->room_name;
                            $a->admin = $cfg_tbs->admin.' ('.$cfg_tbs->admin_email.')';
                            $output .= get_string('clash', 'block_tbs', $a);

                            $existingteacher = $DB->get_record('user', array('username' => $existingclass->create_by));
                            $newteacher = $DB->get_record('user', array('username' => $csvrow->username));

                            $body = get_string('clashemailbody', 'block_tbs', $a);

                            if (email_to_user($existingteacher, $newteacher, get_string('clashemailsub', 'block_tbs', $a), $body)) {
                                $output .= ', '.get_string('clashemailsent', 'block_tbs').' '.$existingteacher->firstname.' '.$existingteacher->lastname.'<'.$existingteacher->email.'>';
                            } else {
                                $output .= get_string('clashemailnotsent', 'block_tbs').$existingclass->description.'('.$existingclass->id.')';
                            }
                            if (email_to_user($newteacher, $existingteacher, get_string('clashemailsub', 'block_tbs', $a), $body)) {
                                $output .= ', '.get_string('clashemailsent', 'block_tbs').' '.$newteacher->firstname.' '.$newteacher->lastname.'<'.$newteacher->email.'>';
                            } else {
                                $output .= get_string('clashemailnotsent', 'block_tbs').$csvrow->description.'('.$newentryid.')';
                            }
                            $output .= "\n";
                        }
                    }
                }
                $date += 604800;

                //checks for being an hour out due to BST/GMT change and corrects
                if (date('G', $date) == 01) {
                    $date = $date + 3600;
                }
                if (date('G', $date) == 23) {
                    $date = $date - 3600;
                }
            }
        }

        // any remaining type M records are no longer in the import file, so delete
        $DB->delete_records_select('block_tbs_entry', 'type=\'M\'');

        //move the processed file to prevent wasted time re-processing TODO: option for how long to keep these- I've found them useful for debugging but obviously can't keep them for ever
        $date = date('Ymd');
        if (rename($cfg_tbs->cronfile, $cfg_tbs->cronfile.'.'.$date)) {
            $output .= $cfg_tbs->cronfile.get_string('movedto', 'block_tbs').$cfg_tbs->cronfile.'.'.$date."\n";
        }
        $script_time_taken = time() - $script_start_time;
        $output .= get_string('finishedimport', 'block_tbs', $script_time_taken);

        echo $output; //will only show up if being run via apache

        //email output to admin
        if ($tbsadmin = $DB->get_record('user', array('email' => $cfg_tbs->admin_email))) {
            email_to_user($tbsadmin, $tbsadmin, get_string('importlog', 'block_tbs'), $output);
        }
    }
}
//==========================================FUNCTIONS==============================================================

//looks up the room id from the name
function room_id_lookup($name) {
    global $DB;
    if (!$room = $DB->get_record('block_tbs_room', array('room_name' => $name))) {
        $error = "ERROR: failed to return id from database (room $name probably doesn't exist)";
        echo $error."\n";
        return 'error';
    } else {
        return $room->id;
    }
}

/**
 * Checks if a class already has a timetable entry. If a previous imported entry exists,
 * and was edited, leave it. If it wasn't edited (flagged by type M), change it's type back to
 * K (to show it's an imported record), and return true. If there's no record for the class, or
 * updating the type back to K fails, return false.
 *
 * @param $name string name of the booking
 * @param $time int start time of the booking in unix timestamp format
 * @return bool does a previous booking exist?
 */
function is_timetabled($name, $time) {
    global $DB;
    if ($DB->get_record('block_tbs_entry', array('name' => $name, 'start_time' => $time, 'type' => 'L'))) {
        return true;
    } else if ($record = $DB->get_record('block_tbs_entry', array(
        'name' => $name, 'start_time' => $time, 'type' => 'M'
    ))
    ) {
        $upd = new stdClass;
        $upd->id = $record->id;
        $upd->type = 'K';
        if ($DB->update_record('block_tbs_entry', $upd)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Adds together a date (unixtime) and a time (hh:mm)
 *
 * @param $date integer date in seconds since epoch
 * @param $time string time in hh:mm format
 * @return integer date/time in seconds since epoch
 */
function time_to_datetime($date, $time) {
    global $cfg_tbs;
    list($hours, $mins) = explode(':', $time);
    $hours = intval($hours);
    $mins = intval($mins);
    if ($cfg_tbs->enable_periods && $hours == 0 && $mins < count($cfg_tbs->periods)) {
        $hours = 12; // Periods are imported as  P1 - 00:00, P2 - 00:01, P3 - 00:02, etc.
        // but stored internally as P1 - 12:00, P2 - 12:01, P3 - 12:02, etc.
    }
    return $date + 60 * $mins + 3600 * $hours;
}

/**
 * Returns a human readable tbs time from a unix timestamp.
 * If periods are enabled then gives the name of the period starting at this time
 * Will probably break is some idiot has more than 59 periods per day (seems very unlikely though)
 *
 * @param $time integer unix timestamp
 * @return string either the time formatted as hh:mm or the name of the period starting at this time
 */
function to_hr_time($time) {
    $cfg_tbs = get_config('block/tbs');
    if ($cfg_tbs->enable_periods) {
        $period = intval(date('i', $time));
        return $cfg_tbs->periods[$period];
    } else {
        return date('G:i', $time);
    }
}
