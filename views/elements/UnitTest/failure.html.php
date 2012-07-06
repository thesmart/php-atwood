<?php
/**
 * @var callback $h			Short-cut for htmlspecialchars()
 * @var callback $el		Function for rendering an element from the /views/elements folder
 * @var string $suiteName
 * @var \PHPUnit_Framework_TestFailure $error
 */

/** @var \Atwood\lib\test\AtwoodTest $test  */
$test	= $error->failedTest();
$ex		= $error->thrownException();

use \Atwood\lib\fx\exception\Trace;
$trace	= new Trace($ex, $suiteName);

$accordion	= $el('bootstrap/accordion', array(
		'id' => uniqid(),
		'label' => 'Callstack',
		'type'	=> 'error',
		'body' => $el('UnitTest/stackTrace', array('trace' => $trace))
));

?>
<div class="alert alert-error alert-block">
	<h4 class="alert-heading">FAIL - <?=$h($ex->getMessage())?></h4>
	<p>
		<?=$h($trace->frame)?>
	</p>
	<?=$accordion?>
</div>