<?php
/**
 * The default site layout.
 *
 * @var int $SCRIPT_START_TIME
 * @var callback $h			Short-cut for htmlspecialchars()
 * @var callback $el		Function for rendering an element from the /views/elements folder
 * @var string $pageTitle
 * @var string $bodyId
 * @var string $bodyClasses
 * @var string $actionHtml
 */

use \Smart\lib\fx\statics\JsStatics;
use \Smart\lib\fx\statics\CssStatics;

global $SCRIPT_START_TIME;

$bodyId				= isset($controller) ? sprintf(' id="%s"', $h($controller)) : '';
$bodyClass			= isset($action) ? sprintf(' class="%s"', $h($action)) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?=CssStatics::renderHead()?>
</head>
<body<?=$bodyId?><?=$bodyClass?>>
	<?=$actionHtml?>
	<!-- RUNTIME: <?=$h(round((microtime(true) - $SCRIPT_START_TIME) * 1000))?>ms -->
</body>
</html>
