<?php

function thim_child_enqueue_styles() {
    wp_enqueue_style( 'thim-parent-style', get_template_directory_uri() . '/style.css', array(), THIM_THEME_VERSION );
	wp_enqueue_script( 'thim_child_script', get_stylesheet_directory_uri() . '/js/child_script.js', array( 'jquery' ), THIM_THEME_VERSION );
}

add_action( 'wp_enqueue_scripts', 'thim_child_enqueue_styles', 1000 );

function thim_child_enqueue_admin_script() {
	wp_dequeue_style( 'learn-press-mb-course' );
	wp_dequeue_script( 'learn-press-mb-course' );
}

add_action( 'admin_enqueue_scripts', 'thim_child_enqueue_admin_script', 1000 );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
//Add meta-box-course.js into single course dashboard
if ( is_admin() && is_plugin_active( 'learnpress/learnpress.php' ) ) {
	global $current_screen;
	if ( $current_screen ) {
		$screen_id = $current_screen->id;
		if ( in_array( $screen_id, array( "edit-lp_course" ) ) ) {
			LP_Admin_Assets::enqueue_script( 'meta-box-course', learn_press_plugin_url( 'assets/js/admin/meta-box-course.js' ), array( 'jquery' ) );
		}
	}
}

load_theme_textdomain( 'eduma-child', get_stylesheet_directory() . '/languages' );

//Replace courses meta
function thim_add_course_meta( $meta_box ) {
	$fields             = array();
	$fields[]           = array(
		'name' => esc_html__( 'Duration Info', 'eduma-child' ),
		'id'   => 'thim_course_duration',
		'type' => 'text',
		'desc' => esc_html__( 'Display duration info', 'eduma-child' ),
		'std'  => '30 hours'
	);
	$fields[]           = array(
		'name' => esc_html__( 'Class Size', 'eduma-child' ),
		'id'   => 'thim_course_class_size',
		'type' => 'number',
		'desc' => esc_html__( 'Class Size', 'eduma-child' ),
		'std'  => '30'
	);
	$fields[]           = array(
		'name' => esc_html__( 'Available Seats', 'eduma-child' ),
		'id'   => 'thim_course_available_seats',
		'type' => 'number',
		'desc' => esc_html__( 'Enter available seats', 'eduma-child' ),
		'std'  => '10'
	);
	$fields[]           = array(
		'name' => esc_html__( 'Years Old', 'eduma-child' ),
		'id'   => 'thim_course_year_old',
		'type' => 'text',
		'desc' => esc_html__( 'Enter age', 'eduma-child' ),
		'std'  => '2 - 4'
	);
	$fields[]           = array(
		'name' => esc_html__( 'Price', 'eduma-child' ),
		'id'   => 'thim_course_price',
		'type' => 'text',
		'desc' => esc_html__( 'Enter course price', 'eduma-child' ),
		'std'  => '$50'
	);
	$fields[]           = array(
		'name' => esc_html__( 'Unit', 'eduma-child' ),
		'id'   => 'thim_course_unit_price',
		'type' => 'text',
		'desc' => esc_html__( 'Enter unit, for example, p/h, person/hour', 'eduma-child' ),
		'std'  => esc_html__( 'p/h', 'eduma-child' )
	);
	$fields[]           = array(
		'name' => esc_html__( 'Media Intro', 'eduma' ),
		'id'   => 'thim_course_media_intro',
		'type' => 'textarea',
		'desc' => esc_html__( 'Enter media intro', 'eduma' ),
	);
	$meta_box['fields'] = $fields;

	return $meta_box;
}

//Override function thim_add_course_remove_meta on parent theme
function thim_add_course_remove_meta( $meta_box ) {
	$meta_box['fields'] = array();
	return $meta_box;
}

