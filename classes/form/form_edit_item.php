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
 * Edit link form
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav\form;
defined('MOODLE_INTERNAL') || die;

global $CFG;

use block_fastnav\helper;

require_once($CFG->libdir . '/formslib.php');

/**
 * Class form_edit_item
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class form_edit_item extends \moodleform {

    /**
     * @var mixed
     */
    private $item;

    /**
     * @var mixed
     */
    private $blockcontext;

    /**
     * The constructor function calls the abstract function definition() and it will then
     * process and clean and attempt to validate incoming data.
     *
     * It will call your custom validate method to validate data and will also check any rules
     * you have specified in definition using addRule
     *
     * The name of the form (id attribute of the form) is automatically generated depending on
     * the name you gave the class extending moodleform. You should call your class something
     * like
     *
     * @param mixed $action     the action attribute for the form. If empty defaults to auto detect the
     *                          current url. If a moodle_url object then outputs params as hidden variables.
     * @param mixed $customdata if your form defintion method needs access to data such as $course
     *                          $cm, etc. to construct the form definition then pass it in this array. You can
     *                          use globals for somethings.
     *
     */
    public function __construct($action = null, $customdata = null) {
        parent::__construct($action, $customdata);

        // Init.
        $this->blockcontext = $customdata['blockcontext'];
        $this->item = $customdata['item'];
    }

    /**
     * Form definition
     *
     * @throws \coding_exception
     */
    protected function definition() : void {
        $mform = &$this->_form;

        $mform->addElement('text', 'name', get_string('form:name', 'block_fastnav'), []);
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'link', get_string('form:link', 'block_fastnav'), []);
        $mform->setType('link', PARAM_URL);

        $mform->addElement('filepicker', 'link_icon',
            get_string('form:link_icon', 'block_fastnav'), null, helper::get_file_options());

        // Rules.
        $mform->addRule('name', get_string('required'), 'required', 255, 'client');
        $mform->addRule('link', get_string('required'), 'required', 255, 'client');
        $mform->addRule('link', get_string('required'), 'required', 255, 'client');

        $this->add_action_buttons(false, get_string('btn:update', 'block_fastnav'));
    }

    /**
     * After data
     *
     * definition_after_data
     */
    public function definition_after_data() : void {

        $draftitemid = file_get_submitted_draft_itemid('link_icon');
        file_prepare_draft_area(
            $draftitemid,
            $this->blockcontext->id,
            'block_fastnav',
            'link_icon',
            $this->item->get_id(),
            helper::get_file_options()
        );

        // Set data.
        $this->set_data([
            'link_icon' => $draftitemid,
        ]);
    }
}