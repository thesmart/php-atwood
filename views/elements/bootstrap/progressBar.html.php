<?php
/**
 * @var \Closure $h			Short-cut for htmlspecialchars()
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var string $level		Values: success, warning, danger
 * @var string $label
 * @var int $percent
 */

$level	= isset($level) ? ' progress-'.$level : '';

?>
<div class="progress<?=$h($level)?> progress-striped">
	<div class="bar" style="width: <?=$h($percent)?>%;"></div>
</div