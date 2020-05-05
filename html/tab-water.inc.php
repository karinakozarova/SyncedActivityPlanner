<link rel="stylesheet" href="../css/waterTab.css">
<?php
include('../backend/getDailyWaterIntake.php');
?>
<form action="../backend/addDailyCups.php" method="post">
    <label for="quantity">
        Number of cups today(250ml):
    </label> <br>
    <input type="number" id="quantity" name="quantity" min="1" value="<?= $waterCups ?>">
    <input type="submit">
</form>
<h4> Water statistics</h4>
<div class="chart-wrapper chart-container" id="activity-chart-container"
     style="position: relative; height:40vh; width:60vw">
    <canvas id="line-chartcanvas" width="200px" height="80px"></canvas>
</div>

</div>
<?php
$date = date('Y-m-d');
$dayOfWeek = date("l", strtotime($date));
//Print out the day that our date fell on
$daynum = date("w", strtotime($dayOfWeek));
    if ($dayOfWeek = "Sunday") $daynum = 6;
$dates = [];

for ($i = (int)$daynum; $i >= 0; $i--) {
    $ago = '-' . $i . ' days';
    $thisDate = date("Y-m-d", strtotime($ago, strtotime($date)));
        $dates[] = $thisDate;
}
?>
<script>
    let inputData = [0, 0, 0, 0, 0, 0, 0];
    let dates = [];
    let dayNames = [];
    dates = <?= json_encode($dates)?>;
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    dates.forEach(function (item, index) {
        $.ajax({
            type: 'get',
            url: '../backend/getWaterIntake.php?date=' + item,
            date: item,
            success: function (res) {
                inputData[index] = res;
            }
        });
        const currentDay = new Date(item);
        dayNames[index] = days[currentDay.getDay()];
    });

    $(function () {
        let ctx = $("#line-chartcanvas");
        let dailyGoal = <?= $waterCupsGoals ?>;
        const goals = [dailyGoal, dailyGoal, dailyGoal, dailyGoal, dailyGoal, dailyGoal, dailyGoal];
        const data = {
            labels: dayNames,
            datasets: [
                {
                    label: "Water Intake",
                    data: inputData,
                    backgroundColor: "#552244",
                    borderColor: "#552244",
                    fill: false,
                    lineTension: 0,
                    radius: 5
                },
                {
                    label: "Your goals",
                    data: goals,
                    backgroundColor: "#1abc9c",
                    borderColor: "#1abc9c",
                    fill: false,
                    lineTension: 3,
                    radius: 0,

                }
            ]
        };

        //options
        const options = {
            responsive: true,
            title: {
                display: true,
                position: "top",
                fontSize: 18,
                fontColor: "#111"
            },
            legend: {
                display: true,
                position: "bottom",
                labels: {
                    fontColor: "#333",
                    fontSize: 16
                }
            }
        };

        //create Chart class object
        const chart = new Chart(ctx, {
            type: "line",
            data: data,
            options: options
        });
    });
</script>