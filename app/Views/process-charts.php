<?php
$processItems=$page==='andamento'
    ? [['label'=>'Pendentes','value'=>$counts['Pendente']??0,'color'=>'#f4c755'],['label'=>'Em andamento','value'=>($counts['Aprovada']??0)+($counts['Em andamento']??0),'color'=>'#8125b6']]
    : array_map(fn($s)=>['label'=>$s,'value'=>$counts[$s]??0,'color'=>['Pendente'=>'#f4c755','Aprovada'=>'#b38bd3','Em andamento'=>'#8125b6','Entregue'=>'#64b997','Recusada'=>'#e38b96'][$s]],['Pendente','Aprovada','Em andamento','Entregue','Recusada']);
$processTotal=array_sum(array_column($processItems,'value')); $offset=0; $stops=[];
foreach($processItems as $item) { $end=$offset+($processTotal?$item['value']/$processTotal*100:0); $stops[]=$item['color'].' '.$offset.'% '.$end.'%'; $offset=$end; }
?>
<div class="analytics-grid">
<section class="card analytics-card"><div class="section-heading"><div><h2>Distribuição dos processos</h2><p>Visualização dos pedidos por status.</p></div><span class="chart-symbol" aria-hidden="true">◔</span></div>
<div class="donut-chart" role="img" aria-label="<?= (int)$processTotal ?> solicitações no total. Valores por status na legenda abaixo." style="background:<?= $processTotal?'conic-gradient('.implode(',',$stops).')':'#ece8f1' ?>"><div><strong><?= (int)$processTotal ?></strong><small><?= $processTotal?'solicitações':'Sem solicitações' ?></small></div></div>
<ul class="chart-legend"><?php foreach($processItems as $item): ?><li><i style="background:<?= e($item['color']) ?>" aria-hidden="true"></i><?= e($item['label']) ?> <strong><?= (int)$item['value'] ?></strong></li><?php endforeach ?></ul></section>
<section class="card analytics-card"><div class="section-heading"><div><h2>Comparação de solicitações</h2><p>Quantidade de pedidos em cada etapa.</p></div><span class="chart-symbol" aria-hidden="true">▥</span></div><?php $chartItems=$processItems; require __DIR__.'/vertical-chart.php'; ?><?php if($page==='andamento'): ?><small class="muted">Em andamento inclui solicitações aprovadas.</small><?php endif ?></section>
</div>
