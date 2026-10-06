<?php
// CLI only. Uses a dedicated test database; never touches tcc.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/conexao.php';
require __DIR__ . '/../app/Models/EpiModel.php';
putenv('DB_NAME=epi_control_test');
$port=(int)(getenv('DB_PORT')?:3318);
$server=new mysqli('127.0.0.1',getenv('DB_USER')?:'root',getenv('DB_PASSWORD')?:'', '', $port);
$server->query('DROP DATABASE IF EXISTS epi_control_test');
$server->query('CREATE DATABASE epi_control_test CHARACTER SET utf8mb4');
$db=conectar();
function sqlFile(mysqli $db,string $path): void {
    $db->multi_query(file_get_contents($path));
    do { if($r=$db->store_result()) $r->free(); } while($db->more_results() && $db->next_result());
}
sqlFile($db,__DIR__.'/../database/tcc.sql');
$m=new EpiModel($db); $checks=0;
function check(bool $ok,string $label): void { global $checks; if(!$ok) throw new RuntimeException('FAIL: '.$label); $checks++; echo "PASS: $label\n"; }
function rejects(callable $action,string $label): void { try {$action();} catch(DomainException $e) {check(true,$label);return;} check(false,$label); }
check((int)$m->one('SELECT quantidade FROM tb_solicitacoes WHERE id_solicitacao=1')['quantidade']===2,'Complete SQL imports request quantities');
$before=(int)$m->one('SELECT quantidade_estoque FROM tb_epis WHERE id_epi=1')['quantidade_estoque'];
$m->run('INSERT INTO tb_solicitacoes (id_funcionario,id_epi,quantidade,justificativa) VALUES (2,1,2,?)',['Teste de entrega']); $request=$db->insert_id;
$m->changeRequest($request,'Aprovada'); $m->changeRequest($request,'Em andamento'); $m->changeRequest($request,'Entregue');
check((int)$m->one('SELECT quantidade_estoque FROM tb_epis WHERE id_epi=1')['quantidade_estoque']===$before-2,'Delivery decrements stock');
check(count(array_filter($m->history(2),fn($h)=>$h['motivo']==='Entrega da solicitação #'.$request))===1,'Delivery creates employee history');
rejects(fn()=>$m->changeRequest($request,'Entregue'),'Repeated delivery is rejected');
$m->run('INSERT INTO tb_solicitacoes (id_funcionario,id_epi,quantidade,justificativa) VALUES (2,1,999999,?)',['Teste estoque']); $large=$db->insert_id;
$m->changeRequest($large,'Aprovada'); rejects(fn()=>$m->changeRequest($large,'Entregue'),'Insufficient stock is rejected');
check($m->one('SELECT status_solicitacao FROM tb_solicitacoes WHERE id_solicitacao=?',[$large])['status_solicitacao']==='Aprovada','Failed delivery rolls back status');
check((int)$m->one('SELECT quantidade_estoque FROM tb_epis WHERE id_epi=1')['quantidade_estoque']===$before-2,'Failed and duplicate deliveries do not change stock');
$m->run('INSERT INTO tb_pedidos (id_epi,quantidade,observacao) VALUES (1,3,?)',['Teste compra']); $purchase=$db->insert_id;
$m->changePurchase($purchase,'Comprado'); rejects(fn()=>$m->changePurchase($purchase,'Comprado'),'Repeated purchase is rejected');
check((int)$m->one('SELECT quantidade_estoque FROM tb_epis WHERE id_epi=1')['quantidade_estoque']===$before+1,'Purchase increments stock exactly once');
check(count(array_filter($m->history(),fn($h)=>$h['motivo']==='Recebimento da compra #'.$purchase))===1,'Purchase creates an entry');
rejects(fn()=>$m->assign(2,1,0,'Invalid'),'Zero delivery is rejected');
rejects(fn()=>$m->assign(1,1,1,'Invalid'),'Delivery to administrator is rejected');
rejects(fn()=>$m->deleteEmployee(2),'Employee history is preserved on deletion');
check(count(array_filter($m->requests(2),fn($r)=>(int)$r['id_funcionario']!==2))===0,'Employee requests are scoped');

