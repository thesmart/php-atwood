<?php
/**
 * List a set of tests.
 * @var \Closure $h			Short-cut for htmlspecialchars()
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var string $suiteName
 * @var int $numTests
 * @var array $passed
 * @var array $skipped
 * @var array $notImplemented
 * @var array $errors
 * @var array $failures
 * @var \Atwood\lib\test\CodeCoverage $codeCoverage
 */

$summaryHtml	= $el('UnitTest/summary', array(
		'suiteName' 		=> $suiteName,
		'numTests' 			=> $numTests,
		'passed' 			=> $passed
));

$codeCoverageHtml	= '';
if (isset($codeCoverage) && empty($errors) && empty($failures)) {
	$codeCoverageHtml	= $el('UnitTest/codeCoverage', array('codeCoverage' => $codeCoverage));
}

?>
<?=$summaryHtml?>
<?=$codeCoverageHtml?>

<!--ERRORS-->
<?
$isFirst = true;
foreach ($errors as /** @var \PHPUnit_Framework_TestFailure $fail */ $fail) {
?>
	<?=$el('UnitTest/error', array('suiteName' => $suiteName, 'error' => $fail, 'isFirst' => $isFirst))?>
<?
	$isFirst = false;
}
?>

<!--FAILED TESTS-->
<?
$isFirst = true;
foreach ($failures as /** @var \PHPUnit_Framework_TestFailure $fail */ $fail) {
?>
	<?=$el('UnitTest/failure', array('suiteName' => $suiteName, 'error' => $fail, 'isFirst' => $isFirst))?>
<?
	$isFirst = false;
}
?>

<!--SKIPPED TESTS-->
<? foreach ($skipped as /** @var \PHPUnit_Framework_TestFailure $fail */ $fail) {
	/** @var \Atwood\lib\test\AtwoodTest $test  */
	$test	= $fail->failedTest();
?>
	<?=$el('bootstrap/alert', array('hn' => "SKIPPED - {$test->getName(false)}", 'body' => $fail->exceptionMessage(), 'level' => 'warn'))?>
<? } ?>

<!--NOT IMPLEMENTED TESTS-->
<? foreach ($notImplemented as /** @var \PHPUnit_Framework_TestFailure $fail */ $fail) {
	/** @var \Atwood\lib\test\AtwoodTest $test  */
	$test	= $fail->failedTest();
?>
	<?=$el('bootstrap/alert', array('hn' => "NOT IMPLEMENTED - {$test->getName(false)}", 'body' => $fail->exceptionMessage(), 'level' => 'inverse'))?>
<? } ?>
