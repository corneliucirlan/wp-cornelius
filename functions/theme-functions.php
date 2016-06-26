<?php

	/**
     * Filter the CSS class for a nav menu based on a condition.
     *
     * @param array  $classes The CSS classes that are applied to the menu item's <li> element.
     * @param object $item    The current menu item.
     * @return array (maybe) modified nav menu class.
     */
    function wpdocs_special_nav_class($classes, $item)
    {
        // to be added later - "active" class to the active page
        $classes[] = "nav-item";
        return $classes;
    }
    add_filter('nav_menu_css_class' , 'wpdocs_special_nav_class' , 10, 2);

    /**
     * Add custom class to menu anchor tags
     */
    function my_walker_nav_menu_start_el($item_output, $item, $depth, $args)
    {
        $item_output = preg_replace('/<a /', '<a class="nav-link" ', $item_output, 1);
        return $item_output;
    }
    add_filter('walker_nav_menu_start_el', 'my_walker_nav_menu_start_el', 10, 4);


	// Display one card
	function displayCard($settings, $content = false)
	{
		?>
		<article class="card">
			<header class="card-header">
				<?php if (array_key_exists('linkTitle', $settings) && $settings['linkTitle']): ?>
					<a href="<?php the_permalink() ?>"><h2 class="card-title"><?php the_title() ?></h2></a>
				<?php else: ?>
					<h2 class="card-title"><?php the_title() ?></h2>
				<?php endif; ?>
				<?php if (array_key_exists('showCardDetails', $settings) && $settings['showCardDetails']) displayBlogPostDetails(array_key_exists('isSingle', $settings) && $settings['isSingle'] ? true : false) ?>
			</header>
			<div class="card-block">
				<?php the_post_thumbnail('medium') ?>
				<?php array_key_exists('isSingle', $settings) && $settings['isSingle'] == true ? the_content() : the_excerpt() ?>
			</div>
			<footer class="card-footer">
				<div class="row">
					<?php if (array_key_exists('buttons', $settings)): ?>
							<div class="col-sm-12 col-md-12 col-lg-6">
								<?php foreach ($settings['buttons'] as $button): ?>
									<a href="<?php echo $button['url'] ?>" target="<?php echo $button['target'] ?>" class="btn btn-primary-outline"><?php echo $button['label'] ?></a>
								<?php endforeach; ?>
							</div>
							<div class="col-sm-12 col-md-12 col-lg-6">
								<?php if (array_key_exists('showFooterShare', $settings) && $settings['showFooterShare']) displayShareButtons($settings['footerShareSettings']) ?>
							</div>
						<?php else: ?>
								<div class="col-sm-12 col-md-6 col-offset-md-6 col-lg-6 col-offset-lg-6">
									<?php if (array_key_exists('showFooterShare', $settings) && $settings['showFooterShare']) displayShareButtons($settings['footerShareSettings']) ?>
								</div>
					<?php endif; ?>
				</div>
			</footer>
		</article>
		<?php
	}


	// Display latest posts
	function displayRecentPosts($category, $numberOfPosts = 3)
	{
		$args = array(
			'post_type'			=> 'post',
			'post_status' 		=> 'publish',
			'order'				=> 'DESC',
			'category__in'		=> $category,
			'post__not_in'		=> array(get_the_id()),
			'posts_per_page'	=> $numberOfPosts,
		);
		$blogPosts = new WP_Query($args);
		?>
		<h2>Latest Posts</h2>
		<ul class="latest-posts">
			<?php while ($blogPosts->have_posts()): $blogPosts->the_post(); ?>
				<li><h4><a href="<?php the_permalink() ?>" target="_self"><?php the_title() ?></a></h4></li>
			<?php endwhile; ?>
		</ul>
		<?php
	}


	// Display blog post details
	function displayBlogPostDetails($singlePost = false)
	{
		global $post;
		$categoriesText = '';

		$categories = get_the_category();
		foreach ($categories as $category):
			$categoriesText .= '<a class="category-link" href="'.get_category_link($category->cat_ID).'">'.$category->cat_name.'</a>&nbsp;|&nbsp;';
		endforeach;

		?>
		<div class="blog-post-details row valign-wrapper">
			<div class="no-padding-left <?php echo $singlePost ? 'col-sm-12 col-md-8' : 'col-sm-12' ?>">
				<?php echo $categoriesText ?>
				<a href="<?php the_permalink() ?>"><?php echo get_the_date() ?></a>&nbsp;|&nbsp;
				<a rel="author" href="https://twitter.com/<?php the_author_meta('twitter') ?>" target="_blank"><i style="color: #1DA1F2;" class="fa fa-twitter"></i><?php echo str_replace(' ', '', get_the_author()) ?></a>
			</div>
			<?php if ($singlePost): ?>
				<div class="no-padding-right col-sm-12 col-md-4">
					<?php displayShareButtons(array('id' => get_the_id(), 'alignRight' => true)) ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}


	/**
	 * PRINT ONE SKILL
	 */
	function printSkill($skill)
	{
		?>
		<div class="card">
			<h3><?php echo $skill->getSkillName() ?></h3>
			<div class="item-stars">
				<?php
					for ($x = 1; $x <= 5; $x++):
						if ($x <= $skill->getSkillLevel()) echo '<i class="fa fa-star"></i>';
							else echo '<i class="fa fa-star-o"></i>';
					endfor;
				?>
			</div>
		</div>
		<?php
	}


	/**
	 * Get thumbnail photo size
	 *
	 * Uses PHP MobileDetect class
	 *
	 * @return string WP media size
	 */
	function getPhotoSize()
	{
		$detect = new Mobile_Detect();

		$size = 'medium';
		if ($detect->isMobile() && !$detect->isTablet()) $size = 'thumbnail';
			elseif ($detect->isTablet()) $size = 'medium';

		return $size;
	}


	/**
	 * Display share buttons
	 *
	 * @param  array $settings Array of various settings
	 */
	function displayShareButtons($settings)
	{
		// get url if page is category page
		if (array_key_exists('isCategory', $settings) && $settings['isCategory']):
				$url = urlencode(get_category_link($settings['id']));

			// or if tag page
			elseif (array_key_exists('isTag', $settings) && $settings['isTag']):
					$url = urlencode(get_tag_link($settings['id']));

				// or if normal page
				else:
					$url = urlencode(get_permalink($settings['id']));
		endif;

		// get page title
		$title = urlencode(get_post_meta(get_the_id(), '_yoast_wpseo_title', true) != '' ? get_post_meta(get_the_id(), '_yoast_wpseo_title', true) : get_the_title($settings['id']));

		// get page excerpt
		$excerpt = urlencode(get_the_excerpt());

		// set twitter related accounts
		$related = urlencode('corneliucirlan:Corneliu Cirlan');

		// get bitly short url
		//$bitly = json_decode(file_get_contents('https://api-ssl.bitly.com/v3/shorten?access_token='.get_option('bitly_api_key').'&longUrl='.$url));

		// set url to bitly short url
		//$url = $bitly->data->url;
		?>

		<ul class="social-icons <?php echo array_key_exists('alignRight', $settings) && $settings['alignRight'] == true ? ' ' : '' ?>">
			<li><span>Share:</span></li>
            <li class="share-button"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url ?>" title="Share on Facebook"><i class="fa fa-facebook"></i></a></li>
            <li class="share-button"><a target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo $title ?>&amp;url=<?php echo $url ?>&amp;related=<?php echo $related ?>" title="Share on Twitter"><i class="fa fa-twitter"></i></a></li>
            <li class="share-button"><a target="_blank" href="https://plus.google.com/share?url=<?php echo $url ?>" title="Share on Google+"><i class="fa fa-google-plus"></i></a></li>
            <li class="share-button"><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $url ?>&amp;title=<?php echo $title ?>&amp;summary=<?php echo $excerpt ?>" title="Share on Linkedin"><i class="fa fa-linkedin"></i></a></li>
        </ul>
		<?php
	}

?>
