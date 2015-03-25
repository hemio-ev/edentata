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

function elementUsername() {
    return document.getElementById("form_login_username");
}

function elementPassword() {
    return document.getElementById("form_login_password");
}

function elementForm() {
    return document.getElementById("form_login");
}

function elementWindowLogin() {
    return document.getElementById("js_window");
}

function elementMessageLoginFailed() {
    return document.getElementById("message_login_failed");
}

document.addEventListener(
        "DOMContentLoaded",
        function () {
            console.log("Event: Document loaded");
            elementForm().onsubmit =
                    function () {
                        console.log("Event: Submit form");
                        runHttpAuth();
                        return false;
                    };
            elementWindowLogin().setAttribute("data-js-hidden", "");
            elementUsername().focus();
        }
);

function runHttpAuth() {
    var username = elementUsername().value;
    var password = elementPassword().value;
    httpAuth("/?auth=http", username, password);
}

function httpAuth(url, username, password) {

    var client = new XMLHttpRequest();
    client.open("GET", url, true, username, password);
    client.send();

    client.onreadystatechange = function () {
        if (this.readyState === this.HEADERS_RECEIVED) {
            console.log("HTTP Login Answer (" + this.status + ") " + this.statusText);
            if (this.status === 200) {
                location.reload();
            } else if (this.status === 401) {
                elementMessageLoginFailed().
                        setAttribute("data-js-hidden", "");

                elementPassword().value = "";
                elementPassword().focus();
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
