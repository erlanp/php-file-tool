<?php if ( ! empty($text)): ?>
<textarea rows="<?php echo count(explode("\n", $text)) ?>" cols="65"><?= $text ?></textarea>
</div>
<?php endif; ?>