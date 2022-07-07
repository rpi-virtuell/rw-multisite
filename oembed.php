<?php
$url = $_GET['url'];
header('Content-Type: application/json');

if(
	preg_match('#https://.*/wp-admin/admin-ajax\.php\?action=h5p_embed.*#',$url)  || 
	preg_match('#https://religionsunterricht.net/ru/.*#',$url)  

){

	$html = json_encode('<iframe src="'.$url.'" width="100%" style="margin-bottom: 20px;" frameborder="0" allowfullscreen="allowfullscreen"></iframe><script src="https://blogs.rpi-virtuell.de/wp-content/plugins/h5p/h5p-php-library/js/h5p-resizer.js" charset="UTF-8"></script>');

?>
{
	"version": "1.0",
	"type": "video",
	"width": 600,
	"maxwidth": 2000,
	"height": 300,
	"maxheight": 1000,
	"title": "h5p",
	"url": "https://raw.githubusercontent.com/rpi-virtuell/Logos-2017/master/png/rpi-virtuell-2017.png",
	"author_name": "h5p",
	"author_url": "http://h5p.org",
	"provider_name": "rpi-virtuell",
	"provider_url": "https://rpi-virtuell.de",
	"html":<?php echo $html; ?>
}
<?php } ?>