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

$instanceid = required_param(parname: 'instanceid', type: PARAM_INT);
$courseid = required_param(parname: 'courseid', type: PARAM_INT);
$action = optional_param(parname: 'action', default: '', type: PARAM_ALPHA);
$id = optional_param(parname: 'id', default: false, type: PARAM_INT);

$blockcontext = context_block::instance($instanceid);

// Security check.
require_capability(capability: 'block/fastnav:management', context: $blockcontext);

$PAGE->set_context($blockcontext);
$PAGE->set_url(url: '/blocks/fastnav/view/edit.php', params: [
    'instanceid' => $instanceid,
    'courseid' => $courseid,
    'action' => $action,
    'id' => $id,
]);

$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout(pagelayout: 'course');

$parentcourse = $DB->get_record(table: 'course', conditions: ['id' => $courseid], strictness: MUST_EXIST);
$PAGE->navbar->add(ucfirst($parentcourse->fullname), new moodle_url(url: '/course/view.php', params: ['id' => $parentcourse->id]));
$PAGE->navbar->add(get_string(identifier: 'heading:overview', component: 'block_fastnav'));

$renderer = $PAGE->get_renderer(component: 'block_fastnav');
$item = new \block_fastnav\item($id);

$baseurl = clone $PAGE->url;
$baseurl->param(paramname: 'action', newvalue: '');
$baseurl->param(paramname: 'id', newvalue: '');

switch ($action) {

    case 'up':
        $item->change_sortorder(direction: -1);
        redirect($baseurl);
        break;

    case 'down':
        $item->change_sortorder(direction: 1);
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
