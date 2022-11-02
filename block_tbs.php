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

class block_tbs extends block_base {

    function init() {
        $this->title = get_string('blockname', 'block_tbs');
        $this->content_type = BLOCK_TYPE_TEXT;
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $cfg_tbs = get_config('block/tbs');

        $context = context_system::instance();

        if (has_capability('block/tbs:viewtbs', $context) or has_capability('block/tbs:edittbs', $context) or has_capability('block/tbs:administertbs', $context)) {
            if (isset($CFG->block_tbs_serverpath)) {
                $serverpath = $CFG->block_tbs_serverpath;
            } else {
                $serverpath = $CFG->wwwroot.'/blocks/tbs/web';
            }
            $go = get_string('accesstbs', 'block_tbs');
            $icon = $OUTPUT->pix_icon('web', 'TBS icon', 'block_tbs');
            $target = '';
            if ($cfg_tbs->newwindow) {
                $target = ' target="_blank" ';
            }
            $this->content = new stdClass();
            $this->content->text = '<a href="'.$serverpath.'/index.php" '.$target.'>'.$icon.' &nbsp;'.$go.'</a>';
            $this->content->footer = '';
            return $this->content;
        }

        return null;
    }

    function cron() {
        global $CFG;
        include($CFG->dirroot.'/blocks/tbs/import.php');

        return true;
    }
}
