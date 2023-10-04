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

namespace hemio\edentata\gui;

use hemio\html;
use hemio\form;
use hemio\edentata\exception;

/**
 * Description of ContentMessage
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class Message extends form\Container
{

    public function __construct(exception\Printable $event)
    {
        $this['article'] = new html\Article;
        $this['article']->addCssClass('message');

        if ($event instanceof exception\Error)
            $this['article']->setAttribute('role', 'alert');

        if ($event instanceof exception\Warning)
            $this['article']->addCssClass('warning');

        $this['article']['h2']   = new html\H2();
        $this['article']['h2'][] = new html\Str($event::title());

        $this['article']['p']   = new html\P();
        $this['article']['p'][] = new html\Str($event->getMessage());

        if ($event->backTo !== null)
            $backUrl = $event->backTo;
        else
            $backUrl = new \hemio\edentata\Request();

        $button = new LinkButton($backUrl, _('OK'));
        $button->getButton()->setAttribute('autofocus', true);
        $button->setSuggested();

        $this['article']['button_group']           = new ButtonGroup();
        $this['article']['button_group']['button'] = $button;
    }

    /**
     *
     * @return html\Str
     */
    public function getButtonString()
    {
        return $this['article']['button_group']['button']->getButtonString();
    }
}