// remove curriculum
add_action( 'init', 'thim_remove_course_curriculum', 10 );
if ( !function_exists( 'thim_remove_course_curriculum' ) ) {
	function thim_remove_course_curriculum() {
		if ( class_exists( 'LP_Course_Post_Type' ) ) {

			remove_action( 'edit_form_after_editor', array( LP_Course_Post_Type::instance(), 'curriculum_editor' ), 0 );
		}
	}
}

add_filter( 'rwmb_show_course_assessment', '__return_false' );
add_filter( 'rwmb_show_course_curriculum', '__return_false' );
add_filter( 'rwmb_show_course_payment', '__return_false' );

//Remove tab assessment & payment from LP 2.1.3
add_filter( 'learn_press_lp_course_tabs', 'thim_remove_tabs_course' );
function thim_remove_tabs_course( $tabs ) {
	if ( !empty( $tabs ) ) {
		foreach ( $tabs as $tab_id => $tab ) {
			if ( !empty( $tab->meta_box ) && is_array( $tab->meta_box ) ) {
				$id = $tab->meta_box['id'];
				if ( !empty( $id ) ) {
					if ( in_array( $id, array( 'course_payment', 'course_assessment' ) ) ) {
						unset( $tabs[$tab_id] );
					}
				}
			}
		}
	}
	return $tabs;
}


function thim_meta_course_kindergarten( $course_id ) {

	$class_size = get_post_meta( $course_id, 'thim_course_class_size', true );
	$year_old   = get_post_meta( $course_id, 'thim_course_year_old', true );
	$price      = get_post_meta( $course_id, 'thim_course_price', true );
	$unit_price = get_post_meta( $course_id, 'thim_course_unit_price', true );
	?>
	<?php if ( !empty( $class_size ) ): ?>
		<div class="class-size">
			<label><?php esc_html_e( 'Class Size', 'eduma-child' ); ?></label>

			<div class="value"><?php echo esc_html( $class_size ); ?></div>
		</div>
	<?php endif; ?>

	<?php if ( !empty( $year_old ) ): ?>
		<div class="year-old">
			<label><?php esc_html_e( 'Years Old', 'eduma-child' ); ?></label>

			<div class="value"><?php echo esc_html( $year_old ); ?></div>
		</div>
	<?php endif; ?>

	<div class="course-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<div class="value " itemprop="price" content="<?php echo esc_attr( $price ); ?>">
			<?php echo esc_html( $price ); ?>
		</div>
		<?php echo ( !empty( $unit_price ) ) ? '<div class="unit-price">' . $unit_price . '</div>' : ''; ?>
	</div>

	<?php

}

