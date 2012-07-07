<?php
/**
 * The default site layout.
 *
 * @var int $SCRIPT_START_TIME
 * @var \Closure $h			Short-cut for htmlspecialchars()
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var string $pageTitle
 * @var string $bodyId
 * @var string $bodyClass
 * @var string $actionHtml
 */

use \Atwood\lib\fx\statics\JsStatics;
use \Atwood\lib\fx\statics\CssStatics;

global $SCRIPT_START_TIME;

JsStatics::inclHead(JS_OLD_IE);

$pageTitle			= isset($pageTitle) ? $pageTitle : "php-atwood: It's a Good Day to Start Something New";
$bodyId				= !empty($bodyId) ? sprintf(' id="%s"', $h($bodyId)) : '';
$bodyClass			= !empty($bodyClass) ? sprintf(' class="%s"', $h($bodyClass)) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?=$h($pageTitle)?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
	<?=CssStatics::renderHead()?>
	<?=JsStatics::renderHead()?>

</head>
<body<?=$bodyId?><?=$bodyClass?>>
	<?=JsStatics::renderBody()?>
	<div class="container_16">
		<div class="grid_16">
			<?=$actionHtml?>
		</div>
		<div class="clear"></div>
	</div>
	<?=JsStatics::renderDomReady()?>
	<!-- RUNTIME: <?=$h(round((microtime(true) - $SCRIPT_START_TIME) * 1000))?>ms -->
</body>
</html>
