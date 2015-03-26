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

use hemio\edentata\gui;
use hemio\form;
use hemio\html;

/**
 * Description of Window
 *
 * @author Michael Herold <quabla@hemio.de>
 */
abstract class Window
{
    /**
     *
     * @var Request
     */
    protected $request;

    /**
     *
     * @param \hemio\edentata\Module $module
     */
    public function __construct(Module $module)
    {
        $this->module  = $module;
        $this->db      = $module->db;
        $this->request = $module->request;
    }

    public function newWindow(
    $title = null
    , $subtitle = null
    , $addOverviewButton = true
    )
    {
        $window = new gui\WindowModule($title, $subtitle);
        $window->setModule($this->module);

        if ($addOverviewButton)
            $window->addOverviewButton();

        return $window;
    }

    public function newForm($formName)
    {
        return new gui\FormPost($formName, $this->module->request->post);
    }

    public function newFormWindow(
    $formName
    , $title = null
    , $subtitle = null
    , $submitText = null
    , $addOverviewButton = true
    )
    {
        $window = new gui\WindowModuleWithForm($title, $subtitle);
        $window->setModule($this->module);

        $form = $this->newForm($formName);
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

    public function newDeleteWindow(
    $formName
    , $title
    , $subtitle
    , $message
    , $deleteText
    , $protectedDelete = false
    )
    {
        $window = new gui\WindowModuleWithForm($title, $subtitle);
        $window->setModule($this->module);
        $window->addCssClass('delete_dialog');

        $form = $this->newForm($formName);
        $window->setForm($form);

        $msg = new html\P();
        $msg->addCssClass('text');
        $msg->addChild(new html\String($message));

        if ($protectedDelete) {
            $switch = new gui\FieldSwitch('enable_delete', _('Permit Deletion'));
            $switch->setForm($form);
            $switch->getControlElement()->addCssClass('delete_perimition');
            $switch->setRequired();


            $hint = new html\P;
            $hint->addCssClass('hint');
            $hint->addChild(new html\String(_('You must activate the switch before you can submit')));
        }

        $cancelButton = new gui\LinkButton(
            $this->module->request->derive()
            , _('Cancel')
        );
        $cancelButton->getButton()->setAttribute('autofocus', true);

        $deleteButton = new form\FieldSubmit('delete', $deleteText);
        $deleteButton->setForm($form);
        $deleteButton->setAutofocus(false);

        $buttonGroup = new gui\ButtonGroup();
        $buttonGroup->addChild($deleteButton);
        $buttonGroup->addChild($cancelButton);

        $window->addChild($msg);
        if ($protectedDelete) {
            $window->addChild($switch);
            $window->addChild($hint);
        }
        $window['mid'] = new form\Container();
        $window->addChild($buttonGroup);

        return $window;
    }
}
