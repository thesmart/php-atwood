<?php
/**
 * @var \Closure $h			Short-cut for htmlspecialchars()
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var string $level		error, success, info
 * @var \Exception $ex
 */

$accordion	= $el('bootstrap/accordion', array(
		'id' => spl_object_hash($ex),
		'label' => 'See More',
		'type'	=> 'error',
		'body' => isset($ex->xdebug_message) ? nl2br($h($ex->xdebug_message)) : nl2br($h($ex->getTraceAsString()))
));

$level	= isset($level) ? ' alert-'.$level : ' alert-error';

?>
<div class="alert<?=$h($level)?> alert-block">
	<h4 class="alert-heading"><?=$h(sprintf('Exception: "%s"', get_class($ex)))?></h4>
	<p>
		<?=$h(sprintf('"%s" line %s', $ex->getFile(), $ex->getLine()))?>
	</p>
	<?=$accordion?>
</div>