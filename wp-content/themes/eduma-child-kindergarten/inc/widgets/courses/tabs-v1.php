<?php

global $post, $wpdb;
$limit_tab  = $instance['tabs-options']['limit_tab'] ? $instance['tabs-options']['limit_tab'] : 4;
$cat_id_tab = $instance['tabs-options']['cat_id_tab'] ? $instance['tabs-options']['cat_id_tab'] : array();


$sort = $instance['order'];

if ( ! empty( $cat_id_tab ) ) {

	foreach ( $cat_id_tab as $value ) {

		$array[ $value ] = 1;
		$html[ $value ]  = '';

		$condition[ $value ]              = array(
			'post_type'           => 'lp_course',
			'posts_per_page'      => $limit_tab,
			'ignore_sticky_posts' => true,
		);
		$condition[ $value ]['tax_query'] = array(
			array(
				'taxonomy' => 'course_category',
				'field'    => 'term_id',
				'terms'    => $value
			),
		);

		if ( $sort == 'popular' ) {

			$popular_query[ $value ] = $wpdb->get_col( $wpdb->prepare(
				"
		SELECT pm.post_id, pm.meta_value + COUNT(pm.post_id) - IF (uc.course_id, 0, 1) as students
		FROM `$wpdb->postmeta` AS pm
		LEFT JOIN `$wpdb->learnpress_user_courses` AS uc ON pm.post_id = uc.course_id
		WHERE pm.meta_key = %s
		GROUP BY pm.post_id
		ORDER BY students DESC",
				'_lp_students'
			) );

			$condition[ $value ]['post__in'] = $popular_query[ $value ];
			$condition[ $value ]['orderby']  = 'post__in';
		}

		$the_query[ $value ] = new WP_Query( $condition[ $value ] );

		if ( $the_query[ $value ]->have_posts() ) :
			?>
			<?php
			ob_start();
			while ( $the_query[ $value ]->have_posts() ) : $the_query[ $value ]->the_post();
				?>
				<div class="course-item lpr_course <?php echo 'course-grid-' . $limit_tab; ?>">
					<?php
					echo '<div class="course-thumbnail">';
					echo '<a href="' . esc_url( get_the_permalink() ) . '" >';
					echo thim_get_feature_image( get_post_thumbnail_id( $post->ID ), 'full', apply_filters( 'thim_course_thumbnail_width', 450 ), apply_filters( 'thim_course_thumbnail_height', 450 ), get_the_title() );
					echo '</a>';
					thim_course_wishlist_button( $post->ID );
					echo '<a class="course-readmore" href="' . esc_url( get_the_permalink() ) . '">' . esc_html__( 'Read More', 'eduma' ) . '</a>';
					echo '</div>';
					?>
					<div class="thim-course-content">
						<?php
						learn_press_course_instructor();
						?>
						<h2 class="course-title">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>"> <?php echo get_the_title(); ?></a>
						</h2>

						<div class="course-meta">
							<?php learn_press_course_students(); ?>
							<?php thim_course_ratings_count(); ?>
							<div class="course-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								<?php
								$price = get_post_meta( $post->ID, 'thim_course_price', true );
								$unit_price = get_post_meta( $post->ID, 'thim_course_unit_price', true );
								?>
								<div class="course-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
									<div class="course-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
										<div class="value " itemprop="price" content="<?php echo esc_attr( $price ); ?>">
											<?php echo esc_html( $price ); ?>
										</div>
										<?php echo ( ! empty( $unit_price ) ) ? '<div class="unit-price">' . $unit_price . '</div>' : ''; ?>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<?php
			endwhile;
			$html[ $value ] .= ob_get_contents();
			ob_end_clean();
			?>

			<?php
		endif;
		wp_reset_postdata();
	}
} else {
	return;
}

if ( $instance['title'] ) {
	echo ent2ncr( $args['before_title'] . $instance['title'] . $args['after_title'] );
}

$list_tab = $content_tab = '';
foreach ( $cat_id_tab as $k => $tab ) {
	$term = get_term_by( 'id', $tab, 'course_category' );
	if ( $k == 0 ) {
		$list_tab .= '<li class="active"><a href="#tab-course-' . $tab . '" data-toggle="tab">' . $term->name . '</a></li>';
		$content_tab .= '<div role="tabpanel" class="tab-pane fade in active" id="tab-course-' . $tab . '">';
		$content_tab .= $html[ $tab ];
		$content_tab .= '</div>';
	} else {
		$list_tab .= '<li><a href="#tab-course-' . $tab . '" data-toggle="tab">' . $term->name . '</a></li>';
		$content_tab .= '<div role="tabpanel" class="tab-pane fade" id="tab-course-' . $tab . '">';
		$content_tab .= $html[ $tab ];
		$content_tab .= '</div>';
	}
}
?>
<div class="thim-category-tabs thim-course-grid">
	<ul class="nav nav-tabs">
		<?php echo ent2ncr( $list_tab ); ?>
	</ul>
	<div class="tab-content thim-list-event">
		<?php echo ent2ncr( $content_tab ); ?>
	</div>
</div>

