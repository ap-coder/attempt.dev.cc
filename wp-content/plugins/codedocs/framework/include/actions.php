<?php

if( ! function_exists('print_feedback_function')){

	function print_feedback_function(){

		$string = '<ul>';

		if( isset( $_SESSION['feedback'] ) )
		{
			foreach( $_SESSION['feedback'] as $key=>$feedback )
			{

				if( $key == 'validation' )
				{
					
					foreach($feedback as $fb)
					{
						foreach($fb as $f)
						{
							$string .= '<li><i class="fa fa-close"></i> &nbsp;'.$f.'</li>';
						}
					}
				
				}

				if( $key == 'message' )
				{

					$string .= '<li>'.$feedback.'</li>';
				
				}
			}
		}

		$string .= '</ul>';
		
		echo $string;

		session_destroy();
	}
}

add_action('print_feedback', 'print_feedback_function');