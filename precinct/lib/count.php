<?php
// add_action('wp_ajax_nopriv_do-count', 'do_count' );
add_action('wp_ajax_do-count', 'do_count');

function do_count() {

  $nonce = $_POST['countNonce'];

  // check to see if the submitted nonce matches with the
  // generated nonce we created earlier
  if ( ! wp_verify_nonce( $nonce, 'count-ajax-nonce' ) )
    die( 'Busted!');


  $blog_id = get_current_blog_id();

  $elections = new WP_Query([
    'post_type' => 'election',
    'posts_per_page' => 1,
    'fields' => 'ids'
  ]);
  $election = $elections->posts;
  $election_id = $election[0];

  $precinct_contests = json_decode(get_option('precinct_contests'), true);
  include(locate_template('/lib/fields-exit-poll.php'));
  $election_results = array();

  $election_results = precinct_votes($blog_id, $election_id, $precinct_contests, $ep_fields, $election_results);

  $uploads = wp_upload_dir();

  file_put_contents(
    $uploads['basedir'] . '/precinct_results.json',
    json_encode($election_results)
  );

  header('Content-Type: application/json');
  echo json_encode($election_results);
  exit;
}

/**
 * Create array of all the votes, plus exit poll answers
 *
 */
function precinct_votes($blog_id, $election_id, $precinct_contests, $ep_fields, $election_results) {
  // Headers for all contests
  foreach ($precinct_contests as $s_key => $section) {
    foreach ($section as $contest) {
      $columns_contests[] = $contest['sanitized_title'];
    }
  }

  // Headers for exit polls + participation numbers by exit poll
  foreach ($ep_fields as $ep_field) {
    $columns_eps[] = $ep_field['id'];
  }

  // Create final column headers
  // $columns = array_merge(['blog_id'], $columns_contests, $columns_eps);
  // $precinct_votes[] = $columns;

  // Make rows for each vote
  $ballots = new WP_Query([
    'post_type' => 'ballot',
    'posts_per_page' => -1
  ]);

  if ($ballots->have_posts()) : while ($ballots->have_posts()) : $ballots->the_post();
    $ballot_id = get_the_id();

    // Get ballot results
    $ballot_responses = get_post_custom();
    $row_votes = array('blog_id' => $blog_id);
    foreach ($columns_contests as $contest) {
      if (isset($ballot_responses[$contest])) {
        $row_votes[$contest] = str_replace(['&lt;br /&gt;', '(', ')', ', Jr'], [' & ', '"', '"', ' Jr'], $ballot_responses[$contest][0]);
      } else {
        $row_votes[$contest] = NULL;
      }
    }

    // Get exit poll result for this voter
    $exit_poll = new WP_Query([
      'post_type' => 'exit-poll',
      'posts_per_page' => 1,
      'meta_query' => [
        [
          'key' => '_cmb_ballot_id',
          'value' => $ballot_id
        ]
      ],
      'fields' => 'ids'
    ]);

    $pollee = $exit_poll->posts;

    if (isset($pollee[0])) {
      $ep_responses = get_post_custom($pollee[0]);

      foreach ($columns_eps as $ep) {
        $row_votes[$ep] = $ep_responses[$ep][0];
        $row_votes_state[$ep] = $ep_responses[$ep][0];
      }
    }

    $precinct_votes[] = $row_votes;
    // $election_results[] = $row_votes_state;
  endwhile; endif; wp_reset_postdata();

  // update_option('precinct_votes', json_encode($precinct_votes));

  return $precinct_votes;
}
