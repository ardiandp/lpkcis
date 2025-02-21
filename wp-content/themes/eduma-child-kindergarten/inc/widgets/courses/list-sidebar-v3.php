<?php
global $post;
$limit     = $instance['limit'];
$sort      = $instance['order'];
$condition = array(
	'post_type'           => 'lp_course',
	'posts_per_page'      => $limit,
	'ignore_sticky_posts' => true,
);

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
	<div class="thim-course-list-sidebar">
		<?php
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$course      = LP_Course::get_course( $post->ID );
			$is_required = $course->is_required_enroll();

			?>
			<div class="lpr_course <?php echo has_post_thumbnail() ? 'has-post-thumbnail' : ''; ?>">
				<?php
				if ( has_post_thumbnail() ) {
					$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
					echo '<div class="course-thumbnail">';
					echo '<img src="' . esc_url( $src[0] ) . '" alt="' . get_the_title() . '"/>';
					echo '</div>';
				}
				?>
				<div class="thim-course-content">
					<h3 class="course-title">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>"> <?php echo get_the_title(); ?></a>
					</h3>

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
			<?php
		endwhile;
		?>
	</div>
	<?php
endif;
wp_reset_postdata();

?>