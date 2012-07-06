<?php
/**
 * List a set of tests.
 * @var callback $el		Function for rendering an element from the /views/elements folder
 * @var callback $h			Short-cut for htmlspecialchars()
 * @var string $answer
 */
?>
Survey says!? <?=$el('bootstrap/alert', array('body' => $answer))?>