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
 * Block instance
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 16/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

use block_fastnav\helper;

/**
 * Class block_fastnav
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 16/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class block_fastnav extends block_base {

    /**
     * Init
     *
     */
    public function init(): void {
        $this->title = get_string(identifier: 'pluginname', component: 'block_fastnav');
    }

    /**
     * Subclasses should override this and return true if the
     * subclass block has a settings.php file.
     *
     * @return boolean
     */
    public function has_config(): bool {
        return true;
    }

    /**
     * Which page types this block may appear on.
     *
     * The information returned here is processed by the
     * to know exactly how this works.
     *
     * Default case: everything except mod and tag.
     *
     * @return array page-type prefix => true/false.
     */
    public function applicable_formats(): array {
        return ['all' => true];
    }

    /**
     * Specialization
     *
     */
    public function specialization(): void {
        if (isset($this->config->title)) {
            $this->title = format_string(string: $this->config->title, options: ['context' => $this->context]);

            return;
        }

        $this->title = get_string(identifier: 'pluginname', component: 'block_fastnav');
    }

    /**
     * Parent class version of this function simply returns NULL
     * This should be implemented by the derived class to return
     * the content object.
     *
     * @return object
     */
    public function get_content(): object {
        global $CFG, $USER;
        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $renderer = $this->page->get_renderer(component: 'block_fastnav');

        // Allow ajax call.
        $USER->ajax_updatable_user_prefs['block_fastnav_open'] = true;

        if (has_capability(capability: 'block/fastnav:management', context: $this->context)
            && $this->page->user_is_editing()) {
            $this->content->text .= $renderer->get_management_buttons($this);
        }

        $menuitems = $renderer->get_block_item_list($this->context);

        if (empty($menuitems)) {
            return $this->content;
        }

        if ($this->can_display_block()) {
            $this->content->text .= $menuitems;
        }

        if ($this->can_display_sidebar()) {
            $this->page->requires->js_call_amd(fullmodule: 'block_fastnav/sidebar', func: 'init', params: [
                [
                    'instanceid' => $this->context->instanceid,
                    'open' => get_user_preferences(name: 'block_fastnav_open'),
                ],
            ]);
        }

        return $this->content;
    }

    /**
     * Instance delete
     *
     * @return bool
     */
    public function instance_delete(): bool {
        global $DB;

        $fs = get_file_storage();
        $fs->delete_area_files(contextid: $this->context->id, component: 'block_fastnav');
        $DB->delete_records(table: 'block_fastnav', conditions: ['contextid' => $this->context->id]);

        return true;
    }

    /**
     * Do any additional initialization you may need at the time a new block instance is created
     *
     * @return boolean
     */
    public function instance_create(): bool {
        global $DB, $COURSE;

        if (!empty($COURSE->id) && $COURSE->id > 1) {
            // Update default to course-*.
            $DB->update_record(table: 'block_instances', dataobject: (object) [
                'id' => $this->instance->id,
                'pagetypepattern' => '*', // Any page.
                'showinsubcontexts' => 1,
            ]);
        }

        // Make visible on all pages.
        $config = new stdClass();
        $config->text = '';
        $config->format = FORMAT_HTML;
        $config->display_modus = helper::SHOW_BLOCK_AND_SIDEBAR;

        $this->instance_config_save($config);

        return true;
    }

    /**
     * Can display sidebar
     *
     * @return bool
     */
    private function can_display_sidebar(): bool {
        $displaymodus = (int) $this->config->display_modus;

        if ($displaymodus === helper::SHOW_BLOCK_ONLY) {
            return false;
        }

        return true;
    }

    /**
     * Can display block
     *
     * @return bool
     */
    private function can_display_block(): bool {
        $displaymodus = (int) $this->config->display_modus;

        if ($displaymodus === helper::SHOW_BLOCK_ONLY) {
            return true;
        }

        if ($displaymodus === helper::SHOW_BLOCK_AND_SIDEBAR) {
            return true;
        }

        return false;
    }

}
