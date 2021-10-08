<?php

//TODO! Some Authorization - Whitelisted IP, Security tokens...

echo '<pre>
   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/  UPDATE
                    /_/

';
define('ROOT_DIR',realpath('/home/{USER}/subdomains/{domain}'));
define('EXTRACT_DIRECTORY', ROOT_DIR. '/composer');
define('HOME_DIRECTORY', ROOT_DIR. '/composer/home');
define('COMPOSER_INITED', file_exists(ROOT_DIR.'/vendor'));

set_time_limit(100);
ini_set('memory_limit',-1);

if (!getenv('HOME') && !getenv('COMPOSER_HOME')) {
    putenv("COMPOSER_HOME=".HOME_DIRECTORY);
}

if (file_exists(EXTRACT_DIRECTORY.'/vendor/autoload.php') == true) {
    echo "Extracted autoload already exists. Skipping phar extraction as presumably it's already extracted.\n";
}
else{
    $composerPhar = new Phar("/home/{USER}/composer.phar");

    //php.ini set phar.readonly=0
    $composerPhar->extractTo(EXTRACT_DIRECTORY);
}

// change directory to root
chdir(ROOT_DIR);

//This requires the phar to have been extracted successfully.
require_once (EXTRACT_DIRECTORY.'/vendor/autoload.php');

//Use the Composer classes
use Composer\Console\Application;
use Composer\Command\UpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;


//Create the commands
$args = array('command' => 'update');
if(!COMPOSER_INITED) {
    echo "This is first composer run: --no-scripts option is applies\n";
    $args['--no-scripts']=true;
}
$input = new ArrayInput($args);

//Create the application and run it with the commands
$application = new Application();
$application->setAutoExit(false);
$application->setCatchExceptions(false);


try {
    //Running commdand php.ini allow_url_fopen=1 && proc_open() function available
    $application->run($input);
    echo 'Success';
} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
