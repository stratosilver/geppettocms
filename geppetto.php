<?php
session_start();
//define ('EDITABLE', '<!--EDITABLE-->');
//define ('END_EDITABLE', '<!--END EDITABLE-->');
define ('BASE_URL', $_SERVER['HTTP_HOST']);
define ('URL_SCHEME', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http");

define ('THIS_FILE', pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME));

if(isset($_GET['page']))
    define ('CURENT_PATH', pathinfo($_GET['page'], PATHINFO_BASENAME));
else
    define ('CURENT_PATH', '');

include('geppetto/User.php');
include('geppetto/View.php');
include('geppetto/config.php');



// Check login
//----------------------------------------------------------------------------------------------------------------------
if(!isset($_SESSION['user']['name'])){
    if(isset($_POST['login'])) $login = strip_tags($_POST['login']); else $login = '';
    if(isset($_POST['password'])) $password = $_POST['password']; else $password = '';

    $user = new User();
    $message = array();
    if(isset($_POST['submit'])){
        if($user->login($login, $password)){
            $_SESSION['user']['name'] = $login;
        }
        else{
            $message['type'] = 'danger';
            $message['text'] = 'Login or password is incorrect';
        }

    }
    elseif(isset($_POST['create'])){
        if( isset($_POST['password2']) &&
            $_POST['password2'] != '' &&
            isset($password) &&
            $password != '' &&
            isset($login) &&
            $login != '' &&
            $_POST['password2'] == $password) {
            $user->add($login, $password);

            if ($user->add($login, $password)) {
                $_SESSION['user']['name'] = $login;
            } else {
                $message['type'] = 'danger';
                $message['text'] = 'Login or password is incorrect';
            }
        }
        else{
            $message['type'] = 'danger';
            $message['text'] = 'Login missing or password do not match';
        }
    }

    if(count($user->users)){
        echo View::login($login, $message);
    }
    else{
        echo View::newUser($login, $message);
    }

    exit();
}

// Post
//----------------------------------------------------------------------------------------------------------------------
if(isset($_POST['page'])){
    editPart();
}

// Open page
//----------------------------------------------------------------------------------------------------------------------
if(isset($_GET['page'])){
    $page = $_GET['page'];
}
else{
    if(file_exists('index.html')){
        $page = 'index.html';
    }
    elseif(file_exists('index.htm')){
        $page = 'index.htm';
    }
    elseif(file_exists('index.php')){
        $page = 'index.php';
    }
    else{
        // ask user to select the main file
    }
}

//echo BASE_URL.$page;

$pageContent = file_get_contents(pageToFile($page));
$pageContent = makeEditable($pageContent, $page);

$script = '<script>'.file_get_contents('geppetto.js').'</script>';
$pageContent = str_replace('</body>',$script.'</body>', $pageContent);
echo $pageContent;

function makeEditable($content, $page){
    $counter = 0;
    $editForm = '<form method="post" action="geppetto.php">'."\n";
    $editForm .= '<input type="hidden" name="page" value="'.$page.'">'."\n";
    $content = preg_replace_callback(
        pattern: '/editable/',
        callback: function ($matches) use (&$counter, &$editForm) {
            $counter++;
            $editForm .= '<input type="hidden" name="edit_field_geppetto_'.$counter.'" id="edit_field_geppetto_'.$counter.'" value="">'."\n";
            return 'id="geppetto_'.$counter.'" contenteditable';
        },
        subject: $content
    );
    $editForm .= '<input type="submit" value="Save" style="position:fixed; width:100px; height:60px; bottom:40px; right:40px;">'."\n";
    $editForm .= '</form>'."\n";
    $content = str_replace('</body>',$editForm.'</body>', $content);

    $content = addAlienInLinks($content);
    return($content);
}

function addAlienInLinks(string $content): string{
    $dom = new DOMDocument;
    @$dom->loadHTML($content);
    foreach ($dom->getElementsByTagName('a') as $node)
    {
        $link = $node->getAttribute("href");
        //echo $link.'<br>';
        $splitedLink = parse_url($link);
        if(isset($splitedLink['path'])
          && (substr($splitedLink['path'],0,1) == '/' || substr($splitedLink['path'],0,1) == '\\') ){
            $link = '/'.THIS_FILE.'?page='.$splitedLink['path'];
        }
        elseif(isset($splitedLink['path'])){
            $link = '/'.THIS_FILE.'?page='.CURENT_PATH.$splitedLink['path'];
        }
        else{
            @$link = '/'.THIS_FILE.'?page='.CURENT_PATH.$splitedLink['path'];
        }

        $node->setAttribute("href", $link);
    }
    return $dom->saveHTML();
}

function editPart(){
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTMLFile($_POST['page']);

    $xpath = new DOMXPath($doc);
    $nodes = $xpath->query('//*[@editable="true"]');

    $index = 1;
    foreach ($nodes as $node) {
        echo $index.' ';
        echo $_POST['edit_field_geppetto_'.$index].' <br>';
        if($_POST['edit_field_geppetto_'.$index] != ""){
            $node->nodeValue = $_POST['edit_field_geppetto_'.$index];
        }
        $index++;
    }

// Enregistre le fichier modifié
    $doc->saveHTMLFile($_POST['page']);
}


function pageToFile($page){
    if(substr($page, 0, 1) == '/' ||
        substr($page, 0, 1) == '\\'){
        $fileToOpen = substr($page, 1);
    }
    else{
        $fileToOpen = $page;
    }
    return($fileToOpen);
}



