<!DOCTYPE HTML>
<html lang="sv">
<head>
<meta charset="windows-1252" />
<meta name="robots" content="index,follow" />
<meta name="author" content="Christer Svensson" />
<meta name="description" content="Easy Real Time Full Text Search, PHP Script, Free Download" />
<meta name="keywords" content="PHP, Easy, Real Time, Full Text Search, PHP Script, Free Download" />
<meta name="generator" content="http://www.christersvensson.com/html-tool/" />
<title>Easy Real Time Full Text Search PHP Script</title>
<style type="text/css">
body { width:800px; margin:auto; font-family:Arial,Helvetica,sans-serif; font-size:14px; }
#webpath { color:#228B22; } 
</style>
</head>
<body>

<?php
/* Easy Real Time Full Text Search � 2012 Christer Svensson

---------------------------------------------- Instructions -------------------------------------------------------------------------------------------

 If the name of this file not is easy.php then rename it to easy.php

 Upload it to the directory (folder) on your homepage server that you want to search through.

 The PHP-script in this file performs a real time full text search of every htm, html, asp,
 and php file in the directory (folder) and all subdirectories (subfolders). The search is
 not case sensitive.

 The text displayed in the browser window is searched. For that reason, in the html code, all
 text in tags are omitted and also text between the tag and the end tag for head, script, noscript
 and iframe.

 You may translate the text (string values) in this file to your native language without the
 author's permission. If you do so, please send a copy to postbox@christersvensson.com and it
 will be published on the homepage of this script.

 You may also use another CSS style and you may include the script as a natural part at pages at
 your site.

*/

// You may translate the string values here under to your native languages. 

$buttonvalue = "Search";
$search_at = "Search on";
$search_result = "gave this result";
$pages = "Number of pages with hits";
$to_small = "At least two characters is required";
$recursive = true;  // Change to false if no searching should be done in subdirectories.

//---------------------------- Do not change anything below this line -------------------------------------------------------------------------------

$html = <<<HTML
<p><br /></p>
<form name="form" action="">
<input type="text" name="search" size="30" /> 
<input type="button" value="$buttonvalue" 
 onclick='window.location.assign(document.URL.substring(0,document.URL.indexOf("?")) + "?search=" + document.form.search.value.replace(/ /g,"%20"))' />
</form>
<!-- Please do not remove or change this link to the application's site. Others might like it too. -->

HTML;

echo $html;

function textpart($body, $search) {
// Displays the text after the title
  $length = 30;
  $text = substr($body, max(stripos($body,$search) - $length, 0), strripos($body,$search) - stripos($body,$search) + strlen($search) + 2 * $length);
  if (strripos($text, " ") < strripos($text,$search)) {
    $text = $text . " ";
  }
  if (stripos($text, " ") != strripos($text, " ")) {
    $text = substr($text, stripos($text, " "), strripos($text, " ") - stripos($text, " "));
  }
  $temp = $text;
  $stop = substr($text, strripos($text, $search) + strlen($search));
  if (strlen($stop) > $length) {
    $stop = substr($text, strripos($text, $search) + strlen($search), $length);
    $stop = substr($stop, 0, strripos($stop, " "));
  }
  $text = "... ";
  while (stripos($temp,$search)) {
    $temp = substr_replace($temp, "<b>", stripos($temp, $search), 0);
    $temp = substr_replace($temp, "</b>", stripos($temp, $search) + strlen($search), 0);
    $text = $text . substr($temp, 0, stripos($temp, "</b>") + 4);
    $temp = substr($temp, stripos($temp, "</b>") + 4);
    if(stripos($temp, $search) > (2 * $length)) {
       $text = $text . substr($temp, 0, $length);
       $text = substr($text, 0, strripos($text, " ")) . " ... ";
       $temp = substr($temp, stripos($temp, $search) - $length);
       $temp = substr($temp, stripos($temp, " "));
    }
  }
  $text = $text . $stop . " ... ";
  echo $text; 
  return;
}

function compress($string, $first, $last) {
// Removes everything in $string from $first to $last including $first and $last
  while(stripos($string,$first) && stripos($string,$last)) {
    $string = substr_replace($string, "", stripos($string,$first), stripos($string,$last) - stripos($string,$first) + strlen($last));
  }
  return $string;  
}

function directoryToArray($directory, $recursive) {
// This function by XoloX was downloaded from http://snippets.dzone.com/user/XoloX
  $array_items = array();
  if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) {
      if ($file != "." && $file != "..") {
        if (is_dir($directory. "/" . $file)) {
          if($recursive) {
            $array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
          }
        } else {
          $file = $directory . "/" . $file;
          $array_items[] = preg_replace("/\/\//si", "/", $file);
        }
      }
    }
    closedir($handle);
  }
  return $array_items;
}

function filewalk($file, $search, $counter, $webpath) {
// Selects and treats files with the extension .htm and .html and .asp and .php
  if (strtolower(substr($file, stripos($file, ".htm"))) == ".htm"
      || strtolower(substr($file, stripos($file, ".html"))) == ".html"
      || strtolower(substr($file, stripos($file, ".rtf"))) == ".rtf"
      || strtolower(substr($file, stripos($file, ".asp"))) == ".asp"
      || strtolower(substr($file, stripos($file, ".php"))) == ".php") {
    $all = file_get_contents($file);
    $body = substr($all, stripos($all,"<body"),stripos($all,"</body>") - stripos($all,"<body"));
    $body = preg_replace('/<br \/>/i', ' ', $body);
    $body = preg_replace('/<br>/i', ' ', $body);
    $body = compress($body,"<noscript","</noscript>");
    $body = compress($body,"<script","</script>");
    $body = compress($body,"<iframe","</iframe>");
    $body = compress($body,"<noframe","</noframe>");
    $body = strip_tags($body);
    $body = html_entity_decode($body, ENT_QUOTES);
    $body = preg_replace('/\s+/', ' ', $body);
    // Scans and displays the results
    if (stripos($body, $search)) {
      $title = substr($all, stripos($all,"<title>") + 7,stripos($all,"</title>") - stripos($all,"<title>") - 7);
      $title = html_entity_decode($title, ENT_QUOTES);
      $title = preg_replace('/\s+/', ' ', $title); 
    //  echo '<p><a href="' . $file . '">' . $title . '</a></br>';
      echo '<span id="webpath">' . $webpath . substr($file, stripos($file, "/")) . '</span><br />';
      echo textpart($body, $search) . '</p>';
      $counter = $counter + 1;
    }
  }
  return $counter;
}

// Reads the search text from the page's URL
$url = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
$url .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"] : $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

if (stripos($url,"?search=")) $search = $_GET['search'];

$webpath = dirname($url);

// Starts searching
if (strlen($search) < 2 && trim($search) <> "") {
  echo '<p>' . $to_small . '!</p>';
  $search = "";
}

if (trim($search) <> "") {
  echo "<p>" . $search_at . " '<b>" . $search . "</b>' " . $search_result . ".</p>";
  $counter = 0;
  // Path to the folder containing this file
  $curdir = getcwd();
  // Opens the folder and read its contents
  if ($dir = opendir($curdir)) {
    $files = directoryToArray("./", $recursive);
    foreach ($files as $file) {
      $counter = filewalk($file, $search, $counter, $webpath);
    }
    closedir($dir);
  }
  echo "<p>" . $pages . ": " . $counter . "</p>";
}
?>

</body>
</html>

