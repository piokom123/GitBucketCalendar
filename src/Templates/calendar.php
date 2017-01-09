<style type="text/css">
    .calendar-graph .month {
        font-size: 10px;
        fill: #767676;
    }

    .calendar-graph .wday {
        font-size: 9px;
        fill: #767676;
    }
</style>

<svg width="676" height="104" class="calendar-graph">
    <g transform="translate(16, 20)">
        <g transform="translate(0, 0)">
<?php
    $yOffset = 0;
    $xOffset = 13;
    $weekDaysCount = 0;
    $gTranslate = 0;

    foreach ($contributions as $date => $count) {
?>
            <rect class="day" width="10" height="10" x="<?php echo $xOffset; ?>" y="<?php echo $yOffset; ?>" fill="<?php echo GitBucketCalendar\Helpers\TemplateHelper::getCellColor($steps, $count); ?>" data-count="<?php echo $count; ?>" data-date="<?php echo $date; ?>" />
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
