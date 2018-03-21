<?php if ($content): ?>
	<section>
		<article class="">
			<?= $content ?>
		</article>

		<?php if ($children): ?>
			<section>
				<?php foreach($children as $child): ?>
					<?= $child ?>
				<?php endforeach ?>
			</section>
		<?php endif ?>

	</section>
<?php endif ?>
