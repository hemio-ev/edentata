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

/**
 * Description of WindowModule
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class WindowModule extends Window {

    /**
     *
     * @var \hemio\edentata\Module
     */
    public $module;

    public function setModule(\hemio\edentata\Module $module) {
        $this->module = $module;
    }

    /**
     * 
     * @return \hemio\edentata\gui\LinkButton
     */
    public function addOverviewButton() {
        $button = new LinkButton($this->module->request->derive(), _('Overview'));
        $button->setBack();
        $this->addButtonLeft($button);
        return $button;
    }

}
