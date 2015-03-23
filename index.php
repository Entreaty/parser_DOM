<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<pre>
<?php
/**
 * get_links()
 *@author Jay Gilford
 * @param string $url
 * @return array
 */
function get_links($url) {

    // Create a new DOM Document to hold our webpage structure
    $xml = new DOMDocument();

    // Load the url's contents into the DOM
    $xml->loadHTMLFile($url);
    echo '!!!!!!'.$xml->xmlEncoding.'<br>';
    echo '!!!!!!'.$xml->encoding.'<br>';
    // Empty array to hold all links to return
    $links = array();

    //Loop through each <a> tag in the dom and add it to the link array
    foreach($xml->getElementsByTagName('a') as $link) {
        $links[] = array('url' => $link->getAttribute('href'), 'text' => trim($link->nodeValue));
    }

    //Return the links
    return $links;
}
$links=@get_links("http://kirovsky.tms.sudrf.ru/modules.php?name=docum_sud&rid=6");
print_r($links);
?>

