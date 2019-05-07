<?php
require './vendor/autoload.php';

use Nicu\Console\BuildCommand;

(new BuildCommand($argv))->run();
