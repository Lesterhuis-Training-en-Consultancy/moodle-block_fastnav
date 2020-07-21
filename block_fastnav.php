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
 * @package   moodle-block_fastnav
 * @copyright 16/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/
defined('MOODLE_INTERNAL') || die;

/**
 * Class block_fastnav
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodle-block_fastnav
 * @copyright 16/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class block_fastnav extends block_base {

    /**
     * @throws coding_exception
     */
    public function init() : void {
        $this->title = get_string('pluginname', 'block_fastnav');
    }

    /**
     * Subclasses should override this and return true if the
     * subclass block has a settings.php file.
     *
     * @return boolean
     */
    public function has_config() : bool {
        return true;
    }

    /**
     * Which page types this block may appear on.
     *
     * The information returned here is processed by the
     * {@link blocks_name_allowed_in_format()} function. Look there if you need
     * to know exactly how this works.
     *
     * Default case: everything except mod and tag.
     *
     * @return array page-type prefix => true/false.
     */
    public function applicable_formats() : array {
        return ['all' => true];
    }

    /**
     * @throws coding_exception
     */
    public function specialization() : void {
        if (isset($this->config->title)) {
            $this->title = $this->title = format_string($this->config->title, true, ['context' => $this->context]);

            return;
        }

        $this->title = get_string('pluginname', 'block_fastnav');
    }

    /**
     * Are you going to allow multiple instances of each block?
     * If yes, then it is assumed that the block WILL USE per-instance configuration
     *
     * @return boolean
     */
    public function instance_allow_multiple() : bool{
        return false;
    }

    /**
     * Parent class version of this function simply returns NULL
     * This should be implemented by the derived class to return
     * the content object.
     *
     * @return stdObject
     * @throws coding_exception
     */
    public function get_content() {
        global $CFG , $PAGE;

        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        /** @var block_fastnav_renderer $renderer **/
        $renderer = $PAGE->get_renderer('block_fastnav');

        if(has_capability('block/fastnav:management', $this->context)){
            $this->content->text .= $renderer->get_management_buttons($this);
        }

        $menuitems = $renderer->get_block_item_list($this->context);

        if(!empty($menuitems)){
            $this->content->text .= $menuitems;
            $PAGE->requires->js_call_amd('block_fastnav/sidebar', 'init', [[
                'contextid' => $this->context->id,
            ]]);
        }

        return $this->content;
    }

    /**
     * Serialize and store config data
     *
     * @param      $data
     * @param bool $nolongerused
     */
    public function instance_config_save($data, $nolongerused = false) : void {

        $config = clone($data);
        // Move embedded files into a proper filearea and adjust HTML links to match
//        $config->text = file_save_draft_area_files($data->text['itemid'], $this->context->id,
//            'block_fastnav', 'content', 0, ['subdirs' => true], $data->text['text']);
//        $config->format = $data->text['format'];

        parent::instance_config_save($config, $nolongerused);
    }

    /**
     * @return bool
     */
    public function instance_delete() : bool {
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_fastnav');

        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     *
     * @param int $fromid the id number of the block instance to copy from
     *
     * @return boolean
     */
    public function instance_copy($fromid) : bool {
        $fromcontext = context_block::instance($fromid);
        $fs = get_file_storage();
        // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
        if (!$fs->is_area_empty($fromcontext->id, 'block_fastnav', 'content', 0, false)) {
            $draftitemid = 0;
            file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_fastnav', 'content', 0, ['subdirs' => true]);
            file_save_draft_area_files($draftitemid, $this->context->id, 'block_fastnav', 'content', 0, ['subdirs' => true]);
        }

        return true;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() : bool {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }
}