function thim_related_courses() {
	$related_courses = thim_get_related_courses( 3 );

	if ( $related_courses ) {
		?>
		<div class="thim-ralated-course">
			<h3 class="related-title"><?php esc_html_e( 'You May Like', 'eduma' ); ?></h3>

			<div class="thim-course-grid">
				<?php foreach ( $related_courses as $course_item ) : ?>

					<article class="course-grid-3 lpr_course">
						<div class="course-item">
							<div class="course-thumbnail">
								<a href="<?php echo get_the_permalink( $course_item->ID ); ?>">
									<?php
									echo thim_get_feature_image( get_post_thumbnail_id( $course_item->ID ), 'full', 450, 450, get_the_title( $course_item->ID ) );
									?>
								</a>
								<?php do_action( 'thim_inner_thumbnail_course' ); ?>
								<?php echo '<a class="course-readmore" href="' . esc_url( get_the_permalink( $course_item->ID ) ) . '">' . esc_html__( 'Read More', 'eduma' ) . '</a>'; ?>
							</div>
							<div class="thim-course-content">

								<h4 class="course-title">
									<a href="<?php echo esc_url( get_the_permalink( $course_item->ID ) ); ?>"> <?php echo get_the_title( $course_item->ID ); ?></a>
								</h4>

								<div class="course-author">
									<?php echo get_avatar( $course_item->post_author, 40 ); ?>
									<div class="author-contain">
										<div class="value">
											<?php echo get_the_author_meta( 'display_name', $course_item->post_author ); ?>
										</div>
									</div>
								</div>

								<div class="thim-background-border"></div>

								<div class="course-meta">
									<?php thim_meta_course_kindergarten( $course_item->ID ); ?>
								</div>

							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}

function thim_course_info() {

	$course_id = get_the_ID();

	$duration        = get_post_meta( $course_id, 'thim_course_duration', true );
	$class_size      = get_post_meta( $course_id, 'thim_course_class_size', true );
	$year_old        = get_post_meta( $course_id, 'thim_course_year_old', true );
	$available_seats = get_post_meta( $course_id, 'thim_course_available_seats', true );
	$thim_options    = get_theme_mods();

	$category = wp_get_post_terms( $course_id, 'course_category' );

	$cat_name = $category[0]->name;

	?>
	<div class="thim-course-info">
		<h3 class="title"><?php esc_html_e( 'Course Features', 'eduma-child' ); ?></h3>
		<ul>
			<li class="duration-feature">
				<i class="fa fa-clock-o"></i>
				<span class="label"><?php esc_html_e( 'Duration', 'eduma-child' ); ?></span>
				<span class="value"><?php echo esc_html( $duration ); ?></span>
			</li>
			<li class="activities-feature">
				<i class="fa fa-futbol-o"></i>
				<span class="label"><?php esc_html_e( 'Activities', 'eduma-child' ); ?></span>
				<span class="value"><?php echo esc_html( $cat_name ); ?></span>

			</li>
			<li class="class-feature">
				<i class="fa fa-users"></i>
				<span class="label"><?php esc_html_e( 'Class Sizes', 'eduma-child' ); ?></span>
				<span class="value"><?php echo esc_html( $class_size ); ?></span>

			</li>
			<li class="years-feature">
				<i class="fa fa-sun-o"></i>
				<span class="label"><?php esc_html_e( 'Years Old', 'eduma-child' ); ?></span>
				<span class="value"><?php echo esc_html( $year_old ); ?></span>
			</li>
			<li class="available-feature">
				<i class="fa fa-user-plus"></i>
				<span class="label"><?php esc_html_e( 'Available Seats', 'eduma-child' ); ?></span>
				<span class="value"><?php echo esc_html( $available_seats ); ?></span>
			</li>

		</ul>
		<?php
		if ( !empty( $thim_options['thim_learnpress_timetable_link'] ) ) {
			echo '<div class="text-center"><a class="thim-timetable-link" target="_blank" href="' . esc_url( $thim_options['thim_learnpress_timetable_link'] ) . '">' . esc_html( 'Courses Schedules', 'eduma-child' ) . '</a></div>';
		}
		?>
	</div>
	<?php
}

function thim_child_manage_course_columns( $columns ) {
	unset( $columns['price'] );
	$keys   = array_keys( $columns );
	$values = array_values( $columns );
	$pos    = array_search( 'sections', $keys );
	if ( $pos !== false ) {
		array_splice( $keys, $pos + 1, 0, array( 'thim_price' ) );
		array_splice( $values, $pos + 1, 0, __( 'Price', 'eduma-child' ) );
		$columns = array_combine( $keys, $values );
	} else {
		$columns['thim_price'] = __( 'Price', 'eduma-child' );
	}
	return $columns;
}

add_filter( 'manage_lp_course_posts_columns', 'thim_child_manage_course_columns' );

function thim_child_manage_course_columns_content( $column ) {
	global $post;
	switch ( $column ) {
		case 'thim_price':
			$price      = get_post_meta( $post->ID, 'thim_course_price', true );
			$unit_price = get_post_meta( $post->ID, 'thim_course_unit_price', true );
			echo $price . ' ' . $unit_price;
	}
}

add_filter( 'manage_lp_course_posts_custom_column', 'thim_child_manage_course_columns_content' );
