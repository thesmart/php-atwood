<?php
/**
 * Render all passed tests
 * @var \Closure $h			Short-cut for htmlspecialchars()
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var string $suiteName
 * @var array $passed
 * @var int $numTests
 */

$percent	= round((count($passed) / $numTests) * 100);
$level		= $percent >= 100 ? 'success' : 'danger';
?>
<div class="hero-unit">
	<h1><?=$h($percent)?>% Passed</h1>
	<p>
		<?=$el('bootstrap/progressBar', array('percent' => $percent, 'level' => $level))?>
	</p>
	<h3>
		Tests: <?=count($passed)?> / <?=$h($numTests)?><br/>
	</h3>
	<? foreach($passed as $k => $v) {
	$k = explode('::', $k);
?>
	<span class="label label-success"><?=$h($k[1])?></span>
<? } ?>
</div>