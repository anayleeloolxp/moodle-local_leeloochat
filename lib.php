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
 * Libary file .
 *
 * @package     local_leeloochat
 * @copyright  2020 Leeloo LXP (https://leeloolxp.com)
 * @author     Leeloo LXP <info@leeloolxp.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(dirname(__DIR__)) . '/config.php');

/**
 * Function to get Leeloo Install
 *
 * @return string leeloo url
 */
function local_leeloochat_get_leelooinstall() {

    global $SESSION;

    if (isset($SESSION->chatleelooinstall)) {
        return $SESSION->chatchatleelooinstall;
    }

    $leeloolxplicense = get_config('local_leeloochat')->license;

    $url = 'https://leeloolxp.com/api_moodle.php/?action=page_info';
    $postdata = [
        'license_key' => $leeloolxplicense,
    ];

    $curl = new curl;
    $options = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_HEADER' => false,
        'CURLOPT_POST' => count($postdata),
    );

    if (!$output = $curl->post($url, $postdata, $options)) {
        $chatchatleelooinstallurl = 'no';
        $SESSION->chatchatleelooinstall = $chatchatleelooinstallurl;
    }

    $infoteamnio = json_decode($output);
    if ($infoteamnio->status != 'false') {
        $chatchatleelooinstallurl = $infoteamnio->data->install_url;
        $SESSION->chatchatleelooinstall = $chatchatleelooinstallurl;
    } else {
        $chatchatleelooinstallurl = 'no';
        $SESSION->chatchatleelooinstall = $chatchatleelooinstallurl;
    }

    return $chatchatleelooinstallurl;
}

/**
 * HTML hook to add the restrictions on unpaid A/R.
 */
function local_leeloochat_before_footer() {

    global $USER;
    global $PAGE;
    global $DB;
    global $CFG;
    global $SESSION;

    @$useremail = $USER->email;

    if ($useremail != '' && $useremail != 'root@localhost' && !is_siteadmin()) {
        $leeloolxpurl = local_leeloochat_get_leelooinstall();

        if ($leeloolxpurl == 'no') {
            return true;
        }

        $leeloolxplicense = get_config('local_leeloochat')->license;
        $leeloopage = get_config('local_leeloochat')->pageurl;

        $pagebutton = '';
        if ($leeloopage != '') {
            $pagebutton = '<span onclick=\"show_page(\''.$leeloopage.'\');\">P</span>';
        }

        $PAGE->requires->js('/local/leeloochat/js/widget.js');

        $jsessionid = $SESSION->jsession_id;

        $frameurl = "https://leeloolxp.com/wespher_support_system/?view=snippet&user=" . base64_encode($USER->id) . "&token=" . $leeloolxplicense . "&jsessionid=" . $jsessionid;

        $js2 = '
        var z = document.createElement("div"); // is a node
        z.setAttribute("class", "wespher_widget_div");

        z.innerHTML = "<div class=\"wespher_chat\"><span class=\"wespher_chat_title\" onclick=\"show_widget();\">' . get_string('widget_title', 'local_leeloochat') . '</span><div class=\"wespherbuttonsdiv\"><span onclick=\"show_widget();\">W</span><span onclick=\"show_full();\">F</span>' . $pagebutton . '<span onclick=\"close_frame();\">X</span></div></div><iframe id=\"wespher_widget_frame\" class=\"wespher_widget\" src=\"' . $frameurl . '\" style=\"display:none;\"></iframe>";
        document.body.appendChild(z);';

        $PAGE->requires->js_init_code("$js2");
    }
}
