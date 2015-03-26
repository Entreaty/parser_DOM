<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<pre>
   <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

       Insert URL:<br/>
       <input type="text" name="source" placeholder="URL here..." style="width:430px"
              value="<?= $_POST['source'] ?>"/><br/>
       <br/>
       <input type="submit" value="REAP DATA!"/>

   </form>

    <?php
    $TIME_START = explode(' ', microtime());
    $SOCKET_TIME_OUT = 180000;
    function get_RID_links($url)
    {

        // Create a new DOM Document to hold our webpage structure
        $xml = new DOMDocument();

        // Load the url's contents into the DOM
        $xml->loadHTMLFile($url);

        // Empty array to hold all links to return
        $links = array();

        // Empty ID
        $maxLink = null;

        foreach ($xml->getElementsByTagName('div') as $link) {

            if ('menuArea' == $link->getAttribute('class')) {
                if (($maxLink == null) || ($maxLink->getAttribute('id') < $link->getAttribute('id'))) {
                    $maxLink = $link;
                }
            }
        }
        foreach ($maxLink->getElementsByTagName('a') as $a) {
            $links[] = array('url' => $a->getAttribute('href'),
                'text' => trim($a->nodeValue));
        }
        //Loop through each <a> tag in the dom and add it to the link array

//    foreach($xml->getElementsByTagName('a') as $link) {
//
//        if(preg_match('@.*?modules.php\\?name=docum_sud&rid=@',$link->getAttribute('href')))
//
//            $links[] = array('url' => $link->getAttribute('href'), 'text' => trim($link->nodeValue));
//    }


//        if(preg_match('@.*?modules.php\\?name=docum_sud&rid=@',$link->getAttribute('href')))

//        $links[] = array('url' => $link->getAttribute('href'), 'text' => trim($link->nodeValue));


        //Return the links
        return $links;
    }

    function get_ID_links($url)
    {

        // Create a new DOM Document to hold our webpage structure
        $xml = new DOMDocument();

        // Load the url's contents into the DOM
        $xml->loadHTMLFile($url);

        // Empty array to hold all links to return
        $links = array();

        //Loop through each <a> tag in the dom and add it to the link array
        foreach ($xml->getElementsByTagName('a') as $link) {

            if (preg_match('@.*?modules.php\\?name=docum_sud&id=@', $link->getAttribute('href')))
                /*Костыль id=822 - рекламная ссылка*/
                if ($link->getAttribute('href') !== "http://kirovsky.tms.sudrf.ru/modules.php?name=docum_sud&id=822") {

                    $links[] = array('url' => $link->getAttribute('href'), 'text' => trim($link->nodeValue));
                }
        }
        //Return the links
        return $links;
    }

    function get_TEXT($url, $nameOfFile,$path)
    {

        // Заменяем слэши на безобидные "__"
        if(strlen($nameOfFile)>100){
            $name=substr($nameOfFile, 0, 100);
        }else{
            $name = str_replace("/", "__", "$nameOfFile");
        }

        // Create a new DOM Document to hold our webpage structure
        $xml = new DOMDocument();

        // Load the url's contents into the DOM
        $xml->loadHTMLFile($url);

        // Empty array to hold all links to return
        $texts = array();

        //Loop through each <a> tag in the dom and add it to the link array
        foreach ($xml->getElementsByTagName('p') as $text) {

            file_put_contents($path.$name.'.txt', trim($text->nodeValue) . "\r\n", FILE_APPEND);
            echo $path.$name.'.txt <br>';
        }

        $handle = @fopen($path.$name.'.txt', "r");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgetss($handle, 4096);
            }
            fclose($handle);
        }
        //Return the links
        return $texts;
    }

    ?>

    <?
    $path = 'D:\TEEE\\';
    $sections = $_POST['razdels'];
    if (empty($sections)) {
        echo("YOU CHOOSE NOTHING" . '<br>');
    } else {
        $N = count($sections);

        echo ("You selected $N section(s): ") . '<br>';
        for ($i = 0; $i < $N; $i++) {
            echo ($sections[$i]) . '<br>';
            list($url, $name) = explode('|', $sections[$i]);
            echo "URL = $url, Name = $name" . '<br>';
            mkdir($path . $name, 0777);
            if ($id = @get_id_links('http://kirovsky.tms.sudrf.ru/' . $url)) {
                foreach ($id as $item) {
                   @get_TEXT('http://kirovsky.tms.sudrf.ru/'.$item['url'], $item['text'], $path.$name.'\\');
                }
            } else {
                $rid = @get_rid_links('http://kirovsky.tms.sudrf.ru/' . $url);
                foreach ($rid as $link) {
                    mkdir($path . $name . '\\' . $link['text'], 0777);
                    if ($id = @get_id_links('http://kirovsky.tms.sudrf.ru/' . $link['url'])) {
                        foreach ($id as $item) {
                            @get_TEXT('http://kirovsky.tms.sudrf.ru/' . $item['url'], $item['text'], $path . $name . '\\' . $link['text'] . '\\');
                        }
                    }
                }
                print_r($rid);
            }
        }
    }

    $list = @get_RID_links($_POST['source']);
    $TIME_END = explode(' ', microtime());
    print_r($list);
    echo $TIME_TOTAL = ($TIME_END[0]+$TIME_END[1])-($TIME_START[0]+$TIME_START[1]);

    ?>





    <hr>
  <form action="index.php" method="post">
      <?php
      foreach ($list as $razdel) {
          $name = $razdel['text'];
          $url = $razdel['url'];

          echo <<<L
<p><input type="checkbox" name="razdels[]" value="$url | $name" />$name</p>
L;
      }
      ?>



      <input type="submit" name="formSubmit" value="Submit"/>

  </form>




<?
//print_r($library);
//$_SERVER['DOCUMENT_ROOT']='C:\getCont\\';
//print_r($library2);
//echo __LINE__.'<br>'. __FILE__.'<br>'. __FUNCTION__.'<br>'. __DIR__.'<br>'. getcwd() ;
//@mkdir('C:\getCont\\');
//chdir('C:\getCont\\');
//echo __LINE__
//.'<br>'. __FILE__.'<br>'. __FUNCTION__.'<br>'. __DIR__.'<br>getcwd = '. getcwd().'<br>'.$_SERVER['DOCUMENT_ROOT'] ;
?>