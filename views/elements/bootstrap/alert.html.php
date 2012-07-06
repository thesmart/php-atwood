<?php
/**
 * @var callback $h			Short-cut for htmlspecialchars()
 * @var string $hn		The h1, h2, or whatever hn tag
 * @var string $body		Optional.
 * @var string $enableClose Optional.
 * @var string $level		Optional. error, success, info
 */

$level	= isset($level) ? ' alert-'.$level : ' alert-error';
$body	= isset($body) ? $body : '';
?>
<? if (empty($hn)) { ?>
<div class="alert<?=$h($level)?>">
	<? if (!empty($enableClose)) { ?>
		<button class="close" data-dismiss="alert">&times;</button>
	<? } ?>
	<?=$body?>
</div>
<? } else { ?>
<div class="alert<?=$h($level)?> alert-block">
	<? if (!empty($enableClose)) { ?>
		<button class="close" data-dismiss="alert">&times;</button>
	<? } ?>
	<h4 class="alert-heading"><?=$hn?></h4>
	<?=$body?>
</div>
<? } ?>