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

namespace hemio\edentata\sql;

use hemio\edentata\exception;

/**
 * Description of ExceptionMapping
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ExceptionMapping extends ExceptionMapper
{

    /**
     *
     * @param exception\SqlSpecific $e
     * @throws exception\Error
     * @throws exception\SqlSpecific
     */
    public function map(exception\SqlSpecific $e)
    {
        switch ($e->getMessage()) {
            case 'user:login_invalid':
                return $this->error(
                        _('Invalid unser login.')
                        , 1001
                        , $e
                );

            case 'system:no_contingent':
            case 'system:contingent_not_owner':
            case 'system:contingent_total_exceeded':
            case 'system:contingent_domain_exceeded':
                return $this->error(
                        _('The operation you want to perform would exceed your current contingent.'
                            .' Please contact the support to extend your contingent.')
                        , 1002
                        , $e
                );

            case 'commons:inaccessible_or_missing':
                return $this->error(
                        _('The object you tried to change is inaccessible or missing.')
                        , 1003
                        , $e
                );

            default:
                return $e;
        }
    }
}
