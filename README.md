
# EPI Control — Sistema de Gestão de EPIs

Projeto de Conclusão de Curso (TCC), desenvolvido para o Curso Técnico em Desenvolvimento de Sistemas do SENAI Ourinhos (2026).

O **EPI Control** é uma aplicação web para organizar funcionários, Equipamentos de Proteção Individual (EPIs), solicitações, entregas, compras, estoque e movimentações.

## Tecnologias e requisitos

- **Backend:** PHP 8.1+ com `mysqli` e organização MVC.
- **Frontend:** HTML, CSS e JavaScript.
- **Banco de dados:** MySQL / MariaDB.
- **Ambiente local:** XAMPP com Apache e MySQL/MariaDB.
- **Testes HTTP:** extensões PHP `mysqli`, `mbstring` e `curl` e servidor PHP para testes.

## Organização dos arquivos

- `app/Models/EpiModel.php`: consultas ao banco, movimentações e regras de negócio.
- `app/Controllers/AppController.php`: autenticação, autorização, validações e ações.
- `app/Views/`: telas exibidas ao usuário.
- `app/bootstrap.php` e `app/dispatch.php`: inicialização e encaminhamento das rotas.
- `templates/`: cabeçalho e rodapé comuns.
- `css/app.css` e `js/app.js`: estilos e interações principais.
- `config/conexao.php`: conexão com o MySQL, sem credenciais de produção.
- `database/tcc.sql`: estrutura e dados fictícios para demonstração e testes.
- `tests/integration.php`: testes de integração.
- `docs/`: imagens de telas utilizadas na documentação.

## Instalação local com XAMPP

1. Coloque o projeto na pasta `htdocs/epi-control` do XAMPP.
2. Ligue **Apache** e **MySQL** no painel do XAMPP.
3. No phpMyAdmin, crie um banco vazio chamado `tcc`, com codificação `utf8mb4`.
4. Importe o arquivo `database/tcc.sql` nesse banco vazio.
5. Verifique `config/conexao.php`. Os padrões locais são `127.0.0.1`, `root`, senha vazia, banco `tcc` e porta `3306`.
6. Abra `http://localhost/epi-control/` no navegador. Se a pasta tiver outro nome, adapte o endereço.
7. Para acessar o sistema, use as contas de demonstração criadas pelo SQL exclusivamente em ambiente local.

**Importante:** o SQL deste repositório é adequado para instalar um banco novo e vazio. Não deve ser importado sobre um banco existente com dados reais.

## Configuração segura para hospedagem (InfinityFree)

O arquivo `config/conexao.php` não contém sua senha da hospedagem.

Para configurar o ambiente hospedado, crie somente no servidor o arquivo `config/conexao.local.php` com este conteúdo, substituindo pelos dados que aparecem no painel do seu provedor:

```php
<?php
return [
    'host' => 'HOST_DO_BANCO',
    'user' => 'USUARIO_DO_BANCO',
    'password' => 'SENHA_DO_BANCO',
    'database' => 'NOME_DO_BANCO',
    'port' => 3306,
];
```

**Nunca publique `conexao.local.php` no GitHub.**

Adicione a seguinte linha ao arquivo `.gitignore` na raiz do repositório:

```gitignore
/config/conexao.local.php
```

Se o site já está funcionando na hospedagem, não troque o arquivo de conexão do site antes de preparar e testar essa configuração privada.

Como alternativa, o código reconhece as variáveis de ambiente `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` e `DB_PORT` quando disponíveis. Elas têm prioridade sobre o arquivo local.

## Funcionalidades

### Administrador

- Autenticação de usuários.
- Cadastro e gerenciamento de funcionários.
- Edição e consulta de informações cadastrais.
- Exclusão condicionada às regras de negócio.
- Inativação e reativação de funcionários.
- Consulta e entrega de EPIs.
- Gerenciamento de estoque.
- Acompanhamento de solicitações.
- Pedidos de compra de equipamentos.
- Registro de entradas e saídas.
- Acompanhamento de processos.
- Consulta de relatórios administrativos.
- Gerenciamento de perfil.

### Funcionário

- Autenticação e acesso ao painel pessoal.
- Solicitação de EPIs.
- Acompanhamento das solicitações.
- Consulta dos equipamentos recebidos.
- Mapa educativo interativo de proteção corporal.
- Consulta ao histórico de equipamentos.
- Visualização de informações do perfil.

## Regras de negócio relevantes

