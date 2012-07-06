<?php
/**
 * Test case results will load here
 *
 * @var int $SCRIPT_START_TIME
 * @var callback $h			Short-cut for htmlspecialchars()
 * @var callback $el		Function for rendering an element from the /views/elements folder
 * @var string $pageTitle
 * @var string $bodyId
 * @var string $bodyClasses
 * @var string $testListHtml
 * @var string $actionHtml
 * @var array $unitTests
 * @var array $libs
 */

use \Atwood\lib\fx\statics\JsStatics;
use \Atwood\lib\fx\statics\CssStatics;

global $SCRIPT_START_TIME;

$pageTitle			= isset($pageTitle) ? $pageTitle : "Testing, 1 2 3";
$bodyId				= isset($controller) ? sprintf(' id="%s"', $h($controller)) : '';
$bodyClass			= isset($action) ? sprintf(' class="%s"', $h($action)) : '';

$percentage	= round((count($unitTests) / count($libs)) * 100);
$level		= $percentage >= 90 ? 'success' : 'warn';

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
	<div class="container-fluid">
		<header>
			<h1>Test Sweet</h1>
			<p class="lead">
				"The difference between a good programmer and a bad programmer is that the good programmer uses tests to detect his mistakes as soon as possible."
				&hearts; Sebastian Bergmann
			</p>
		</header>
		<div class="row-fluid">
			<div class="span3">
				<h3>Overall Coverage: <?=$h($percentage)?>%</h3>
				<p>
					<?=$el('bootstrap/progressBar', array('percent' => $percentage, 'level' => $level))?>
				</p>
				<h3>Available Tests</h3>
				<h6>Choose a test to run:</h6>
				<ul>
				<? foreach($unitTests as $name) { ?>
					<li><a href="/tests/<?=$h($name)?>" title="Click to run test: <?=$h($name)?>"><?=$h($name)?></a></li>
				<? } ?>
				</ul>
			</div>
			<div class="span9">
				<?=$actionHtml?>
			</div>
		</div>
	</div>
	<?=JsStatics::renderDomReady()?>
	<script>
	if (prettyPrint) {
		prettyPrint()
	}
	</script>
	<!-- RUNTIME: <?=$h(round((microtime(true) - $SCRIPT_START_TIME) * 1000))?>ms -->
</body>
</html>
