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

namespace hemio\edentata;

/**
 * Description of Window
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Window {

    /**
     *
     * @var Module
     */
    public $module;

    /**
     *
     * @var ModuleDb
     */
    protected $db;

    /**
     * 
     * @param \hemio\edentata\Module $module
     */
    public function __construct(Module $module) {
        $this->module = $module;
        $this->db = new module\email\Db($module->pdo);
    }

    /**
     * 
     * @return ModuleDb
     */
    public function db() {
        return $this->db;
    }

    public function newWindow(
    $title = null
    , $subtitle = null
    , $addOverviewButton = true
    ) {
        $window = new gui\WindowModule($title, $subtitle);
        $window->setModule($this->module);
        $window->addOverviewButton($addOverviewButton);
        return $window;
    }

    public function newFormWindow(
    $formName
    , $title = null
    , $subtitle = null
    , $submitText = null
    , $addOverviewButton = true
    ) {
        $window = new gui\WindowModuleWithForm($title, $subtitle);
        $window->setModule($this->module);

        $form = new gui\FormPost($formName, $this->module->request->post);
        $window->setForm($form);

        if ($submitText) {
            $submitButton = new \hemio\form\FieldSubmit('submit', $submitText);
            $submitButton->setForm($form);
            $window->addButtonRight($submitButton);
        }

        if ($addOverviewButton)
            $window->addOverviewButton($addOverviewButton);

        return $window;
    }

}
