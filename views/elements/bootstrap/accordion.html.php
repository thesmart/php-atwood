<?php
/**
 * View
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var \Closure $h		Short-cut for htmlspecialchars()
 * @var string $level	Values: null, primary, danger, warning, success, info, inverse
 * @var string $id		Unique id of the element pair
 * @var string $label
 * @var string $body
 * @var bool $isOpen
 */

$level = isset($level) ? ' btn-'.$level : '';

?>
<button class="btn<?=$h($level)?>" data-toggle="collapse" data-target="#<?=$h($id)?>">
	<?=$label?>
</button>
<div id="<?=$h($id)?>" class="collapse<? if (!empty($isOpen)) { ?> in<? } ?>">
	<?=$body?>
</div>