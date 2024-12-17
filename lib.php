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
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 */
function block_fastnav_pluginfile($course, $recordorcm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $CFG;

    if ($context->contextlevel !== CONTEXT_BLOCK) {
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
            && !core_course_category::get(id: $parentcontext->instanceid, strictness: IGNORE_MISSING)) {
            send_file_not_found();
        }
    }

    $fs = get_file_storage();
    $itemid = (int) array_shift(array: $args);
    $filename = array_pop(array: $args);

    $file = $fs->get_file(
        contextid: $context->id,
        component: 'block_fastnav',
        filearea: $filearea,
        itemid: $itemid,
        filepath: '/',
        filename: $filename
    );

    if (!$file || $file->is_directory()) {
        send_file_not_found();
    }

    \core\session\manager::write_close();
    send_stored_file(storedfile: $file, forcedownload: true, options: $options);
}

/**
 * Defines the user preferences for the Fast Navigation block.
 *
 * @return array
 */
function block_fastnav_user_preferences(): array {
    $preferences = [];

    // Define the 'block_fastnav_open' preference.
    $preferences['block_fastnav_open'] = [
        'type' => PARAM_INT,            // Using PARAM_INT to accept integer values.
        'null' => NULL_NOT_ALLOWED,     // Null values are not allowed.
        'default' => 0,                 // Default value is 0 (closed).
        'choices' => [0, 1],            // Valid values are 0 (closed) and 1 (open).
    ];

    return $preferences;
}