// HTTP tests require the server to use DB_NAME=epi_control_test and this same port.
$base=getenv('TEST_URL')?:'http://127.0.0.1:8098';
$jars=[];
function http(string $path,?array $post=null,string $session='admin'): array {
    global $base,$jars;
    $jars[$session]??=tempnam(sys_get_temp_dir(),'epi-cookie-');
    $c=curl_init($base.'/'.$path); curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_COOKIEJAR=>$jars[$session],CURLOPT_COOKIEFILE=>$jars[$session],CURLOPT_TIMEOUT=>15]);
    if($post!==null) {curl_setopt($c,CURLOPT_POST,true);curl_setopt($c,CURLOPT_POSTFIELDS,http_build_query($post));}
    $body=curl_exec($c); $code=curl_getinfo($c,CURLINFO_HTTP_CODE); if($body===false) throw new RuntimeException(curl_error($c)); curl_close($c); return [$code,$body];
}
function token(string $html): string { preg_match('/name="csrf" value="([^"]+)"/',$html,$match); return $match[1]??''; }
function loginTest(string $email,string $session): void {
    [$code,$body]=http('login.php',null,$session); check($code===200,'Login page loads ('.$session.')');
    [$code]=http('login.php',['csrf'=>token($body),'usuario'=>$email,'senha'=>'123456'],$session); check($code===302,'Login succeeds ('.$session.')');
}
[$code]=http('home.php',null,'anonymous');check($code===302,'Anonymous users redirected');
loginTest('admin@epicontrol.com','admin'); loginTest('joaoteste@gmail.com','employee');
check(password_verify('123456',$m->user(2)['senha']),'Authenticated password is stored as a valid hash');
foreach(['home','cadastro','funcionarios','relatorios','estoque','pedidos','fazerpedidos','compras','movimentacoes','andamento','perfil','informacoes.php?id=2','editarfunc.php?id=2','adicionarepi.php?id=2','excluirfunc.php?id=2'] as $route) {
    $path=str_contains($route,'.php')?$route:$route.'.php'; [$code,$body]=http($path); check($code===200 && !str_contains($body,'Fatal error'),'Admin page '.$path);
}
foreach(['homefunc','meuepi','solicitar','minhassolicitacoes','perfil'] as $route) {[$code,$body]=http($route.'.php',null,'employee');check($code===200,'Employee page '.$route);}
foreach(['home','cadastro','funcionarios','relatorios','estoque','pedidos','fazerpedidos','compras','movimentacoes','andamento','editarfunc','excluirfunc','informacoes','adicionarepi','salvarpedido','atualizarpedido','excluirmovimentacao'] as $route) {[$code]=http($route.'.php',null,'employee');check($code===403,'Employee forbidden '.$route);}
foreach(['homefunc','meuepi','solicitar','minhassolicitacoes'] as $route) {[$code]=http($route.'.php');check($code===403,'Admin forbidden '.$route);}
[$code]=http('solicitar.php',['id_epi'=>1,'quantidade'=>1,'justificativa'=>'Invalid CSRF'],'employee');check($code===403,'CSRF required');
[$code,$body]=http('solicitar.php',null,'employee');$csrf=token($body);
[$code,$body]=http('solicitar.php',['csrf'=>$csrf,'id_epi'=>1,'id_funcionario'=>20,'quantidade'=>-1,'justificativa'=>'Invalid quantity'],'employee');check($code===200 && str_contains($body,'maior que zero'),'Negative quantity validation');
[$code]=http('solicitar.php',['csrf'=>$csrf,'id_epi'=>1,'id_funcionario'=>20,'quantidade'=>2,'justificativa'=>'HTTP scope check'],'employee');check($code===302,'Request submitted');
$r=$m->one("SELECT * FROM tb_solicitacoes WHERE justificativa='HTTP scope check'");check((int)$r['id_funcionario']===2,'Posted user ID cannot spoof ownership');
[$code,$body]=http('minhassolicitacoes.php?id=20',null,'employee');check(!str_contains($body,'Equipamento danificado'),'Another employee requests are not exposed');
[$code,$body]=http('perfil.php',null,'employee');$csrf=token($body);
[$code]=http('perfil.php',['csrf'=>$csrf,'id_funcionario'=>20,'foto_url'=>'img/woman.png'],'employee');check($code===302 && $m->user(2)['foto_url']==='img/woman.png','Profile photo saved to authenticated account');
[$code,$body]=http('homefunc.php',null,'employee');check(str_contains($body,'src="img/woman.png"'),'Header uses database photo');
[$code,$body]=http('cadastro.php');$csrf=token($body);
[$code]=http('cadastro.php',['csrf'=>$csrf,'nome'=>'Teste Integração','email'=>'integration@example.test','cpf'=>'11122233344','cargo'=>'Operador','setor'=>'Teste','foto_url'=>'img/boy.png','senha'=>'teste123']);check($code===302,'Admin creates employee');
$created=$m->one("SELECT * FROM tb_funcionarios WHERE email='integration@example.test'");check(password_verify('teste123',$created['senha']),'New passwords are hashed');
[$code]=http('editarfunc.php?id='.$created['id_funcionario'],['csrf'=>$csrf,'id_funcionario'=>$created['id_funcionario'],'nome'=>'Teste Editado','email'=>'integration@example.test','cpf'=>'11122233344','cargo'=>'Técnico','setor'=>'Teste','foto_url'=>'img/woman.png','senha'=>'']);check($code===302 && $m->user((int)$created['id_funcionario'])['nome_completo']==='Teste Editado','Admin edits employee');
[$code]=http('excluirfunc.php?id='.$created['id_funcionario'],['csrf'=>$csrf,'id_funcionario'=>$created['id_funcionario']]);check($code===302 && !$m->user((int)$created['id_funcionario']),'Admin deletes employee without history');
// Leave the disposable preview in a useful state after exercising edge cases.
$m->run("UPDATE tb_funcionarios SET foto_url='img/boy.png' WHERE id_funcionario=2");
$m->run('DELETE FROM tb_solicitacoes WHERE id_solicitacao=?',[$large]);
[$code,$body]=http('perfil.php',null,'employee');
[$code,$body]=http('perfil.php',['csrf'=>token($body),'foto_url'=>'img/boy.png','senha_atual'=>'incorreta','senha'=>'newpassword123'],'employee');
check($code===200 && str_contains($body,'Senha atual incorreta') && password_verify('123456',$m->user(2)['senha']),'Profile rejects password change without current password');
[$code,$body]=http('perfil.php',null,'employee');
[$code]=http('logout.php',['csrf'=>token($body)],'employee'); check($code===302,'Logout succeeds');
[$code]=http('homefunc.php',null,'employee');check($code===302,'Logout revokes authenticated access');
foreach($jars as $jar) unlink($jar);
echo "\n$checks checks passed. Test database: epi_control_test.\n";
