<?php
    require_once('config.php');
    if (isset($_GET['uh'])) :
        $user_hash = $_GET['uh'];
        $get_user_details = $config->link->query("SELECT * FROM `users` WHERE `user_hash` = '{$user_hash}'");
        $user_details = $get_user_details->fetch();
?>

<!DOCTYPE html>
<html lang="en" dir="RTL">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="stylesheets/general/main.css">
        <link rel="stylesheet" href="stylesheets/profile/profile.css">
        <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://use.fontawesome.com/6b4470aa21.js"></script>
        <title>
        <?php
            if ($get_user_details->rowCount() == 1) {
                echo $user_details['nickname'];
            } else {
                echo '404 פרופיל לא נמצא';
            }
        ?> - <?php echo $config->website_name; ?></title>
    </head>
    <body>
        <?php include 'includes/nav.php'; ?>
        <div class="site-wrap">
            <?php if ($get_user_details->rowCount() == 1) : ?>
                    <?php include 'modules/general/profile-right-sidebar.php'; ?>
            <?php else : ?>
                <?php include 'modules/general/right-sidebar.php'; ?>
            <?php endif; endif; ?>

            <div class="grid__col grid__col--7-of-14">
                <?php if ($get_user_details->rowCount() == 1) : ?>
                    <!-- User statistics -->
                    
                    <div id="answers-chars-trigger-scale">
                        <div class="trigger" data-tabid="answers-chart-day">יום</div>
                        <div class="trigger" data-tabid="answers-chart-month">חודש</div>
                    </div>
                    <div class="chart-container" id="answers-charts" style="position: relative; height:350px; width:100%">
                        <canvas id="answers-chart-month" class="chart-tab"></canvas>
                        <canvas id="answers-chart-day" class="chart-tab active"></canvas>
                    </div>

                    <div id="common-tags-chart-container" style="width: 350px; height: 350px; margin: auto;">
                        <canvas height="150px" width="150px" id="common-tags-chart">
                    </div>
                <?php else : ?>
                    <h2>404</h2>
                    <p>פרופיל לא קיים</p>
                    <a href="index.php">חזור לדף הבית</a>
                <?php endif; ?>
            </div>

            <?php include 'modules/general/left-sidebar.php'; ?>
        </div>

        <?php
            // User Answers charts
            $users_monthly_answers = $config->link->query("SELECT MONTH(`date`) AS `month`, COUNT(*) as `count` FROM `public_consultation_answers` WHERE `poster_hash` = '{$user_hash}' AND YEAR(`date`) = YEAR(CURDATE()) GROUP BY month"); 
            $users_monthly_answers_labels = "";
            $users_monthly_answers_data = "";

            while ($month = $users_monthly_answers->fetch()) {
                $users_monthly_answers_labels .= "'" . $config->numberToMonthName($month['month']) . "', ";
                $users_monthly_answers_data .= "'" . $month['count'] . "', ";
            }

            $users_daily_answers = $config->link->query("SELECT DAY(`date`) AS `day`, COUNT(*) as `count` FROM `public_consultation_answers` WHERE `poster_hash` = '{$user_hash}' AND MONTH(`date`) = MONTH(CURDATE()) GROUP BY day"); 
            $users_daily_answers_labels = "";
            $users_daily_answers_data = "";

            while ($day = $users_daily_answers->fetch()) {
                $users_daily_answers_labels .= "'" . $day['day'] . "', ";
                $users_daily_answers_data .= "'" . $day['count'] . "', ";
            }

            // User tags charts
            $tags_to_display = [];
            $user_common_tags = $config->link->query("SELECT * FROM `public_consultation_answers` LEFT OUTER JOIN `consultations_tags` ON public_consultation_answers.consultation_hash = consultations_tags.consultation_hash WHERE public_consultation_answers.poster_hash = '{$_SESSION['user_hash']}'");
            while ($tag = $user_common_tags->fetch()) {
                if ($tag['tag_hash'] != NULL) {
                    if (count($tags_to_display) <= 5 || in_array($tag, $tags_to_display)) {
                        if (in_array($tag['tag_hash'], $tags_to_display)) {
                            $tags_to_display[$tag['tag_hash']] += 1;
                        } else {
                            $tags_to_display[$tag['tag_hash']] = 1;
                        }
                    }
                }
            }

            $user_common_tags_chart_data = "";
            $user_common_tags_chart_labels = "";
            foreach ($tags_to_display as $tag_hash => $data) {
                $user_common_tags_chart_labels .= "'" . $config->get_tag_details_by_hash($tag_hash)['title'] . "', ";
                $user_common_tags_chart_data .= "'" . $data . "', ";
            }
        ?>

        <script src="scripts/jquery.query-object.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>
        <script>
            new Chart(document.getElementById("answers-chart-month"),
            {"type":"line",
            "data": {
                "labels": [<?php echo $users_monthly_answers_labels; ?>],
                "datasets":[{
                    "label": "מספר תשובות לפי חודש",
                    "data": [<?php echo $users_monthly_answers_data; ?>],
                    "fill": 'start',"borderColor": "rgb(75, 192, 192)",
                    "lineTension": 0.1
                }]
            },
                "options": {}
            });

            new Chart(document.getElementById("answers-chart-day"),
            {"type":"bar",
            "data": {
                "labels": [<?php echo $users_daily_answers_labels; ?>],
                "datasets":[{
                    "label": "מספר תשובות לפי יום",
                    "data": [<?php echo $users_daily_answers_data; ?>],
                    "fill": false,"backgroundColor": "rgba(58, 92, 149, 0.7)",
                    "lineTension": 0.1
                }]
            },
                options : {
                    legend: {
                        display: false
                    },
                }
            });

            // And for a doughnut chart
            var myDoughnutChart = new Chart(document.getElementById("common-tags-chart"), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [<?php echo $user_common_tags_chart_data; ?>],
                        backgroundColor: ['#FFCD56', '#36A2EB', '#FF6384', '#6FB76F', '#BFCCC9']
                    }],

                    // These labels appear in the legend and in the tooltips when hovering different arcs
                    labels: [<?php echo $user_common_tags_chart_labels; ?>]
                }
            });
        </script>
        <script src="scripts/main.js"></script>
        <script src="scripts/profile.js"></script>
    </body>
</html>