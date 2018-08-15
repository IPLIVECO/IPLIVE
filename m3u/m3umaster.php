<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
$search = $_GET["title"];
//array of m3u lists to search through
$lists = array('movielinks.m3u','movielist.m3u','tvlist.m3u','tvlinks3.m3u');
foreach ($lists as $list) {
    search($list, $search);
}
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
   $string = preg_replace('/-+/', '-', $string);
   return str_replace('-', ' ', $string); // Replaces multiple hyphens with single one.
}
function url_exists($url) {
ini_set('default_socket_timeout', 2);
if(@file_get_contents($url,0,NULL,0,1))
{return $url;}
else
{return NULL;}
}


function customSearch($keyword, $arrayToSearch){
    foreach($arrayToSearch as $key => $arrayItem){
        if( stristr( $arrayItem, $keyword ) ){
            return $key;
        }
    }
}


function search($list, $search){
$string = file_get_contents($list);
preg_match_all('/(?P<tag>#EXTINF:-1)|(?:(?P<prop_key>[-a-z]+)=\"(?P<prop_val>[^"]+)")|(?<title>,[^\r\n]+)|(?<url>http[^\s]+)/', $string, $match );
$count = count( $match[0] );
$result = [];
$index = -1;
for( $i =0; $i < $count; $i++ ){
    $item = $match[0][$i];
    if( !empty($match['tag'][$i])){
        //is a tag increment the result index
        ++$index;
    }elseif( !empty($match['prop_key'][$i])){
        //is a prop - split item
        $result[$index][$match['prop_key'][$i]] = $match['prop_val'][$i];
    }elseif( !empty($match['title'][$i])){
        //is a prop - split item
        $result[$index]['title'] = $item;
    }elseif( !empty($match['url'][$i])){
        $result[$index]['url'] = $item ;
    }elseif( !empty($match['url'][$i])){
        $result[$index]['url'] = $item ;
    }
}
$json = json_encode($result);
$dec = json_decode($json);
foreach ($dec as $channels => $object) {
$name = str_replace(',','',clean(strtolower($object->title)));
$searchArray = explode(' ', strtolower($search));
$nameArray = explode(' ', $name);
$searchCount = count($searchArray);
$matches = 0;
foreach ($searchArray as $key) {
    if(strpos($name, $key) !== false){
        $matches++;
    }
if($matches == $searchCount){
$url = url_exists($object->url);
if($url !== null){
echo '<a href='.'"'.$url.'"'.">$name</a><br>";}
}
}
//$chance = similar_text(strtolower($search), strtolower($name), $perc);
}
}
//print_r( $result );
?>