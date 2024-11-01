<?php

/**
 * WordPressPastebinWidgetRecent Class
 *
 * @since 0.0.2
 */
class WordPressPastebinWidgetRecent extends WP_Widget {

	function WordPressPastebinWidgetRecent() {
		$widget_ops = array(
			'description' => __( 'List your recent pastes', 'wordpress-pastebin' )
		);
		$control_ops = array(
			'id_base' => 'wordpress_pastebin'
		);
		$this->WP_Widget(
			'wordpress_pastebin',
			__( 'WP Pastebin Recent', 'wordpress-pastebin' ),
			$widget_ops,
			$control_ops
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title	= apply_filters( 'widget_title', $instance['title'] );
		$number	= $instance['number'];
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		$this->loop( $number );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function input( $title, $name, $size, $value ) { ?>
		<label for="<?php echo $this->get_field_id( $name ); ?>"><?php _e( $title ); ?><br />
			<input id="<?php echo $this->get_field_id( $name ); ?>" name="<?php echo $this->get_field_name( $name ); ?>" type="text" value="<?php echo $value; ?>" size="<?php echo $size ?>" />
		</label> <?php
	}

	function form( $instance ) {
		$defaults = array(
			'config'	=> array(
				'title'		=> __( 'Recent Pastes', 'wordpress-pastebin' ),
				'number'	=> '5'
			)
		);
		$instance = wp_parse_args( (array) $instance, $defaults['config'] );
		$title = esc_attr( $instance['title'] );
		$number = esc_attr( $instance['number'] ); ?>
		<p> <?php
			$this->input(
				__( 'Title:', 'wordpress-pastebin' ),
				'title',
				15,
				$title
			) ?>
		</p>
		<p> <?php
			$this->input(
				__( 'Show how many pastes:', 'wordpress-pastebin' ),
				'number',
				2,
				$number
			) ?>
		</p> <?php
	}

	function loop( $number = 5) {
		$my_query = new WP_Query( array(
			'post_type'			=> 'paste',
			'post_status'		=> 'publish',
			'posts_per_page'	=> $number,
			'suppress_filters'	=> true
		) );
		if ( $my_query->have_posts() ) {
			echo '<ul class="loop">';
			while ( $my_query->have_posts() ) {
				$my_query->the_post();
				echo '<li><a href="' . get_permalink( get_the_ID() ) . '">';
				the_title();
				echo '</a></li>';
			}
			echo '</ul>';
		}
		else {
			_e( 'No pastes found', 'wordpress-pastebin' );
		}
	}

}
