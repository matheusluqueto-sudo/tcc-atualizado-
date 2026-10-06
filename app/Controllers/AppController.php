<?php
class AppController
{
    private EpiModel $model;
    private array $user;
    private bool $admin;
    private string $page;
    private const ADMIN = ['home','cadastro','funcionarios','editarfunc','excluirfunc','informacoes','adicionarepi','relatorios','estoque','pedidos','atualizarpedido','fazerpedidos','salvarpedido','compras','movimentacoes','excluirmovimentacao','andamento'];
    private const EMPLOYEE = ['homefunc','meuepi','solicitar','minhassolicitacoes'];
    public function handle(string $page): void {
        $this->page = $page;
        try {
            if ($page !== 'login' && empty($_SESSION['id_funcionario'])) redirect('login.php');
            $this->model = new EpiModel(conectar());
            if ($page === 'login') { $this->login(); return; }
            $this->user = $this->model->user((int)$_SESSION['id_funcionario']) ?? [];
            if (!$this->user) { session_destroy(); redirect('login.php'); }
            $this->admin = $this->user['nivel_acesso'] === 'Administrador';
            if ($page === 'index') redirect($this->admin?'home.php':'homefunc.php');
            if ((!$this->admin && in_array($page,self::ADMIN,true)) || ($this->admin && in_array($page,self::EMPLOYEE,true))) {
                http_response_code(403); $this->render('message',['title'=>'Acesso restrito','message'=>'Esta área não está disponível para o seu perfil.']); return;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']) || empty($_SESSION['csrf'])) {
                    http_response_code(403); $this->render('message',['title'=>'Formulário expirado','message'=>'Atualize a página e tente novamente.']); return;
                }
                try { $this->mutate(); }
                catch (DomainException $e) { $error = $e->getMessage(); }
                catch (mysqli_sql_exception $e) { error_log((string)$e); $error = $e->getCode() === 1062 ? 'Este e-mail ou código já está cadastrado.' : 'Não foi possível salvar. Confira os campos e tente novamente.'; }
            }
            $aliases = ['atualizarpedido'=>'pedidos','salvarpedido'=>'fazerpedidos','excluirmovimentacao'=>'movimentacoes'];
            if (isset($aliases[$page])) $page = $aliases[$page];
            $data = ['error'=>$error ?? null,'title'=>$this->titles()[$page] ?? 'EPI Control'];
            $id = (int)($_GET['id'] ?? $_POST['id_funcionario'] ?? 0);
            $ownId = (int)$this->user['id_funcionario'];
            if (in_array($page,['home','homefunc','andamento','pedidos','minhassolicitacoes','relatorios'],true)) {
                $data['requests'] = $this->model->requests($this->admin ? null : $ownId);
                $data['counts'] = array_count_values(array_column($data['requests'],'status_solicitacao'));
            }
            if (in_array($page,['home','homefunc','estoque','solicitar','adicionarepi','fazerpedidos'],true)) $data['epis'] = $this->model->epis();
            if (in_array($page,['home','funcionarios','relatorios'],true)) $data['employees'] = $this->model->employees(trim((string)($_GET['q'] ?? '')));
            if (in_array($page,['home','movimentacoes','relatorios'],true)) $data['history'] = $this->model->history();
            if (in_array($page,['homefunc','meuepi'],true)) {
                $data['equipment'] = $this->model->equipment($ownId);
                $data['history'] = $this->model->history($ownId);
            }
            if ($page === 'compras') $data['purchases'] = $this->model->purchases();
            if (in_array($page,['informacoes','editarfunc','excluirfunc','adicionarepi'],true)) {
                $data['person'] = $this->model->user($id);
                if (!$data['person'] || $data['person']['nivel_acesso'] !== 'Funcionario') {
                    http_response_code(404); $this->render('message',['title'=>'Funcionário não encontrado','message'=>'Selecione um funcionário da lista.']); return;
                }
                $data['equipment'] = $this->model->equipment($id);
                $data['history'] = $this->model->history($id);
            }
            if ($page === 'perfil') $data['person'] = $this->user;
            $views = ['home'=>'dashboard','homefunc'=>'dashboard','andamento'=>'requests','pedidos'=>'requests','minhassolicitacoes'=>'requests','cadastro'=>'person-form','editarfunc'=>'person-form','perfil'=>'person-form','funcionarios'=>'employees','informacoes'=>'equipment','meuepi'=>'equipment','adicionarepi'=>'order-form','solicitar'=>'order-form','fazerpedidos'=>'order-form','estoque'=>'stock','compras'=>'purchases','movimentacoes'=>'history','relatorios'=>'reports','excluirfunc'=>'delete'];
            $this->page = $page;
            $this->render($views[$page] ?? 'message',$data);
        } catch (Throwable $e) {
            error_log((string)$e);
            http_response_code(503);
            $title = 'Sistema indisponível'; $message = 'Confira se o MySQL está iniciado e se o banco tcc foi configurado a partir de database/tcc.sql.';
            require __DIR__ . '/../Views/unavailable.php';
        }
    }
    private function titles(): array { return ['home'=>'Visão geral','homefunc'=>'Meu painel','cadastro'=>'Cadastrar funcionário','funcionarios'=>'Funcionários','editarfunc'=>'Editar funcionário','informacoes'=>'Informações do funcionário','excluirfunc'=>'Excluir funcionário','adicionarepi'=>'Entregar EPI','relatorios'=>'Relatórios','estoque'=>'Estoque de EPIs','pedidos'=>'Solicitações dos funcionários','fazerpedidos'=>'Novo pedido de compra','compras'=>'Compras','movimentacoes'=>'Movimentações','andamento'=>'Em andamento','meuepi'=>'Meu EPI','solicitar'=>'Solicitar EPI','minhassolicitacoes'=>'Minhas solicitações','perfil'=>'Meu perfil']; }
    private function render(string $view, array $data): void {
        extract($data);
        $user = $this->user; $admin = $this->admin; $page = $this->page;
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        require __DIR__ . '/../../templates/header.php';
        require __DIR__ . '/../Views/' . $view . '.php';
        require __DIR__ . '/../../templates/footer.php';
    }
    private function photo(string $current): string {
        $choice=$this->field('foto_url',255,false);
        if (in_array($choice,['','img/boy.png','img/woman.png'],true)) return $choice;
        if ($choice!=='selfie') throw new DomainException('Escolha uma foto válida.');
        $data=$_POST['selfie_data']??'';
        if ($data==='' && preg_match('~^uploads/avatars/[a-f0-9]{32}\.jpg$~',$current)) return $current;
        if (!is_string($data) || strlen($data)>1500000 || !str_starts_with($data,'data:image/jpeg;base64,')) throw new DomainException('Capture ou escolha sua foto antes de salvar.');
        $bytes=base64_decode(substr($data,23),true);
        $info=$bytes!==false?@getimagesizefromstring($bytes):false;
        if (!$info || $info[2]!==IMAGETYPE_JPEG || $info[0]>1024 || $info[1]>1024) throw new DomainException('Foto inválida. Capture novamente.');
        $dir=__DIR__.'/../../uploads/avatars';
        if (!is_dir($dir) && !mkdir($dir,0755,true)) throw new DomainException('Não foi possível salvar a foto.');
        $path='uploads/avatars/'.bin2hex(random_bytes(16)).'.jpg';
        $saved=file_put_contents(__DIR__.'/../../'.$path,$bytes,LOCK_EX);
        if (!$saved) throw new DomainException('Não foi possível salvar a foto.');
        return $path;
    }
    private function positive(string $name): int {
        $v = filter_var($_POST[$name] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>1000000]]);
        if ($v === false) throw new DomainException('Informe uma quantidade ou identificação válida, maior que zero.');
        return $v;
    }
    private function field(string $name, int $max = 100, bool $required = true): string {
        $v = trim(is_string($_POST[$name] ?? null) ? $_POST[$name] : '');
        if (($required && $v === '') || mb_strlen($v) > $max) throw new DomainException('Preencha corretamente o campo ' . $name . ' (máximo ' . $max . ' caracteres).');
        return $v;
    }
    private function done(string $url, string $message): void { $_SESSION['flash']=$message; redirect($url); }
    private function mutate(): void {
        $m = $this->model; $page = $this->page;
        if ($page === 'logout') { $_SESSION=[]; session_destroy(); redirect('login.php'); }
        if ($page === 'solicitar') {
            $epi = $this->positive('id_epi'); $qty = $this->positive('quantidade'); $reason = $this->field('justificativa',2000);
            $m->request((int)$this->user['id_funcionario'],$epi,$qty,$reason);
            $this->done('minhassolicitacoes.php','Solicitação enviada. Acompanhe o status abaixo.');
        }
        if ($page === 'perfil') {
            $photo = $this->photo($this->user['foto_url'] ?? '');
            $password = (string)($_POST['senha'] ?? '');
            if ($password !== '') {
                $current=(string)($_POST['senha_atual'] ?? '');
                if (!password_verify($current,$this->user['senha']) && !(password_get_info($this->user['senha'])['algo']===null && hash_equals($this->user['senha'],$current))) throw new DomainException('Senha atual incorreta.');
                if (strlen($password)<6 || strlen($password)>72) throw new DomainException('A nova senha deve ter entre 6 e 72 caracteres.');
                $m->updateProfile((int)$this->user['id_funcionario'],$photo,password_hash($password,PASSWORD_DEFAULT));
            } else $m->updateProfile((int)$this->user['id_funcionario'],$photo,null);
            $this->done('perfil.php','Perfil atualizado.');
        }
        if (!$this->admin) throw new DomainException('Operação não permitida.');
        if (in_array($page,['cadastro','editarfunc'],true)) {
            $name=$this->field('nome'); $email=$this->field('email'); $cpf=preg_replace('/\D/','',$this->field('cpf',20));
            $cargo=$this->field('cargo'); $setor=$this->field('setor'); $photo=$this->photo($page==='editarfunc'?($m->user($this->positive('id_funcionario'))['foto_url']??''):''); $password=(string)($_POST['senha'] ?? '');
            if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($cpf)!==11) throw new DomainException('Informe um e-mail válido e um CPF com 11 dígitos.');
            if (($page === 'cadastro' || $password !== '') && (strlen($password)<6 || strlen($password)>72)) throw new DomainException('A senha deve ter entre 6 e 72 caracteres.');
            $m->saveEmployee($page==='cadastro'?null:$this->positive('id_funcionario'),[$name,$email,$cpf,$cargo,$setor,$photo],$password);
            $this->done('funcionarios.php','Funcionário salvo com sucesso.');
        }
        if ($page === 'excluirmovimentacao') { $m->deleteMovement($this->positive('id_movimentacao'),$this->field('tipo',10)); $this->done('movimentacoes.php','Registro de movimentação excluído. O saldo de estoque foi mantido.'); }
        if ($page === 'excluirfunc') { $m->deleteEmployee($this->positive('id_funcionario')); $this->done('funcionarios.php','Funcionário excluído.'); }
        if ($page === 'adicionarepi') { $id=$this->positive('id_funcionario'); $m->assign($id,$this->positive('id_epi'),$this->positive('quantidade'),$this->field('motivo',2000)); $this->done('informacoes.php?id='.$id,'Entrega registrada e estoque atualizado.'); }
        if (in_array($page,['pedidos','atualizarpedido','andamento'],true)) { $m->changeRequest($this->positive('id_solicitacao'),$this->field('status',30)); $this->done($page==='andamento'?'andamento.php':'pedidos.php','Solicitação atualizada.'); }
        if (in_array($page,['fazerpedidos','salvarpedido'],true)) {
            $epi=$this->positive('id_epi');
            $m->purchase($epi,$this->positive('quantidade'),$this->field('observacao',255,false));
            $this->done('compras.php','Pedido de compra registrado.');
        }
        if ($page === 'compras') { $m->changePurchase($this->positive('id_pedido'),$this->field('status',30)); $this->done('compras.php','Compra atualizada. Compras recebidas são adicionadas ao estoque.'); }
        throw new DomainException('Esta página não permite alterações.');
    }
    private function login(): void {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_string($_POST['csrf'] ?? null) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'],$_POST['csrf'])) $error='Atualize a página e tente novamente.';
            else {
                $u=$this->model->findByEmail(trim((string)($_POST['usuario'] ?? '')));
                $password=(string)($_POST['senha'] ?? '');
                if ($u && (password_verify($password,$u['senha']) || (password_get_info($u['senha'])['algo'] === null && hash_equals($u['senha'],$password)))) {
                    if (password_needs_rehash($u['senha'],PASSWORD_DEFAULT)) $this->model->updatePassword((int)$u['id_funcionario'],password_hash($password,PASSWORD_DEFAULT));
                    session_regenerate_id(true); $_SESSION=['id_funcionario'=>(int)$u['id_funcionario']];
                    redirect($u['nivel_acesso']==='Administrador'?'home.php':'homefunc.php');
                } else $error='E-mail ou senha inválidos.';
            }
        }
        require __DIR__ . '/../Views/login.php';
    }
}
