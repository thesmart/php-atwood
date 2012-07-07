<?php
/**
 * @var \Closure $h			Short-cut for htmlspecialchars()
 * @var string $hn		The h1, h2, or whatever hn tag
 * @var string $body
 * @var string $enableClose
 */
?>
<? if (empty($hn)) { ?>
<div class="alert alert-success">
	<? if (!empty($enableClose)) { ?>
		<button class="close" data-dismiss="alert">&times;</button>
	<? } ?>
	<?=$body?>
</div>
<? } else { ?>
<div class="alert alert-success alert-block">
	<? if (!empty($enableClose)) { ?>
		<button class="close" data-dismiss="alert">&times;</button>
	<? } ?>
	<h4 class="alert-heading"><?=$hn?></h4>
	<?=$body?>
</div>
<? } ?>