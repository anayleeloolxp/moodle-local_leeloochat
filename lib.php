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

/**
 * Function to get Leeloo Install
 *
 * @return string leeloo url
 */
function local_leeloochat_get_leelooinstall() {

    global $SESSION;
    global $CFG;
    require_once($CFG->dirroot . '/lib/filelib.php');

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
    global $SESSION;

    @$useremail = $USER->email;

    if ($useremail != '' && $useremail != 'root@localhost' && !is_siteadmin()) {
        $leeloolxpurl = local_leeloochat_get_leelooinstall();

        if ($leeloolxpurl == 'no') {
            return true;
        }

        $leeloolxplicense = get_config('local_leeloochat')->license;
        $leeloopage = get_config('local_leeloochat')->pageurl;

        $x = '<img src=\"' . new moodle_url('/local/leeloochat/icons/close.png') . '\"/>';
        $f = '<img src=\"' . new moodle_url('/local/leeloochat/icons/column.png') . '\"/>';
        $p = '<img src=\"' . new moodle_url('/local/leeloochat/icons/full-page.png') . '\"/>';
        $w = '<img src=\"' . new moodle_url('/local/leeloochat/icons/widget.png') . '\"/>';

        $pgbtn = '';
        if ($leeloopage != '') {
            $pgbtn = '<span onclick=\"show_page(\'' . $leeloopage . '\');\">' . $p . '</span>';
        }

        $PAGE->requires->js('/local/leeloochat/js/widget.js');

        $jsessionid = $SESSION->jsession_id;

        $frameurl = "https://leeloolxp.com/wespher_support_system/?view=snippet&user=" .
            base64_encode($USER->id) .
            "&token=" .
            $leeloolxplicense .
            "&jsessionid=" .
            $jsessionid;

        $wespherchattitle = '<span class=\"wespher_chat_title\" onclick=\"show_full();\">' .
            get_string('widget_title', 'local_leeloochat') .
            '</span>';

        $closeframe = '<span onclick=\"close_frame();\">' . $x . '</span>';

        $btns = '<div class=\"wespherbuttonsdiv\"><span onclick=\"show_half();\">' .
            $w .
            '</span><span onclick=\"show_full();\">' .
            $f .
            '</span>' .
            $pgbtn .
            $closeframe .
            '</div>';

        $frame = '<iframe id=\"wespher_widget_frame\" class=\"wespher_widget\" src=\"' . $frameurl . '\"></iframe>';

        $js2 = '
        var z = document.createElement("div");
        z.setAttribute("class", "wespher_widget_div l_hidden");
        z.setAttribute("id", "wespher_widget_div");

        z.innerHTML = "<div class=\"wespher_chat\">' . $wespherchattitle . $btns . '</div>' . $frame . '";
        document.body.appendChild(z);

        var navleeloolxpchatcontainer = document.getElementById("navleeloolxpchatcontainer");
        if(navleeloolxpchatcontainer){
            document.getElementById("navleeloolxpchatcontainer").classList.remove("l_hidden");
        }

        ';

        $PAGE->requires->js_init_code("$js2");
    }
}
