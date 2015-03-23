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
 * Description of Utils
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Utils
{

    public static function getPost()
    {
        $input = file_get_contents('php://input');

        if ($input === '')
            return [];

        $pairs = explode('&', $input);
        $post  = [];
        foreach ($pairs as $pair) {
            $nv          = explode('=', $pair);
            $name        = urldecode($nv[0]);
            $value       = urldecode($nv[1]);
            $post[$name] = $value;
        }

        return $post;
    }

    public static function htmlRedirect(Request $request)
    {
        usleep(100 * 1000);
        header(
            sprintf('Location: %s', $request->getUrl())
            , true
            , 303
        );
        exit(0);
    }

    public static function sysExec($command, $pipe = '')
    {
        $descriptorspec = [
            0 => ['pipe', 'r'], // STDIN
            1 => ['pipe', 'w'] // STDOUT
        ];

        $pipes   = [];
        $process = proc_open($command, $descriptorspec, $pipes);

        if (!is_resource($process))
            throw new exception\Error('Failed to proc_open().');

        $status = proc_get_status($process);
        if (
            $status === false ||
            $status['running'] === false
        )
            throw new exception\Error('Process is not running.');

        fwrite($pipes[0], $pipe);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $returnStatus = proc_close($process);

        if ($returnStatus === -1) {
            throw new exception\Error('Internal error on proc_close().');
        } elseif ($returnStatus !== 0) {
            throw new exception\Error('External error on proc_close().');
        }

        return $stdout;
    }

    public static function fmtDate(\DateTime $date)
    {
        return strftime('%x', $date->getTimestamp());
    }

    public static function fmtDateTime(\DateTime $date)
    {
        return strftime('%x %X', $date->getTimestamp());
    }
}
