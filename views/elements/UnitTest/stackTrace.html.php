<?php
/**
 * @var \Closure $h			Short-cut for htmlspecialchars()
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var Atwood\lib\fx\exception\Trace $trace
 * @var \PHPUnit_Framework_TestFailure $error
 */

$i	= 0;
?>
<table class="table table-condensed">
<?foreach ($trace->trace as $frame) { ?>
	<tr <? if ($trace->frame === $frame) { ?>class="accent"<? } ?>>
		<td><?=$h($i++)?></td><td><?=$h($frame)?></td>
	</tr>
<? } ?>
</table>