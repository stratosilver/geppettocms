<?php
session_start();

include('geppetto/User.php');
include('geppetto/View.php');

define ('BASE_URL_GEPPETTO', $_SERVER['HTTP_HOST']);
define ('URL_SCHEME', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http");
define ('THIS_FILE', pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME));


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
elseif(isset($_GET['logoff'])){
    $user = new User();
    $user->logoff();
}



// Which page to edit
if(isset($_GET['page']) && $_GET['page'] != '' && $_GET['page'] != '/'){
    // Security, remove leading / or ..
    $page = ltrim($_GET['page'], './\\');
    define ('CURENT_PATH', pathinfo($_GET['page'], PATHINFO_BASENAME));
}
// Set default page to edit
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

    define ('CURENT_PATH', '');
}


// Post
//----------------------------------------------------------------------------------------------------------------------
if(isset($_POST['page']) && $_POST['page'] != '' && $_POST['page'] != '/'){
    editPart($page);
}

// Open page
//----------------------------------------------------------------------------------------------------------------------

//echo BASE_URL.$page;

$pageContent = file_get_contents($page);
$pageContent = makeEditable($pageContent, $page);

$script = '<script>'.file_get_contents('geppetto/geppetto.js').'</script>';
$pageContent = str_replace('</body>',$script.'</body>', $pageContent);
echo $pageContent;

// End
// ---------------------------------------------------------------------------------------------------------------------
function makeEditable($content, $page){
    $counter = 0;
    $editForm = '<form method="post" action="geppetto.php?page='.$page.'">'."\n";
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

    $content = convertLinks($content);
    return($content);
}

function convertLinks(string $content): string{
    $dom = new DOMDocument;
    @$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    foreach ($dom->getElementsByTagName('a') as $node)
    {
        $link = $node->getAttribute("href");
        //echo $link.'<br>';
        $splitedLink = parse_url($link);
        //var_dump($splitedLink);
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

function editPart($page){
    $html = file_get_contents($page);
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    //$doc->loadHTMLFile($page, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $xpath = new DOMXPath($doc);
    $nodes = $xpath->query('//*[@editable="true"]');

    $index = 1;
    foreach ($nodes as $node) {
        //echo $index.' ';
        //echo $_POST['edit_field_geppetto_'.$index].' <br>';
        if(isset($_POST['edit_field_geppetto_'.$index]) && $_POST['edit_field_geppetto_'.$index] != ""){
            //echo $_POST['edit_field_geppetto_'.$index];
            $htmlCode = strip_tags(nl2br($_POST['edit_field_geppetto_'.$index]), '<br>');

            $tmpDoc = new DOMDocument();
            @$tmpDoc->loadHTML('<?xml encoding="utf-8" ?><div>' . $htmlCode . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);


            // 5. Importer les nœuds du document temporaire dans le document principal
            $fragment = $doc->createDocumentFragment();
            foreach ($tmpDoc->getElementsByTagName('div')->item(0)->childNodes as $tmpNode) {
                $importedNode = $doc->importNode($tmpNode, true); // Importer le nœud avec ses enfants
                $fragment->appendChild($importedNode);
            }

            // 6. Vider le contenu existant du nœud cible (optionnel, si vous voulez remplacer)
            while ($node->hasChildNodes()) {
                $node->removeChild($node->firstChild);
            }

            // 7. Ajouter le fragment au nœud cible
            $node->appendChild($fragment);

        }
        $index++;
    }

// Enregistre le fichier modifié
    $doc->saveHTMLFile($page);
}

