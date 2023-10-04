<?php

/*
 * Copyright (C) 2015 Sophie Herold <sophie@hemio.de>
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

namespace hemio\edentata;

use \hemio\html\Interface_\HtmlCode;

/**
 * Description of ContentEvents
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class ContentEvents {

    /**
     *
     * @var HtmlCode
     */
    private $content;

    /**
     *
     * @var array 
     */
    private $events = [];

    /**
     * 
     * @param HtmlCode $content
     */
    public function __construct(HtmlCode $content) {
        $this->content = $content;
    }

    public function addEvent(exception\Event $event) {
        $this->events[] = $event;
    }

    /**
     * 
     * @return boolean
     */
    public function unhandledEvents() {
        return count($this->events) > 0;
    }

    /**
     * 
     * @throws exception\Event
     */
    public function handleEvent() {
        throw array_shift($this->events);
    }

    /**
     * 
     * @return HtmlCode
     * @throws exception\Event
     */
    public function getContent() {
        while ($this->unhandledEvents()) {
            $this->handleEvent();
        }

        return $this->content;
    }

}
