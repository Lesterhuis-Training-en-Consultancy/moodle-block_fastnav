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
 * Edit fastnav items (links)
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 17/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/
require_once(__DIR__ . '/../../../config.php');
require_login();

$instanceid = required_param('instanceid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', false, PARAM_INT);

$blockcontext = context_block::instance($instanceid);

// Security check.
require_capability('block/fastnav:management', $blockcontext);

$PAGE->set_context($blockcontext);
$PAGE->set_url('/blocks/fastnav/view/edit.php', [
    'instanceid' => $instanceid,
    'courseid' => $courseid,
    'action' => $action,
    'id' => $id,
]);

$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('course');

$parentcourse = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$PAGE->navbar->add(ucfirst($parentcourse->fullname), new moodle_url('/course/view.php', ['id' => $parentcourse->id]));
$PAGE->navbar->add(get_string('heading:overview', 'block_fastnav'));

$renderer = $PAGE->get_renderer('block_fastnav');
$item = new \block_fastnav\item($id);

$baseurl = clone $PAGE->url;
$baseurl->param('action', '');
$baseurl->param('id', '');

switch ($action) {

    case 'up':
        $item->change_sortorder(-1);
        redirect($baseurl);
        break;

    case 'down':
        $item->change_sortorder(1);
        redirect($baseurl);
        break;

    case 'edit':
        $form = new \block_fastnav\form\form_edit_item($PAGE->url, [
            'item' => $item,
            'blockcontext' => $blockcontext,
        ]);

        if ($item->exists()) {
            $form->set_data($item->get_data());
        }

        if ($form->is_cancelled()) {
            redirect($baseurl);
        }

        if ($formdata = $form->get_data()) {

            $formdata->id = $item->get_id();
            $item->save($formdata, $blockcontext);

            redirect($baseurl);
        }

        echo $OUTPUT->header();
        echo $form->render();
        echo $OUTPUT->footer();

        break;

    case 'delete':

        $item->delete();

        redirect($baseurl);
        break;

    default:

        echo $OUTPUT->header();
        echo $renderer->get_edit_button();
        echo $renderer->get_edit_link_table();
        echo $OUTPUT->footer();

}
