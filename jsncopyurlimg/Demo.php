<?
include_once("jsncopyurlimg.php");
$jsncopyurlimg = new Jsncopyurlimg;


//1.想要儲存的絕對路徑
$savedir			=	realpath("demo_savedir");

//2.若使用resize()記得引用 function ImageResize()
include_once("plugin/ImageResize.php");


//3. 指定圖片網址，並COPY兩張縮小的、兩張原圖
$jsncopyurlimg
	->url("http://www.wondershow.tw/upload/File_2013080811523449.JPG")
	->resize(100, 100, 1, $savedir, "s.jpg") //不指定檔名就是自動產生檔名
	->resize(100, 100, 10, $savedir, "s2.jpg")
	->org($savedir, "o.jpg")
	->org($savedir, "o2.jpg")
	->copy();


//4. 檢視成果, 注意型態是ArrayObject Object
$result = $jsncopyurlimg->result();
//print_r($result);


if (is_object($result)) foreach ($result as $file)
{
	echo $file."<br>";
}



?>