<?php 
$input = $this->input;
if (isset($err)) {
	($err);
}
?>

<?php if ( ! empty($text)): ?>
<?php foreach ($text as $k=>$v): ?>
<div>
<?php echo $k ?>
</div>
<div>
<textarea rows="<?php echo count(explode("\n", $v)) ?>" cols="65"><?= $v ?></textarea>
</div>
<?php endforeach; ?>
<?php endif; ?>