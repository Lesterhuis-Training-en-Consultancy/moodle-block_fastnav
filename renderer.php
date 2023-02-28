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
 * Renderer UI class
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

use block_fastnav\output\output_items;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

/**
 * Class block_fastnav_renderer
 *
 * @package   block_fastnav
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class block_fastnav_renderer extends plugin_renderer_base {

    /**
     * get_management_buttons
     *
     * @param block_fastnav $block
     *
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_management_buttons(\block_fastnav $block) : string {
        return html_writer::link(new moodle_url('/blocks/fastnav/view/edit.php', [
            'instanceid' => $block->instance->id,
            'courseid' => $block->page->course->id,
        ]),
            get_string('btn:edit', 'block_fastnav'), [
                'class' => 'btn btn-primary',
            ]);
    }

    /**
     * get_edit_link_table
     *
     * @return string
     * @throws coding_exception
     */
    public function get_edit_link_table() : string {

        $table = new block_fastnav\table\table_links(__CLASS__, $this->page->context->instanceid);
        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('class', 'generaltable generalbox reporttable');
        $table->initialbars(true);
        $table->define_baseurl($this->page->url);

        // Set columns.
        $columns = [
            'icon',
            'name',
            'link',
            'action',
        ];

        $table->define_columns($columns);
        $table->define_headers(array_map(static function ($val) {
            return get_string('heading:table_' . $val, 'block_fastnav');
        }, $columns));

        $table->sortable(false, SORT_ASC);
        $table->collapsible(false);

        $table->out(100, true);

        return ob_get_clean();
    }

    /**
     * get_edit_button
     *
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_edit_button() : string {
        $params = $this->page->url->params();

        return $this->render_from_template('block_fastnav/edit_management', [
            'link' => (new moodle_url('/blocks/fastnav/view/edit.php', array_merge($params, ['action' => 'edit'])))->out(false),
        ]);
    }

    /**
     * get_block_item_list
     *
     * @param context_block $context
     *
     * @return string
     * @throws moodle_exception
     */
    public function get_block_item_list(context_block $context) : string {

        $output = new output_items($context);
        $templatedata = $output->export_for_template($this);

        if (empty($templatedata->items)) {
            return '';
        }

        return $this->render_from_template('block_fastnav/block_item_list', $templatedata);
    }

}
