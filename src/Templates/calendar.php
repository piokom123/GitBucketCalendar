<style type="text/css">
    .gitbucket-calendar {
        width: 800px;
        text-align: center;
    }

    .gitbucket-calendar .month {
        font-size: 10px;
        fill: #767676;
    }

    .gitbucket-calendar .wday {
        font-size: 9px;
        fill: #767676;
    }

    .gitbucket-calendar .legend {
        text-align: right;
        font-size: 11px;
        padding: 20px 14px 10px 0px;
        display: inline-block;
        float: right;
    }

    .gitbucket-calendar .legend ul {
        display: inline-block;
        list-style: none;
        margin: 0 5px;
        position: relative;
        bottom: -1px;
        padding: 0;
    }

    .gitbucket-calendar .legend li {
        display: inline-block;
        width: 10px;
        height: 10px;
    }

    .gitbucket-calendar .table-column {
        display: table-cell;
        width: 1%;
        padding: 15px 10px 15px 10px;
        vertical-align: top;
        border-top: 1px solid #ddd;
        font-size: 11px;
    }

    .gitbucket-calendar .table-column-notfirst {
        border-left: 1px solid #ddd;
    }

    .gitbucket-calendar .table-column .text {
        color: #777;
    }

    .gitbucket-calendar .table-column .number {
        font-weight: 300;
        line-height: 1.3em;
        font-size: 24px;
        display: block;
        color: #333;
    }
</style>

<div class="gitbucket-calendar">
    <div>
        <svg width="676" height="104">
            <g transform="translate(16, 20)">
                <g transform="translate(0, 0)">
<?php
    $yOffset = 0;
    $xOffset = 13;
    $weekDaysCount = 0;
    $gTranslate = 0;

    foreach ($contributions['contributions'] as $date => $count) {
?>
                    <rect class="day" width="10" height="10" x="<?php echo $xOffset; ?>" y="<?php echo $yOffset; ?>" fill="<?php echo GitBucketCalendar\Helpers\TemplateHelper::getCellColor($contributions['steps'], $count); ?>" data-count="<?php echo $count; ?>" data-date="<?php echo $date; ?>">
                        <title><?php echo $date; ?> contributions: <?php echo $count; ?></title>
                    </rect>
<?php
        $yOffset += 12;
        $weekDaysCount++;

        if ($weekDaysCount % 7 === 0) {
            $gTranslate += 13;
            $xOffset--;
            $yOffset = 0;
?>
                </g>
                <g transform="translate(<?php echo $gTranslate; ?>, 0)">
<?php
        }
    }
?>
                </g>

                <text x="13" y="-10" class="month">Jan</text>
                <text x="73" y="-10" class="month">Feb</text>
                <text x="121" y="-10" class="month">Mar</text>
                <text x="169" y="-10" class="month">Apr</text>
                <text x="217" y="-10" class="month">May</text>
                <text x="277" y="-10" class="month">Jun</text>
                <text x="325" y="-10" class="month">Jul</text>
                <text x="385" y="-10" class="month">Aug</text>
                <text x="433" y="-10" class="month">Sep</text>
                <text x="481" y="-10" class="month">Oct</text>
                <text x="541" y="-10" class="month">Nov</text>
                <text x="589" y="-10" class="month">Dec</text>

                <text text-anchor="start" class="wday" dx="-14" dy="8" style="display: none;">Sun</text>
                <text text-anchor="start" class="wday" dx="-14" dy="20">Mon</text>
                <text text-anchor="start" class="wday" dx="-14" dy="32" style="display: none;">Tue</text>
                <text text-anchor="start" class="wday" dx="-14" dy="44">Wed</text>
                <text text-anchor="start" class="wday" dx="-14" dy="57" style="display: none;">Thu</text>
                <text text-anchor="start" class="wday" dx="-14" dy="69">Fri</text>
                <text text-anchor="start" class="wday" dx="-14" dy="81" style="display: none;">Sat</text>
            </g>
        </svg>
    </div>

    <div class="legend">
        Less
        <ul>
            <li style="background-color: #eee"></li>
            <li style="background-color: #d6e685"></li>
            <li style="background-color: #8cc665"></li>
            <li style="background-color: #44a340"></li>
            <li style="background-color: #1e6823"></li>
        </ul>
        More
    </div>

    <div style="clear: both"></div>

    <div class="table-column">
        <span class="text">Contributions in the last year</span>
        <span class="number"><?php echo $contributions['contributionsSum']; ?> total</span>
        <span class="text"> </span>
    </div>

    <div class="table-column table-column-notfirst">
        <span class="text">Longest streak</span>
        <span class="number"><?php echo $contributions['longestStreak'] . ' ' . ($contributions['longestStreak'] === 1 ? ' day' : ' days'); ?></span>
        <span class="text"><?php echo $contributions['longestStreakStart']; ?> - <?php echo $contributions['longestStreakEnd']; ?></span>
    </div>

    <div class="table-column table-column-notfirst">
        <span class="text">Current streak</span>
        <span class="number"><?php echo $contributions['latestStreak'] . ' ' . ($contributions['latestStreak'] === 1 ? ' day' : ' days'); ?></span>
        <span class="text"><?php echo $contributions['latestStreakStart']; ?> - <?php echo $contributions['latestStreakEnd']; ?></span>
    </div>
</div>