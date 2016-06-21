<?php

    // Security check
    if (!defined('ABSPATH')) die;

    the_post();

?>

<?php get_header() ?>

<main class="container-fluid main-container">

	<section class="row">
		<div class="col-xs-12">
			<?php the_content() ?>
		</div>
	</section>

	<section class="row">
		<?php for ($x=1; $x<=3; $x++): ?>
			<div class="frontpage-button col-xs-12 col-md-4 col-lg-4">
				<a href="<?php the_field('button-'.$x) ?>" class="btn btn-primary" style="width: 80%;"><?php the_field('button-'.$x.'-text') ?></a>
			</div>
		<?php endfor; ?>
	</section>

</main>

<?php get_footer() ?>
