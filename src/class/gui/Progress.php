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
use hemio\form;
use hemio\edentata;

/**
 * Description of Pending
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Progress extends form\Container implements ProgressInterface
{
    protected $msg;
    protected $status;

    public static function enhanceWithStatusExplanation(
    html\Interface_\MaintainsChilds $content
    , edentata\Request $request)
    {
        $filter = function (\hemio\html\Interface_\HtmlCode $child) {
            return $child instanceof ProgressInterface;
        };

        $status = [];
        foreach ($content->getRecursiveIterator($filter) as $progress) {
            if ($progress->isPending()) {
                $status[$progress->getStatus()] = $progress->getMessage();
            }
        }

        if (empty($status))
            return new html\Nothing;

        $event         = new edentata\exception\Warning(
            sprintf(
                _('There are elements in this view with status %s. '
                    .'These tasks should be processed by the system soon. '
                    .'If the status remains unchanged please contact the support. '
                    .'This view will NOT be refreshed automatically.')
                , implode(', ', $status)
            )
        );
        $event->backTo = $request;

        $msg = new Message($event);
        $msg->getButtonString()->setValue(_('Refresh View'));

        return $msg;
    }

    public function __construct($backendStatus)
    {
        $this->status = $backendStatus;

        if ($backendStatus !== null) {

            $this['span'] = new html\Span();
            $this['span']->addCssClass('status');

            if ($backendStatus != 'old')
                $this['span']->addCssClass('progress');

            switch ($backendStatus) {
                case 'del':
                    $this->msg = _('Deletion Pending');
                    break;

                case 'upd':
                    $this->msg = _('Changes Pending');
                    break;

                case 'ins':
                    $this->msg = _('Setup Pending');
                    break;

                case 'old':
                    $this->msg = _('Deleted');
                    break;

                default:
                    $this->msg    = _('Unknown Status');
                    $this->status = '?';
            }

            $this['span']->addChild(new html\Str($this->msg));
        }
    }

    public function getMessage()
    {
        return $this->msg;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isPending()
    {
        return $this->status !== null && $this->status !== 'old';
    }
}