- As permissões são verificadas no servidor, de acordo com o perfil autenticado.
- Funcionários com entregas ou solicitações registradas não podem ser excluídos; a inativação preserva o histórico e impede o acesso.
- Uma entrega efetiva de EPI reduz o estoque e registra uma saída.
- Uma compra marcada como **Comprado** confirma o recebimento do pedido e registra uma entrada de estoque; a mesma compra não deve gerar entradas duplicadas.
- Os status de solicitações incluem `Pendente`, `Aprovada`, `Em andamento`, `Entregue` e `Recusada`, conforme a etapa do processo.
- O mapa de EPIs tem finalidade informativa e não substitui a avaliação de segurança do trabalho.

### Melhorias futuras

Entre as funcionalidades previstas para possíveis atualizações estão:

- Leitura de equipamentos por QR Code.
- Assinatura eletrônica para confirmação de entregas.
- Alertas automáticos de vencimento dos EPIs.
- Ampliação das ferramentas de análise e relatórios.

Essas funcionalidades não foram identificadas como recursos ativos na versão analisada.

## Banco de dados

O banco de dados do EPI Control utiliza MySQL e possui oito tabelas principais e auxiliares:

- `tb_funcionarios`
- `tb_epis`
- `tb_solicitacoes`
- `tb_pedidos`
- `tb_entrada`
- `tb_saida`
- `if_solicitacao`
- `recuperacao_senha`

O arquivo `database/tcc.sql` contém a estrutura do banco de dados, incluindo relacionamentos, índices e a coluna `status_funcionario`.

Os registros incluídos no SQL são fictícios e mínimos, destinados a demonstrações locais e testes. Eles não comprovam a exigência acadêmica de 20 registros por tabela.

Para verificar os registros utilizados no ambiente hospedado, deve-se consultar o banco de dados do servidor.

**Atenção:** nunca publique CPFs, e-mails ou senhas reais de funcionários em um repositório público.

### Banco existente no InfinityFree

Não execute o arquivo SQL completo sobre o banco de produção.

Antes de qualquer alteração estrutural, faça uma cópia de segurança.

Para verificar a existência da coluna `status_funcionario`, utilize:

```sql
SHOW COLUMNS FROM tb_funcionarios LIKE 'status_funcionario';
```

Se a coluna ainda não existir, poderá ser utilizada a seguinte instrução:

```sql
ALTER TABLE tb_funcionarios
ADD COLUMN status_funcionario ENUM('Ativo','Inativo')
NOT NULL DEFAULT 'Ativo'
AFTER nivel_acesso;
```

Execute esse comando somente se a coluna não existir.

## Testes de integração

O projeto possui um arquivo de testes de integração localizado em:

`tests/integration.php`

Os testes contemplam verificações relacionadas às regras de negócio, ao banco de dados e ao controle de acesso.

**Importante:** o arquivo de testes apaga e recria o banco `epi_control_test`. Portanto, deve ser utilizado somente em um ambiente de testes, nunca na hospedagem de produção.

Os testes pressupõem um servidor HTTP local e um banco de dados separado.

### Exemplo de configuração

No PowerShell, ajuste as variáveis de ambiente para uma instância MySQL de testes:

```powershell
$env:DB_HOST = '127.0.0.1'
$env:DB_USER = 'root'
$env:DB_PASSWORD = ''
$env:DB_PORT = '3318'
$env:DB_NAME = 'epi_control_test'
php -S 127.0.0.1:8098 -t .
```

Em outro terminal, com as mesmas variáveis de ambiente, execute:

```powershell
php tests/integration.php
```

A porta `3318` é apenas um exemplo e deve ser substituída pela porta configurada para o banco de testes.

Os resultados devem ser registrados após a execução no ambiente utilizado.

## Cuidados com dados e segurança

- Nunca envie ao GitHub senhas da hospedagem ou credenciais reais.
- Não publique dados pessoais de funcionários.
- Não utilize contas e senhas de demonstração no sistema hospedado.
- Se credenciais já foram publicadas anteriormente, altere-as.
- Faça backups do banco de dados antes de realizar modificações.
- Utilize configurações de conexão específicas para cada ambiente.
- Mantenha o código do repositório atualizado em relação ao sistema hospedado.

## Objetivo do projeto

O EPI Control foi desenvolvido com o objetivo de demonstrar como a tecnologia pode contribuir para a organização dos processos relacionados à gestão de Equipamentos de Proteção Individual.

A plataforma procura centralizar informações, facilitar o controle de estoque, melhorar o acompanhamento das solicitações e permitir a consulta do histórico dos equipamentos entregues aos funcionários.

O projeto representa a aplicação prática de conhecimentos adquiridos durante o Curso Técnico em Desenvolvimento de Sistemas do SENAI Ourinhos.

## Equipe de desenvolvimento

- Amanda Noemi Ferroni
- Giovana Maximiano Della Tonia
- Matheus dos Santos Luqueto

**Instituição:** SENAI Ourinhos

**Curso:** Técnico em Desenvolvimento de Sistemas

**Ano:** 2026
