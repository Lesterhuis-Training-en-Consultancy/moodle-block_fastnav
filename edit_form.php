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
 * Form for editing fastnav block instances.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 30/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

use block_fastnav\helper;

/**
 * Class block_fastnav_edit_form
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 30/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class block_fastnav_edit_form extends block_edit_form {

    /**
     * specific_definition
     *
     * @param object $mform
     *
     * @throws coding_exception
     */
    protected function specific_definition($mform) : void {

        $options = [
            helper::SHOW_BLOCK_AND_SIDEBAR => get_string('form:show_block_and_sidebar', 'block_fastnav'),
            helper::SHOW_SIDEBAR => get_string('form:show_sidebar', 'block_fastnav'),
            helper::SHOW_BLOCK_ONLY => get_string('form:show_block', 'block_fastnav'),
        ];

        $mform->addElement('select', 'config_display_modus', get_string('form:display_modus', 'block_fastnav'), $options);
        $mform->setType('config_display_modus', PARAM_INT);
    }
}
