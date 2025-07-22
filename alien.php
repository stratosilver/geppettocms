<?php
define ('EDITABLE', '<!--EDITABLE-->');
define ('END_EDITABLE', '<!--END EDITABLE-->');
define ('BASE_URL', dirname($_SERVER['PHP_SELF']));

var_dump($_POST);
var_dump(BASE_URL);
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

$pageContent = file_get_contents($page);
$pageContent = makeEditable($pageContent, $page);
echo $pageContent;


function makeEditable($content, $page){
    $counter = 1;

    $content = preg_replace_callback(
        '/'.EDITABLE.'/',
        function ($matches) use (&$counter, &$page) {
            return '<form action="/alien.php" method="post" style="width: 100%;">
                        <input type="hidden" name="page" value="'.$page.'" />
                        <input type="hidden" name="formInputCount" value="'.$counter++.'" />
                        <textarea name="formInputContent" rows="10" style="background-color:#d6ffce; width: 100%;">';
        },
        $content
    );

    $content = str_replace(END_EDITABLE, '</textarea>
            <br>
            <input type="submit" value="Save">
            &nbsp;&nbsp;<a href="'.$page.'">View</a>
            </form>', $content);

    $content = addAlienInLinks($content);
    return($content);
}

function addAlienInLinks(string $content): string{
    
    return $content;
}

function editPart(){
    $pageContent = file_get_contents($_POST['page']);
    $formInputCount = 0;
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
    file_put_contents($_POST['page'], $page);
}
