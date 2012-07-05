<?php
/**
 * @var callback $h			Short-cut for htmlspecialchars()
 * @var callback $el		Function for rendering an element from the /views/elements folder
 * @var string $suiteName
 * @var \Smart\lib\test\CodeCoverage $codeCoverage
 */

$percentage	= $codeCoverage->percentCovered();
$level		= $percentage >= 70 ? 'warning' : 'error';
$level		= $percentage >= 90 ? 'success' : $level;

?>
<div class="codeCoverage">
	<h2><?=$h($codeCoverage->percentCovered())?>% Code Coverage</h2>
	<p>
		<?=$el('bootstrap/progressBar', array('percent' => $codeCoverage->percentCovered(), 'level' => $level))?>
	</p>
	<pre class="prettyprint">
<? for ($i = 0; $i < $codeCoverage->countLines(); ++$i) { ?>
<? if ($codeCoverage->isCovered($i)) { ?>
<span class="covered"><?=$h($codeCoverage->getLine($i))?></span>

<? } else if ($codeCoverage->isUncovered($i)) { ?>
<span class="uncovered"><?=$h($codeCoverage->getLine($i))?></span>

<? } else { ?>
<?=$h($codeCoverage->getLine($i))?>

<? } ?>
<? } ?>
	</pre>
</div>