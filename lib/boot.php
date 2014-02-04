<?php
/**
 * This file is part of the Framework of Paradise (FoP).
 * FOP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FoP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FOP.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package FoP
 * @author Alex Lydiate <alexlydiate@gmail.com>
 * @copyright  Copyright (c) 2014, Alex Lydiate
 * @license http://www.gnu.org/licenses/gpl.html
 */

require('database.php');
require('router.php');
require('environment.php');

foreach (glob(ROOTDIR . "/app/config/*.php") as $filename)
{
    require $filename;
}

foreach (glob(ROOTDIR . "/app/lib/*.php") as $filename)
{
    require $filename;
}

foreach (glob(ROOTDIR . "/base/*.php") as $filename)
{
    require $filename;
}

foreach (glob(ROOTDIR . "/app/controllers/*.php") as $filename)
{
    require $filename;
}

foreach (glob(ROOTDIR . "/app/models/*.php") as $filename)
{
    require $filename;
}