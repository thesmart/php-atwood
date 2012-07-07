<?php
/**
 * List a set of tests.
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var \Closure $h			Short-cut for htmlspecialchars()
 * @var string $answer
 */
?>
Survey says!? <?=$el('bootstrap/alert', array('body' => $answer))?>