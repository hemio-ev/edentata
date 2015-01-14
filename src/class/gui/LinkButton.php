<?php

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

namespace hemio\edentata\gui;

use hemio\html;
use \hemio\form;

/**
 * Description of LinkButton
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class LinkButton extends form\Container {

    public function __construct(array $url, $text) {
        $this['form'] = new html\Form();

        foreach ($url as $key => $value) {
            $input = new html\Input('hidden');
            $input->setAttribute('name', $key);
            $input->setAttribute('value', $value);
            $this['form']->addChild($input);
        }

        $this['form']['button'] = new html\Button();
        $this['form']['button']['text'] = new html\String($text);
    }

}
