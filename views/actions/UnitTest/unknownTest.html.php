<?php
/**
 * @var \Closure $el		Function for rendering an element from the /views/elements folder
 * @var \Closure $h			Short-cut for htmlspecialchars()
 */

echo $el('bootstrap/alert', array('body' => 'You requested to run an unknown unit test.', 'level' => 'warn'));
?>