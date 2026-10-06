<?php
// Each chart keeps its labels and values available to assistive technology.
$chartMax=max(1,...array_column($chartItems,'value'));
$chartStep=max(1,(int)ceil($chartMax/5));
$chartCeiling=$chartStep*5;
?>
<?php if(!$chartItems): ?><p class="empty">Ainda não há dados para exibir.</p><?php else: ?>
<div class="column-chart-scroll"><div class="column-chart" style="--columns:<?= count($chartItems) ?>">
<div class="chart-axis" aria-hidden="true"><?php for($tick=5;$tick>=0;$tick--): ?><span><?= $tick*$chartStep ?></span><?php endfor ?></div>
<div class="chart-columns">
<?php foreach($chartItems as $item): ?><div class="chart-column"><div class="column-area"><div class="column-bar" style="height:<?= 100*$item['value']/$chartCeiling ?>%;--bar-color:<?= e($item['color']??'#91cef4') ?>"><span><?= (int)$item['value'] ?></span></div></div><span class="column-label"><?= e($item['label']) ?></span></div><?php endforeach ?>
</div></div></div>
<?php endif ?>
