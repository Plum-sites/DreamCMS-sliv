<?php

const FOLDERS = [
    'bootstrap/cache',
    'public',
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
];

const INFO = 'info';
const SUCCESS = 'success';
const WARNING = 'warning';
const ERROR = 'error';

if (!file_exists('.env')){
    msg("PLEASE, MOVE .env.example TO .env AND CONFIGURE IT", ERROR);
}

msg_header("CHECK FOLDERS");
checkOrCreateFolders(FOLDERS);

msg_header("INSTALL PHP PACKAGES");
runCheckedCommand("composer update");

msg_header("CONFIGURE NPM CONFIG");
runCheckedCommand("npm config set \"@fortawesome:registry\" https://npm.fontawesome.com/ && npm config set \"//npm.fontawesome.com/:_authToken\" 41BE0BEE-5007-4E17-8810-7429F2D610D3");

msg_header("INSTALL NPM PACKAGES");
runCheckedCommand("npm install");

msg_header("BUILD FRONTEND");
msg("It can take some time...", WARNING);
runCheckedCommand("npm run prod");
msg("Frontend build success!", SUCCESS);

msg_header("BUILD ADMIN FRONTEND");
msg("It can take some time...", WARNING);
runCheckedCommand("cd vuexy && npm run prod");
msg("Admin frontend build success!", SUCCESS);

msg_header("RUN MIGRATIONS");
runCheckedCommand("php artisan migrate");

msg_header("GENERATE SECRET KEYS");
runCheckedCommand("php artisan key:generate");
runCheckedCommand("php artisan jwt:secret");

msg_header("FINISHED", SUCCESS);
msg("Congratulations! You can start using your site!", SUCCESS);

function getInput(){
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    return trim($line);
}

function runCheckedCommand($cmd){
    $output = [];
    $exitCode = 1;
    exec($cmd, $output, $exitCode);

    if ($exitCode == 0){
        msg("Command [" . $cmd . "] exit code: " . $exitCode);
    }else{
        msg("Command [" . $cmd . "] failed, exit code: " . $exitCode, ERROR);
        die();
    }
}

function checkOrCreateFolders($folders){
    foreach ($folders as $folder){
        if (file_exists($folder)){
            if (is_dir($folder)){
                $perms = fileperms($folder);
                if (($perms & 0x0100) && ($perms & 0x0080) && ($perms & 0x0020) && ($perms & 0x0010) && ($perms & 0x0004) && ($perms & 0x0002)){
                    msg("Dir " . $folder . " is ok", SUCCESS);
                }else{
                    msg("Dir " . $folder . " has wrong rights, fixing using linux chmod", WARNING);
                    exec('chmod -R 777 "' . $folder . '"');
                }
            }else{
                msg($folder . " is not directory, removing (rm -rf) and creating", WARNING);
                exec('rm -rf "' . $folder . '"');
                createWrittablePath($folder);
            }
        }else{
            msg("Dir " . $folder . " doesn't exists, creating...", INFO);
            createWrittablePath($folder);
        }
    }
}

function createWrittablePath($path){
    umask(0777);
    mkdir($path, 0777, true);
    exec('chmod -R 777 "' . $path . '"');
}

function msg_header($msg, $level = INFO){
    msg("------------------------- " .$msg . " -------------------------", $level);
}

function msg($msg, $level = INFO){
    $colors = new Colors();

    switch ($level){
        case INFO:
            echo $colors->getColoredString($msg, 'white', 'black') . PHP_EOL;
            break;
        case SUCCESS:
            echo $colors->getColoredString($msg, 'light_green', 'black') . PHP_EOL;
            break;
        case WARNING:
            echo $colors->getColoredString($msg, 'black', 'yellow') . PHP_EOL;
            break;
        case ERROR:
            echo $colors->getColoredString($msg, 'black', 'red') . PHP_EOL;
            break;
    }
}

class Colors {
    private $foreground_colors = array();
    private $background_colors = array();

    public function __construct() {
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    // Returns colored string
    public function getColoredString($string, $foreground_color = null, $background_color = null) {
        $colored_string = "";

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }
}