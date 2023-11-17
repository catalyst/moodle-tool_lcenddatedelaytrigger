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

namespace tool_lcenddatedelaytrigger\lifecycle;

global $CFG;
require_once($CFG->dirroot . '/admin/tool/lifecycle/trigger/lib.php');

use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\response\trigger_response;
use tool_lifecycle\settings_type;
use tool_lifecycle\trigger\base_automatic;
use tool_lifecycle\trigger\instance_setting;

defined('MOODLE_INTERNAL') || die();

class trigger extends base_automatic {

    public function get_subpluginname()
    {
        return 'tool_lcenddatedelaytrigger';
    }

    public function get_plugin_description() {
        return "End date delay trigger";
    }

    public function check_course($course, $triggerid)
    {
        return trigger_response::trigger();
    }

    public function get_course_recordset_where($triggerid) {
        // Get the delay from the settings
        $delay = settings_manager::get_settings($triggerid, settings_type::TRIGGER)['delay'];

        // Recent course end date.
        $where = "{course}.enddate < :enddatedelay";
        $params = array(
            "enddatedelay" => time() - $delay,
        );

        // Return sql and params.
        return array($where, $params);
    }

    public function instance_settings() {
        return array(
            new instance_setting('delay', PARAM_INT, true)
        );
    }

    public function extend_add_instance_form_definition($mform) {
        // Duration setting.
        $mform->addElement('duration', 'delay', get_string('delay', 'tool_lcenddatedelaytrigger'));
        $mform->addHelpButton('delay', 'delay', 'tool_lcenddatedelaytrigger');
    }

    public function extend_add_instance_form_definition_after_data($mform, $settings) {
        if (is_array($settings) && array_key_exists('delay', $settings)) {
            $default = $settings['delay'];
        } else {
            $default = WEEKSECS;
        }
        $mform->setDefault('delay', $default);
    }

}
