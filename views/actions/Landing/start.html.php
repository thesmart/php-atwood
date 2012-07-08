<?php
/**
 * 
 * @var \Closure $h			Short-cut for htmlspecialchars()
 */

$msg = isset($msg) ? $msg : 'Welcome to the jungle.';
?>
<?=$h($msg)?>