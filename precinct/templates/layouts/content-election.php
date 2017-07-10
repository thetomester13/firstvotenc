
<div 
  class="single single-election postid-27 logged-in ballot new-local-election-2017"
  style="background: #fff;" 

><!-- ### Remove this ###-->

<article <?php post_class(); ?>>

  <!-- #<p><small>### templates/layouts/content-election.php ###</small></p>-->


  <div class="entry-summary">
    <?php

    if ( isset( $_GET['preview'] ) ) {
      // Display preview ballot
      get_template_part('/templates/layouts/ballot');
    } elseif ( !isset( $_GET['results'] ) ) {
      // Redirect to results view
      wp_redirect( add_query_arg('results', 'general') );
      exit;
    } else {
      // Show results
      get_template_part('/templates/layouts/results');
    }
    //return false;

    /**
     * When election is live
     *
     *
     *
     */

    // Display exit poll
    if ( isset( $_GET['post_submitted'] ) && ( $post = get_post( absint( $_GET['post_submitted'] ) ) ) ) { ?>
		<style>
			.single.ballot .cmb2-metabox{
				column-rule: 0px solid #000;
				border: 0px solid #000;
			}
			.single.ballot .cmb-row{
				border-bottom: 1px solid #c3c6cc; 
				border-top: 0px solid #c3c6cc;
			}
			.single.ballot .cmb2-metabox>.cmb-row{
				padding: 10px;
			}
			.single.exit-poll .cmb2-radio-list li{
				padding-left: 50px;
			}
			.single.ballot .cmb-row li .cmb2-option{
				border: 1px solid #b4b9be;
				background: #fff;
				color: #555;
				clear: none;
				cursor: pointer;
				display: inline-block;
				line-height: 0;
				height: 16px;
				margin: -4px 4px 0 0;
				outline: 0;
				padding: 0!important;
				text-align: center;
				vertical-align: middle;
				width: 16px;
				min-width: 16px;
				-webkit-appearance: none;
				-webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
				box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
				-webkit-transition: .05s border-color ease-in-out;
				transition: .05s border-color ease-in-out;
				margin: 0 5px 0 0;
				position:inherit;
				float:none;
				border-radius: 50%;
			}
			input[type=radio]:checked:before {
				content: "\2022";
				text-indent: -9999px;
				-webkit-border-radius: 50px;
				border-radius: 50px;
				font-size: 24px;
				width: 6px;
				height: 6px;
				margin: 4px;
				line-height: 16px;
				background-color: #1e8cbe;
				float: left;
				display: inline-block;
				vertical-align: middle;
				font: 400 21px/1 dashicons;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
			}
			.single.ballot .cmb-row li label{
				display: inline-block;
				padding-left: 15px;
			}
		</style>
		<?php
		get_template_part('/templates/layouts/exit-poll');
		return false;
    }
	
	
    if ( isset( $_GET['edit'] ) ) { ?>
	   <style>
		   .nav.nav-tabs{   
			   display:none;
		   }
		   body.students{
			   background:#fff;
		   }
		   body:not(.page-template):not(.post-type-archive-data) .alignleft{
				color: #555;
				border-color: #ccc;
				background: #f7f7f7;
				-webkit-box-shadow: 0 1px 0 #ccc;
				box-shadow: 0 1px 0 #ccc;
				vertical-align: top;
				margin-left:0px;
		   }
		   .cmb-repeatable-group .cmb-shift-rows .dashicons{
				height: 1.2em !important;
				width: 1.2em !important;
		   }
		   body:not(.page-template):not(.post-type-archive-data) .alignright{
			   margin-right:0px;
		   }
		   .button-disabled, button.disabled, button:disabled{
			color: #a0a5aa!important;
			border-color: #ddd!important;
			background: #f7f7f7!important;
			-webkit-box-shadow: none!important;
			box-shadow: none!important;
			text-shadow: 0 1px 0 #fff!important;
			cursor: default;
			-webkit-transform: none!important;
			-ms-transform: none!important;
			transform: none!important;
			}
			.cmb2-metabox .cmb-type-group{
				width:100%;
				max-width: 100% !important;
			}
			#_cmb_election input[type=submit]{
				background-color: #24346d;
				color: #fff;
				border: 0px;
				padding: 9px 15px;
				text-align: center;
				display: block;
				position: initial;
				margin: 0 auto;
				-webkit-transform: translate3d(0,0,0);
			}
			.single.ballot .cmb2-metabox{				
				column-count: 1;
				border: 0px solid #000;
			}
			.single.ballot .cmb-row{
				border-bottom: 1px solid #e9e9e9;
			}
			span.button.cmb-multicheck-toggle{
				color: #555;
				border-color: #ccc;
				background: #f7f7f7;
				-webkit-box-shadow: 0 1px 0 #ccc;
				box-shadow: 0 1px 0 #ccc;
				vertical-align: baseline;
				white-space: nowrap;
				display: inline-block;
				text-decoration: none;
				font-size: 13px;
				line-height: 26px;
				height: 28px;
				margin: 0;
				padding: 0 10px 1px;
				cursor: pointer;
				border-width: 1px;
				border-style: solid;
				-webkit-appearance: none;
				-webkit-border-radius: 3px;
				border-radius: 3px;
				white-space: nowrap;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}
			.single.ballot .cmb-row li{
				padding-left: 50px;
			}
			.single.ballot .cmb-row li .cmb2-option{
				border: 1px solid #b4b9be;
				background: #fff;
				color: #555;
				clear: none;
				cursor: pointer;
				display: inline-block;
				line-height: 0;
				height: 16px;
				margin: -4px 4px 0 0;
				outline: 0;
				padding: 0!important;
				text-align: center;
				vertical-align: middle;
				width: 16px;
				min-width: 16px;
				-webkit-appearance: none;
				-webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
				box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
				-webkit-transition: .05s border-color ease-in-out;
				transition: .05s border-color ease-in-out;
				margin: 0 5px 0 0;
				position:inherit;
			}
			input[type=checkbox]:checked:before {
				content: "\f147";
				margin: -3px 0 0 -4px;
				color: #1e8cbe;
				float: left;
				display: inline-block;
				vertical-align: middle;
				width: 16px;
				font: 400 21px/1 dashicons;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
			}
			.single.ballot .cmb-row li label{
				display: inline-block;
				padding-left: 15px;
			}
	  </style>
	<?php
      // Check if the user has permissions to edit elections
      if ( ! current_user_can( 'editor' ) ) {
        wp_redirect( get_the_permalink() );
        exit;
      }

      // If edit was saved, delete generated ballot and redirect to non-edit page
    	if ( isset( $_POST['object_id'] ) ) {
        update_post_meta( $_POST['object_id'], '_cmb_generated_ballot', '' );
        $url = esc_url_raw( get_bloginfo('url') );
    		echo "<script type='text/javascript'>window.location.href = '$url?manage'</script>";
    	}
		
      // Customize ballot settings -- for teachers
      cmb2_metabox_form( '_cmb_election', get_the_id(), ['save_button' => 'Save Election'] );
      return false;
    }
	// I Voted! overlay with TurboVote signup
	if ( isset( $_GET['thank_you'] ) ) {
	  get_template_part('/templates/layouts/i-voted');
	  return false;
	}
	
	get_template_part('/templates/layouts/ballot');
	
    if ( isset( $_GET['results'] ) ) {
      get_template_part('/templates/layouts/results');
      return false;
    }

    // Display live ballot
    //get_template_part('/templates/layouts/ballot');
    ?>

  </div>
</article>

</div>