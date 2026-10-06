# EPI Control

Aplicação PHP 8.1+ / MariaDB 10.4+ para gestão de equipamentos de proteção individual. Extensões: mysqli e mbstring; curl para os testes HTTP.

## Executar no XAMPP

1. Inicie Apache e MySQL no painel do XAMPP.
2. Para uma instalação nova, crie o banco `tcc` vazio no phpMyAdmin e importe **`database/tcc.sql`**. Esse arquivo já contém toda a estrutura e os dados, incluindo as quantidades das solicitações. Se o banco já foi configurado com esse SQL, não precisa importar novamente.
3. Abra `http://localhost/TCC%20-%20Little/`. O início encaminha para o painel do perfil autenticado.

### Arquivo único do banco

`database/tcc.sql` é a única fonte SQL do projeto. Qualquer alteração futura de estrutura ou dos dados distribuídos deve ser feita diretamente nesse arquivo, sem criar scripts SQL paralelos. O sistema utiliza o banco MySQL por meio de `config/conexao.php`; não importa o arquivo automaticamente a cada acesso. O SQL é um dump completo para importação em banco vazio, não um atualizador automático de bancos existentes.

`config/conexao.php` usa por padrão `127.0.0.1`, usuário `root`, senha vazia, banco `tcc`, portas 3306 e 3307. É possível configurar `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` e `DB_PORT` no ambiente. A conta administrativa de demonstração presente no SQL original é `admin@epicontrol.com` / `123456`; altere-a no perfil ao usar o sistema.

## Organização MVC

- `app/Models/EpiModel.php`: consultas preparadas, persistência, transações de entrega/compra e regras de estoque.
- `app/Controllers/AppController.php`: autenticação, autorização por papel, validação, ações POST e escolha das views.
- `app/Views/`: conteúdo das telas e componentes de tabelas/gráficos.
- `templates/header.php` e `templates/footer.php`: layout compartilhado. A navegação horizontal muda conforme o papel do usuário.
- `app/bootstrap.php`: sessão, carregamento e helpers de apresentação; `app/dispatch.php`: entrada do controlador.
- `database/tcc.sql`: estrutura e dados do banco, compartilhados pela instalação e pelos testes. Consultas da aplicação permanecem no Model, sem SQL nos templates ou nas views.
- Arquivos PHP na raiz: entradas compatíveis com os endereços antigos. `css/app.css` e `js/app.js`: interface responsiva e interações atuais. Os antigos CSS/JS foram preservados, mas não são carregados pelo novo layout.

## Perfis e telas

**Funcionário:** início com resumo pessoal, avisos e solicitações recentes; Meu EPI com equipamentos, quantidade, CA, descrição, mapa interativo de proteção e histórico; Solicitar EPI; Minhas solicitações; Perfil.

**Administrador:** dashboard com indicadores e gráfico; cadastro, busca, edição e exclusão de funcionários; consulta e entrega de EPIs por funcionário; relatórios filtrados por funcionário e impressão/PDF pelo navegador; estoque; gestão de solicitações; pedidos de reposição; compras; entradas/saídas; acompanhamento em andamento; Perfil.

As permissões são conferidas no servidor em todas as rotas, inclusive ações antigas. O funcionário nunca escolhe o titular da solicitação: o ID vem da sessão. O administrador não utiliza as páginas pessoais de EPI do funcionário. Ambos acessam o próprio perfil.

## Regras do sistema

- Solicitação: **Pendente → Aprovada → Em andamento → Entregue**. Uma aprovada também pode ser entregue diretamente. A recusa é permitida enquanto não finalizada. Aprovação não reserva estoque; apenas a entrega registra saída e baixa o saldo. Sem saldo, toda a operação é revertida.
- Compra: **Pendente → Comprado / Não comprado**. “Comprado” confirma também o recebimento físico, gerando uma entrada de estoque. Reenvios não duplicam entradas/saídas. Uma compra finalizada não é editável.
- Entrega direta: registrada em Funcionários → Ver EPIs → Entregar EPI, com quantidade e motivo.
- Exclusão: exige formulário POST com confirmação. Funcionários com entregas ou solicitações não podem ser excluídos, preservando o histórico.
- Foto: `tb_funcionarios.foto_url` armazena `img/boy.png` ou `img/woman.png`, escolhidos explicitamente no cadastro, edição ou perfil. Não se infere gênero pelo nome. Registros sem foto usam avatar neutro. Também é possível capturar uma selfie ou escolher uma foto; a imagem é reduzida para 480 × 480 e salva em `uploads/avatars`. A câmera exige localhost ou HTTPS e permissão do navegador.
- “Meu EPI” mostra quantidades acumuladas de entregas, não saldo líquido após devoluções. O projeto não tem fluxo de devolução/descarte. O mapa é educativo, com regiões para olhos, ouvidos, rosto, mãos, cabeça, tronco e pés; itens não reconhecidos orientam consultar as instruções do equipamento.
- Senhas novas usam `password_hash`. Senhas em texto do banco legado são convertidas no próximo login válido. A alteração pelo próprio usuário exige a senha atual. Recuperação é feita pelo administrador na edição do funcionário; as páginas antigas de recuperação redirecionam ao login porque não havia envio de e-mail configurado.
- O catálogo de tipos de EPI é o já cadastrado no banco. O estoque é atualizado pelas compras recebidas e entregas.

## Verificação automatizada

`tests/integration.php` **recria exclusivamente o banco descartável `epi_control_test`**. Não execute com esse nome reservado para dados reais. O teste importa somente `database/tcc.sql` e verifica os dados importados, os fluxos e as permissões por HTTP.

Em um MySQL de teste, inicie um servidor PHP separado com `DB_NAME=epi_control_test` e `DB_PORT` da instância de teste; por exemplo, no PowerShell:

```powershell
$env:DB_PORT = '3318'
$env:DB_NAME = 'epi_control_test'
php -S 127.0.0.1:8098 -t .
```

Em outro terminal com as mesmas variáveis, execute `php tests/integration.php`. Configure `TEST_URL` se usar outro endereço HTTP. A pasta de sessões do PHP precisa ser gravável. Os testes não necessitam do Apache; os bloqueios `.htaccess` de diretórios internos aplicam-se ao Apache.

Validação realizada: 75 verificações de integração aprovadas, sintaxe de todos os PHP válida e interface conferida em desktop e celular. Prévias com dados do banco descartável em `docs/`.

Na página Movimentações, o administrador pode excluir um registro com confirmação. Essa ação remove apenas o histórico, sem estornar o saldo de estoque; saídas excluídas deixam de compor os equipamentos recebidos.
