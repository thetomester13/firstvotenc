<?php

use Roots\Sage\Extras;
use Roots\Sage\Titles;

$type = $_GET['results'];

if (isset($_GET['contest'])) {
  get_template_part('templates/layouts/results', 'contest');
} elseif ($_GET['results'] == 'precincts') {
  get_template_part('templates/layouts/results', 'precincts');
}  elseif ($type == 'participation') {
  get_template_part('templates/layouts/results', 'participation');
} else {
  ?>

  <script src="http://code.highcharts.com/highcharts.js"></script>
  <script type="text/javascript" src="http://code.highcharts.com/modules/data.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>
  <script src="http://code.highcharts.com/modules/offline-exporting.js"></script>

  <script type="text/javascript">
    Highcharts.setOptions({
      lang: {
        thousandsSep: ","
      }
    });
  </script>

  <?php
  $contests = json_decode(get_option('precinct_contests'), true);

  $uploads = wp_upload_dir();
  $uploads_global = network_site_url('wp-content/uploads');
  if ( false === ( $results_json = get_option( 'precinct_votes' ) ) ) {
    $results_file = wp_remote_get($uploads['baseurl'] . '/precinct_results.json');
    //$results_file = json_decode(file_get_contents($uploads_global . '/precinct_results.json'), true);
    $results_json = $results_file['body'];
	//print_r($uploads['baseurl'] . ' test' );
  }
  $results = json_decode($results_json, true);
  $statewide = json_decode(file_get_contents($uploads_global . '/election_results.json'), true);

//echo '<pre>';
 //print_r($results);
 //print_r($uploads['baseurl'] . ' test' );
// // print_r(array_keys($results[150]));
//  echo '</pre>';

  $races = array_keys($results[0]);
  $races_statewide = array_keys($statewide[0]);

  foreach ($races as $race) {
    if (substr($race, 0, 11) == '_cmb_ballot') {

      $match = Extras\array_find_deep($contests, $race);

      // Only show type of results for the tab we're on
      if ($type == 'general') {
        if (!in_array($race, $races_statewide) || isset($contests[$match[0][0][0]][$race]['question'])) {
          continue;
        }
      } elseif ($type == 'local') {
        if (in_array($race, $races_statewide) || isset($contests[$match[0][0][0]][$race]['question'])) {
          continue;
        }
      } elseif ($type == 'issues') {
        if (!isset($contests[$match[0][0][0]][$race]['question'])) {
          continue;
        }
      }

      $data = array_column($results, $race);
      $data_state = array_column($statewide, $race);

      // If data is JSON string, unserialize it
      if (FALSE !== unserialize($data[0])) {
        $flat_data = array();
        foreach ($data as $multiple) {
          $encoded = unserialize($multiple);
          $array = unserialize(html_entity_decode($encoded));
          $flat_data = array_merge(array_values($flat_data), array_values($array));
        }
        $data = $flat_data;
      }

      // If data is JSON string, unserialize it
      if (FALSE !== unserialize($data_state[0])) {
        $flat_data = array();
        foreach ($data_state as $multiple) {
          $encoded = unserialize($multiple);
          $array = unserialize(html_entity_decode($encoded));
          $flat_data = array_merge(array_values($flat_data), array_values($array));
        }
        $data_state = $flat_data;
      }

      // Total number of ballots cast
      $total = count($data) - count(array_keys($data, NULL));
      $total_state = count($data_state) - count(array_keys($data_state, NULL));

      // Set up arrays
      $count = array();
      $count_state = array();

      // Count number of votes per contestant
      if (isset($contests[$match[0][0][0]][$race]['candidates'])) {
        foreach ($contests[$match[0][0][0]][$race]['candidates'] as $candidate) {
          $tally = count(array_keys($data, $candidate['name']));
          $tally_state = count(array_keys($data_state, $candidate['name']));

          // Precinct count
          $count[] = array(
            'name' => $candidate['name'],
            'party' => $candidate['party'],
            'count' => $tally,
            'percent' => round(($tally / $total) * 100, 2)
          );

          if ($type !== 'local' && in_array($race, $races_statewide)) {
            // Statewide count
            $count_state[] = array(
              'name' => $candidate['name'],
              'party' => $candidate['party'],
              'count' => $tally_state,
              'percent' => round(($tally_state / $total_state) * 100, 2)
            );
          }
        }
      } else {
        foreach ($contests[$match[0][0][0]][$race]['options'] as $option) {
          $tally = count(array_keys($data, $option));
          $tally_state = count(array_keys($data_state, $option));

          // Precinct count
          $count[] = array(
            'name' => $option,
            'count' => $tally,
            'percent' => round(($tally / $total) * 100, 2)
          );

          if ($type !== 'local' && in_array($race, $races_statewide)) {
            // Statewide count
            $count_state[] = array(
              'name' => $option,
              'count' => $tally_state,
              'percent' => round(($tally_state / $total_state) * 100, 2)
            );
          }
        }
      }

      if ($type !== 'issues') {
        // Total number of 'no selection' votes
        $tally_none = count(array_keys($data, 'none'));
        $tally_none_state = count(array_keys($data_state, 'none'));
        $count[] = array(
          'name' => 'No Selection',
          'party' => 'no-selection',
          'count' => $tally_none,
          'percent' => round(($tally_none / $total) * 100, 2)
        );
        $count_state[] = array(
          'name' => 'No Selection',
          'party' => 'no-selection',
          'count' => $tally_none_state,
          'percent' => round(($tally_none_state / $total_state) * 100, 2)
        );
      }

      if (isset($contests[$match[0][0][0]][$race]['number'])) {
        $number = $contests[$match[0][0][0]][$race]['number'];
      } else {
        $number = 1;
      }

      if (isset($contests[$match[0][0][0]][$race]['question'])) {
        $question = $contests[$match[0][0][0]][$race]['question'];
      }
      ?>

      <div class="row">
        <div class="<?php if ($type == 'local' || !in_array($race, $races_statewide)) { echo 'col-sm-4'; } else { echo 'col-sm-12'; } ?>">
          <h2 class="h3">
            <?php
            echo $contests[$match[0][0][0]][$race]['title'] . ' ' . $contests[$match[0][0][0]][$race]['district'];
            if (isset($question)) {
              echo ' <small>' . $question . '</small>';
            }
            if (!empty($number) && !is_numeric($number)) {
              echo ' <small>' . $number . '</small>';
            } ?>
          </h2>
          <?php if (is_numeric($number) && $number > 1) { ?>
            <h3 class="h6"><?php echo $number; ?> Winners</h3>
          <?php } ?>
          <a class="btn btn-gray" href="<?php echo add_query_arg('contest', $race); ?>">Explore these results by exit poll</a>
        </div>

        <div class="<?php if ($type == 'local' || !in_array($race, $races_statewide)) { echo 'col-sm-8'; } else { echo 'col-sm-6 extra-bottom-margin'; } ?>">
          <div class="entry-content-asset">
            <div id="<?php echo $race; ?>" class="result-chart"></div>
          </div>
        </div>

        <?php if ($type !== 'local' && in_array($race, $races_statewide)) { ?>
          <div class="col-sm-6 extra-bottom-margin">
            <div class="entry-content-asset">
              <div id="state<?php echo $race; ?>" class="result-chart statewide"></div>
            </div>
          </div>
        <?php } ?>
      </div>

      <script type="text/javascript">
        new Highcharts.Chart({
          chart: { renderTo: '<?php echo $race; ?>', defaultSeriesType: 'bar' },
          credits: {enabled: false},
          title: { text: "<?php echo $contests[$match[0][0][0]][$race]['title'] . ' ' . $contests[$match[0][0][0]][$race]['district']; ?><br />(Precinct Results)", useHTML: true },
          <?php if (is_numeric($number) && $number > 1) { ?>
            subtitle: { text: "<?php echo $number; ?> Winners", useHTML: true },
          <?php } ?>
          <?php if (!empty($number) && !is_numeric($number)) { ?>
            subtitle: { text: "<?php echo $number; ?>", useHTML: true },
          <?php } ?>
          <?php if (isset($question)) { ?>
            subtitle: { text: "<?php echo $string = trim(preg_replace('/\s+/', ' ', $question)); ?>", useHTML: true },
          <?php } ?>
          xAxis: { type: 'category', tickWidth: 0, labels: { useHTML: true } },
          yAxis: { title: {enabled: false}, gridLineWidth: 0, labels: {enabled: false} },
          plotOptions: { bar: { dataLabels: { enabled: true, format: '{point.y:,.0f} votes ({point.percent:.2f}%)', inside: true, align: 'left', useHTML: true } } },
          legend: { enabled: false },
          tooltip: { enabled: false },
          series: [{ data: [<?php foreach ($count as $c) { ?>
              {
                name: '<?php echo str_replace(' & ', '<br />', $c['name']); ?><?php if (!empty($c['party'])) { echo '<br />(' . $c['party'] . ')'; } ?>',
                y: <?php echo $c['count']; ?>,
                className: '<?php if (isset($c['party'])) echo sanitize_title($c['party']); ?>',
                percent: <?php echo $c['percent']; ?>
                // animation: false
              },
            <?php } ?>]
          }]
        });

        <?php if ($type !== 'local' && in_array($race, $races_statewide)) { ?>
          new Highcharts.Chart({
            chart: { renderTo: 'state<?php echo $race; ?>', defaultSeriesType: 'bar' },
            credits: {enabled: false},
            title: { text: "<?php echo $contests[$match[0][0][0]][$race]['title'] . ' ' . $contests[$match[0][0][0]][$race]['district']; ?><br />(Statewide Results)", useHTML: true },
            <?php if (is_numeric($number) && $number > 1) { ?>
              subtitle: { text: "<?php echo $number; ?> Winners", useHTML: true },
            <?php } ?>
            <?php if (!empty($number) && !is_numeric($number)) { ?>
              subtitle: { text: "<?php echo $number; ?>", useHTML: true },
            <?php } ?>
            <?php if (isset($question)) { ?>
              subtitle: { text: "<?php echo $string = trim(preg_replace('/\s+/', ' ', $question)); ?>", useHTML: true },
            <?php } ?>
            xAxis: { type: 'category', tickWidth: 0, labels: { useHTML: true } },
            yAxis: { title: {enabled: false}, gridLineWidth: 0, labels: {enabled: false} },
            plotOptions: { bar: { dataLabels: { enabled: true, format: '{point.y:,.0f} votes ({point.percent:.2f}%)', inside: true, align: 'left', useHTML: true } } },
            legend: { enabled: false },
            tooltip: { enabled: false },
            series: [{ data: [<?php foreach ($count_state as $cs) { ?>
                {
                  name: '<?php echo str_replace(' & ', '<br />', $cs['name']); ?><?php if (!empty($cs['party'])) { echo '<br />(' . $cs['party'] . ')'; } ?>',
                  y: <?php echo $cs['count']; ?>,
                  className: '<?php if (isset($cs['party'])) echo sanitize_title($cs['party']); ?>',
                  percent: <?php echo $cs['percent']; ?>
                  // animation: false
                },
              <?php } ?>]
            }]
          });
        <?php } ?>
      </script>
      <?php
    }
  }
}
