<?php
define ('EDITABLE', '<!--EDITABLE-->');
define ('END_EDITABLE', '<!--END EDITABLE-->');
define ('BASE_URL', dirname($_SERVER['PHP_SELF']));
define ('THIS_FILE', pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME));
if(isset($_GET['page']))
    define ('CURENT_PATH', pathinfo($_GET['page'], PATHINFO_BASENAME));
else
    define ('CURENT_PATH', '');

// Check login
/*
if(!isset($_SESSION['user']['name'])){

}
*/
// Post
if(isset($_POST['page'])){
    editPart();
}

// Open page
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

$pageContent = file_get_contents(pageToFile($page));
$pageContent = makeEditable($pageContent, $page);
echo $pageContent;


function makeEditable($content, $page){
    $counter = 1;

    $content = preg_replace_callback(
        '/'.EDITABLE.'/',
        function ($matches) use (&$counter, &$page) {
            return View::topEditForm($page, $counter);
        },
        $content
    );

    $content = str_replace(END_EDITABLE, View::bottomEditForm($page), $content);

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
    $pageContent = file_get_contents(pageToFile($_POST['page']));
    $formInputCount = 1;
    $positions = 0;
    $lastPos = 0;

    // Find xeme text
    while (($lastPos = strpos($pageContent, EDITABLE, $lastPos))!== false) {
        $positions = $lastPos;
        $lastPos = $lastPos + strlen(EDITABLE);
        if($formInputCount == $_POST['formInputCount']){
            break;
        }
        $formInputCount++;
    }

    $bengiPage = substr($pageContent, 0, $positions);

    $endPage = substr($pageContent, strpos($pageContent, END_EDITABLE, $positions));

    $_POST['formInputContent'] = str_replace(EDITABLE, '', $_POST['formInputContent']);
    $_POST['formInputContent'] = str_replace(END_EDITABLE, '', $_POST['formInputContent']);

    $page = $bengiPage.EDITABLE.$_POST['formInputContent'].$endPage;

    //echo $page;exit();
    file_put_contents(pageToFile($_POST['page']), $page);
}

class View{
    static function topEditForm(&$page, &$counter){
        return('<form action="/alien.php?page='.$page.'" method="post" style="width: 100%;">
                        <input type="hidden" name="page" value="'.$page.'" />
                        <input type="hidden" name="formInputCount" value="'.$counter++.'" />
                        <textarea name="formInputContent" rows="10" style="background-color:#d6ffce; width: 100%;">');
    }

    static function bottomEditForm($page){
        return('</textarea>
            <br>
            <input type="submit" value="Save">
            &nbsp;&nbsp;<a href="'.$page.'">View</a>
            </form>');
    }
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