<?php

global $post;
$limit        = $instance['limit'];
$item_visible = $instance['slider-options']['item_visible'];
$pagination   = $instance['slider-options']['show_pagination'] ? $instance['slider-options']['show_pagination'] : 0;
$navigation   = $instance['slider-options']['show_navigation'] ? $instance['slider-options']['show_navigation'] : 0;
$autoplay =  isset( $instance['slider-options']['auto_play'] ) ? $instance['slider-options']['auto_play'] : 0;
$condition    = array(
	'post_type'           => 'lp_course',
	'posts_per_page'      => $limit,
	'ignore_sticky_posts' => true,
);
$sort         = $instance['order'];

if ( $sort == 'category' && $instance['cat_id'] && $instance['cat_id'] != 'all' ) {
	if ( get_term( $instance['cat_id'], 'course_category' ) ) {
		$condition['tax_query'] = array(
			array(
				'taxonomy' => 'course_category',
				'field'    => 'term_id',
				'terms'    => $instance['cat_id']
			),
		);
	}
}

if ( $sort == 'popular' ) {
//	global $wpdb;
//	$query = $wpdb->prepare( "
//	  SELECT ID, a+IF(b IS NULL, 0, b) AS students FROM(
//		SELECT p.ID as ID, IF(pm.meta_value, pm.meta_value, 0) as a, (
//	SELECT COUNT(*)
//  FROM (SELECT COUNT(item_id), item_id, user_id FROM wp_learnpress_user_items GROUP BY item_id, user_id) AS Y
//  GROUP BY item_id
//  HAVING item_id = p.ID
//) AS b
//FROM wp_posts p
//LEFT JOIN wp_postmeta AS pm ON p.ID = pm.post_id  AND pm.meta_key = %s
//WHERE p.post_type = %s AND p.post_status = %s
//GROUP BY ID
//) AS Z
//ORDER BY students DESC
//	  LIMIT 0, $limit
// ", '_lp_students', 'lp_course', 'publish' );
//
//	$post_in = $wpdb->get_col( $query );
//
//	$condition['post__in'] = $post_in;
//	$condition['orderby']  = 'post__in';
}

$the_query = new WP_Query( $condition );

if ( $the_query->have_posts() ) :
	if ( $instance['title'] ) {
		echo ent2ncr( $args['before_title'] . $instance['title'] . $args['after_title'] );
	}


	?>
	<div class="thim-carousel-wrapper thim-course-carousel thim-course-grid" data-visible="<?php echo esc_attr( $item_visible ); ?>"
	     data-pagination="<?php echo esc_attr( $pagination ); ?>" data-navigation="<?php echo esc_attr( $navigation ); ?>" data-autoplay="<?php echo esc_attr($autoplay); ?>">
		<?php
		//$index = 1;
		while ( $the_query->have_posts() ) : $the_query->the_post();


			?>
			<div class="course-item">
				<?php
				echo '<div class="course-thumbnail">';
				echo '<a href="' . esc_url( get_the_permalink() ) . '" >';
				echo thim_get_feature_image( get_post_thumbnail_id( $post->ID ), 'full', apply_filters( 'thim_course_thumbnail_width', 450 ), apply_filters( 'thim_course_thumbnail_height', 265 ), get_the_title() );
				echo '</a>';
				echo '<a class="course-readmore" href="' . esc_url( get_the_permalink() ) . '">' . esc_html__( 'Read More', 'eduma' ) . '</a>';
				echo '</div>';
				?>
				<div class="thim-course-content">

					<h4 class="course-title">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>"> <?php echo get_the_title(); ?></a>
					</h4>

					<?php
					learn_press_course_instructor();
					?>

					<div class="thim-background-border"></div>

					<div class="course-meta">
						<?php thim_meta_course_kindergarten( $post->ID ); ?>
					</div>
				</div>
			</div>
			<?php
			//$index++ ;
		endwhile;
		?>
	</div>
	<?php
endif;
wp_reset_postdata();

?>
