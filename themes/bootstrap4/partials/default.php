<section>
	<article class="">
		<?= $content ?>
	</article>

	<section>
		<?php foreach($children as $child): ?>
			<?= $child ?>
		<?php endforeach ?>
	</section>

</section>
