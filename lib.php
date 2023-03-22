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
 * Block lib functions
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 17/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

/**
 * Download files
 *
 * @param stdClass $course
 * @param stdClass $recordorcm
 * @param stdClass $context
 * @param string   $filearea
 * @param array    $args
 * @param bool     $forcedownload
 * @param array    $options
 *
 * @throws coding_exception
 * @throws moodle_exception
 * @throws require_login_exception
 */
function block_fastnav_pluginfile($course, $recordorcm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $CFG;

    if ($context->contextlevel != CONTEXT_BLOCK) {
        send_file_not_found();
    }

    // If block is in course context, then check if user has capability to access course.
    if ($context->get_course_context(false)) {
        require_course_login($course);
    } else if ($CFG->forcelogin) {
        require_login();
    } else {

        // Get parent context and see if user have proper permission.
        $parentcontext = $context->get_parent_context();
        // Check if category is visible and user can view this category.
        if (($parentcontext->contextlevel === CONTEXT_COURSECAT)
            && !core_course_category::get($parentcontext->instanceid, IGNORE_MISSING)) {
            send_file_not_found();
        }
    }

    $fs = get_file_storage();
    $itemid = (int)array_shift($args);
    $filename = array_pop($args);

    if (!$file = $fs->get_file($context->id, 'block_fastnav', $filearea, $itemid, '/',
            $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    \core\session\manager::write_close();
    send_stored_file($file, null, 0, true, $options);
}
