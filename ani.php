<?php 
include "dom.php";
function getBetween($content, $start, $end)
{
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}
function getClassNamedFieldName($tempText, $num)
{
    $results = array();
	$i = 0;
	do
	{  
        // $ss = $tempText->find('a',$i);
        $ii = $tempText->find('a', $i);
		$tempItem = trim(getBetween($ii, '>', ' <span')); 
        // $tempCount = trim(getBetween($ss, 'count">(',')'));
        // $arr = array("count" => $tempCount);
		array_push($results, $tempItem);
        

		$i++;
	}
	while(getBetween($tempText->find('a', $i), '>', ' <span') != '');
    // $results = array(
    //    "array" => $tempCount
    // );
	return $results;
}
function getPage($url,$paged) {
$get = file_get_html($url."?paged=".$paged);
$itemx = $get->find("div[class=rapi]",0);
$output = array();

// countAnime
$pageCount = substr_count($get, "<h2 class='episodeye'>");
	$i = 0;	
	while ($i < $pageCount) {
		$item = $itemx->find("div[class=thumbz]",$i);
		$ok = $itemx->find("h2[class=episodeye]",$i);
		$title = getBetween($item, 'title="','"');
		$img = getBetween($item, 'data-lazy-src="','"');
		$link = getBetween($ok,'href="https://anitoki.com/?p=','"');

		$array = array(
			"title" => $title,
			"link" => $link,
			"img" => $img
		);
		array_push($output, $array);
		$i++;
	}
	return $output;
}
function getSearch($url,$search,$paged){
	$get = file_get_html($url."?s=".urlencode($search)."&paged=".$paged);
	$itemx = $get->find("div[class=rapi]",0);
	$output = array();

	$pageCount = substr_count($itemx, "<h2 class='episodeye'>");
	$i = 0;
	while ($i < $pageCount) {
		$item = $itemx->find("div[class=thumbz]", $i);
		$ok = $itemx->find("h2[class=episodeye]",$i);
		$title = getBetween($item, 'title="','"');
		$img = getBetween($item, 'data-lazy-src="','"');
		$link = getBetween($ok,'href="https://anitoki.com/?p=','"');
		$array = array(
			"title" => $title,
			"link" => $link,
			"img" => $img
		);
		array_push($output, $array);
		$i++;
	}
	return $output;
}
function getDownload($url,$download) {
	$get = file_get_html($url."?p=".$download);
	$itemx = $get->find("div[class=dlbod]",0);
	$itemx2 = $itemx->find("div[class=smokeddl]",0);
	$itemx3 = $itemx->find("div[class=smokeddl]",1);
	$output = array();
	
	
	// google drive
		$item = $itemx2->find("div[class=smokeurl]", 0);
		$item2 = $itemx2->find("div[class=smokeurl]", 1);
		$item3 = $itemx2->find("div[class=smokeurl]", 2);
		$item4 = $itemx2->find("div[class=smokeurl]", 3);
		$gd = getBetween($item,'<a href="https://drive.google.com/','"');
		$gd1 = getBetween($item2,'<a href="https://drive.google.com/','"');
		$gd2 = getBetween($item3,'<a href="https://drive.google.com/','"');
		$gd3 = getBetween($item4,'<a href="https://drive.google.com/','"');

	// zippyshare
		$ehe = $itemx2->find("div[class=smokeurl]", 0);
		$ehe2 = $itemx2->find("div[class=smokeurl]", 1);
		$ehe3 = $itemx2->find("div[class=smokeurl]", 2);
		$ehe4 = $itemx2->find("div[class=smokeurl]", 3);
		$zippy = getBetween($ehe, '<a href="https://www','"');
		$zippy1 = getBetween($ehe2, '<a href="https://www','"');
		$zippy2 = getBetween($ehe3, '<a href="https://www','"');
		$zippy3 = getBetween($ehe4, '<a href="https://www','"');
		

		$array = array(
			"360p" => array(
				"h264" => array(
					"gd" => "https://drive.google.com/".$gd,
					"zippy" => "https://www".$zippy
				),
				"[h265]" => array(

				),
			),
			"480p" => array(
				"h264" => array(
					"gd" => "https://drive.google.com/".$gd1,
					"zippy" => "https://www".$zippy1
				),
				"[h265]" => array(
				)
			),
			"720p" => array(
				"h264" => array(
					"gd" => "https://drive.google.com/".$gd2,
					"zippy" => "https://www".$zippy2
				),
				"[h265]" => array(
				)
			),
			"1080p" => array(
				"h264" => array(
					"gd" => "https://drive.google.com/".$gd3,
					"zippy" => "https://www".$zippy3
				),
				"[h265]" => array(
				)
			)
		);

		array_push($output, $array);
	
	return $array;
}
header("Content-type:application/json");
$url = "http://anitoki.com";
$page = isset($_GET['page']) ? intval($_GET['page']) : -1;
if (!isset($_GET['page']) == "") {
	$hehe = getPage($url,$page);
	
	if (empty($hehe)) {
		$arr = array("error_code" => "404");
		$enc = json_encode($arr);
		echo $enc;
	}else {
		$arr = array(
			"error_code" => "200",
			"result" => $hehe
		);
		$arr = json_encode($arr);
		echo $arr;
	}
}elseif (!isset($_GET['s']) == "") {
	$search = $_GET['s'];
	$pages = isset($_GET['p']) ? intval($_GET['p']) : -1;
	$hehe = getSearch($url,$search,$pages);
	
	if (empty($hehe)) {
		$arr = array("error_code" => "404");
		$enc = json_encode($arr);
		echo $enc;
	}else {
		$arr = array(
			"error_code" => "200",
			"result" => $hehe
		);
		$arr = json_encode($arr);
		echo $arr;
	}
}elseif (!isset($_GET['id']) == "") {
	$id = $_GET['id'];
	$hehe = getDownload($url,$id);
	
    if (empty($hehe)) {
		$arr = array("error_code" => "404");
		$enc = json_encode($arr);
		echo $enc;
	}else {
		$arr = array(
			"error_code" => "200",
			"result" => $hehe
		);
		$arr = json_encode($arr);
		echo $arr;
	}
} else {
    $ehe = array(
		"error" => "JANCUK"
	);
	echo json_encode($ehe);
}


?>