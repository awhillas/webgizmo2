<?php if ($content): ?>
	<section>
		<?php if ($children): ?>
			<section>
				<?php foreach($children as $child): ?>
					<?= $child ?>
				<?php endforeach ?>
			</section>
		<?php endif ?>
	</section>
<?php endif ?>
