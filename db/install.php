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

function xmldb_block_tbs_install() {
    global $DB;

    // Get system context.
    $context = context_system::instance();

    // Create the viewer role.
    if (!$DB->record_exists('role', array('shortname' => 'tbsviewer'))) {
        $tbsviewerid = create_role(get_string('tbsviewer', 'block_tbs'), 'tbsviewer',
                                    get_string('tbsviewer_desc', 'block_tbs'));
        set_role_contextlevels($tbsviewerid, array(CONTEXT_SYSTEM));
        assign_capability('block/tbs:viewtbs', CAP_ALLOW, $tbsviewerid, $context->id, true);
    }

    // Create the editor role.
    if (!$DB->record_exists('role', array('shortname' => 'tbseditor'))) {
        $tbseditorid = create_role(get_string('tbseditor', 'block_tbs'), 'tbseditor',
                                    get_string('tbseditor_desc', 'block_tbs'));
        set_role_contextlevels($tbseditorid, array(CONTEXT_SYSTEM));
        assign_capability('block/tbs:viewtbs', CAP_ALLOW, $tbseditorid, $context->id, true);
        assign_capability('block/tbs:edittbs', CAP_ALLOW, $tbseditorid, $context->id, true);
    }

    // Create the admin role.
    if (!$DB->record_exists('role', array('shortname' => 'tbsadmin'))) {
        $tbsadminid = create_role(get_string('tbsadmin', 'block_tbs'), 'tbsadmin',
                                   get_string('tbsadmin_desc', 'block_tbs'));
        set_role_contextlevels($tbsadminid, array(CONTEXT_SYSTEM));
        assign_capability('block/tbs:viewtbs', CAP_ALLOW, $tbsadminid, $context->id, true);
        assign_capability('block/tbs:edittbs', CAP_ALLOW, $tbsadminid, $context->id, true);
        assign_capability('block/tbs:administertbs', CAP_ALLOW, $tbsadminid, $context->id, true);
        assign_capability('block/tbs:viewalltt', CAP_ALLOW, $tbsadminid, $context->id, true);
        assign_capability('block/tbs:forcebook', CAP_ALLOW, $tbsadminid, $context->id, true);
        assign_capability('block/tbs:doublebook', CAP_ALLOW, $tbsadminid, $context->id, true);
    }

    // Clear any capability caches
    $context->mark_dirty();
}
