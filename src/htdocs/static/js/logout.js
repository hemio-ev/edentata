/* 
 * Copyright (C) 2015 Michael Herold <quabla@hemio.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

"use strict";

function elementLogoutSuccessful() {
    return document.getElementById("message_logout_successful");
}

document.addEventListener(
        "DOMContentLoaded",
        function () {
            console.log("Event: Document loaded");
            elementWindowLogin().setAttribute("data-js-hidden", "data-js-hidden");
            httpLogout('?auth=http_logout');
        }
);

function httpLogout(url) {

    var client = new XMLHttpRequest();
    client.open("GET", url, true, "*", "*");
    client.send();

    client.onreadystatechange = function () {
        if (this.readyState === this.HEADERS_RECEIVED) {
            console.log("HTTP Logout Answer (" + this.status + ") " + this.statusText);
            if (this.status === 401) {
                elementLogoutSuccessful().setAttribute("data-js-hidden", "");
            } else {
                alert(
                        "A unknown error occured: " +
                        "(" + this.status + ") "
                        + this.statusText
                        );
            }
        }
    };
}