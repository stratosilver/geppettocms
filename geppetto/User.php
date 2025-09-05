<?php

/*
* Class User
* ---------------------------------------------------------------------------------------------------------------------
*/

class User
{

    public array $users = array();
    public string $message = '';

    function __construct()
    {
        if (file_exists('geppetto/config.php')) {
            try {
                include('geppetto/config.php');
                if (isset($users) && is_array($users)) {
                    $this->users = $users;
                }
            } catch (Exception $e) {
                $this->message = 'Config file error';
                unlink('geppetto/config.php');
            }
        }
    }

    function logoff(){
        $_SESSION['user'] = array();
    }

    function login($login, $password): bool
    {
        return true;
        echo password_hash($password, PASSWORD_DEFAULT);
        if (isset($this->users[$login]))
            return (password_verify($password, $this->users[$login]));
        return false;
    }

    function add($login, $password): bool
    {
        $this->users[$login] = password_hash($password, PASSWORD_DEFAULT);
        return ($this->save());
    }

    private function save(): bool
    {
        $confFile = fopen('geppetto/config.php', 'w');
        fputs($confFile, "<?php\n");
        fputs($confFile, "\$users = [\n");
        foreach ($this->users as $user => $hash) {
            fputs($confFile, "'$user' => '$hash',\n");
        }
        fputs($confFile, "];\n");
        return (true);
    }

}