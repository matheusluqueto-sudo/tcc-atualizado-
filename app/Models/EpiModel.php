<?php
class EpiModel
{
    public function __construct(public mysqli $db) {}
    public function run(string $sql, array $params = []): mysqli_stmt {
        $stmt = $this->db->prepare($sql);
        if ($params) {
            $types = implode('', array_map(fn($p) => is_int($p) ? 'i' : 's', $params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt;
    }
    public function all(string $sql, array $params = []): array { return $this->run($sql, $params)->get_result()->fetch_all(MYSQLI_ASSOC); }
    public function one(string $sql, array $params = []): ?array { return $this->all($sql, $params)[0] ?? null; }
    public function user(int $id): ?array { return $this->one('SELECT * FROM tb_funcionarios WHERE id_funcionario=?', [$id]); }
    public function findByEmail(string $email): ?array { return $this->one('SELECT * FROM tb_funcionarios WHERE email=?',[$email]); }
    public function updatePassword(int $id, string $hash): void { $this->run('UPDATE tb_funcionarios SET senha=? WHERE id_funcionario=?',[$hash,$id]); }
    private function requireEpi(int $id): void { if (!$this->one('SELECT id_epi FROM tb_epis WHERE id_epi=?',[$id])) throw new DomainException('Selecione um EPI válido.'); }
    public function request(int $userId, int $epiId, int $quantity, string $reason): void {
        $this->requireEpi($epiId);
        if ($quantity < 1) throw new DomainException('Informe uma quantidade positiva.');
        $this->run('INSERT INTO tb_solicitacoes (id_funcionario,id_epi,quantidade,justificativa) VALUES (?,?,?,?)',[$userId,$epiId,$quantity,$reason]);
    }
    public function purchase(int $epiId, int $quantity, string $note): void {
        $this->requireEpi($epiId);
        if ($quantity < 1) throw new DomainException('Informe uma quantidade positiva.');
        $this->run('INSERT INTO tb_pedidos (id_epi,quantidade,observacao) VALUES (?,?,?)',[$epiId,$quantity,$note]);
    }
    public function updateProfile(int $id, string $photo, ?string $hash): void {
        if ($hash !== null) $this->run('UPDATE tb_funcionarios SET foto_url=?,senha=? WHERE id_funcionario=?',[$photo,$hash,$id]);
        else $this->run('UPDATE tb_funcionarios SET foto_url=? WHERE id_funcionario=?',[$photo,$id]);
    }
    public function saveEmployee(?int $id, array $values, string $password): void {
        if ($id === null) {
            $this->run("INSERT INTO tb_funcionarios (nome_completo,email,cpf,cargo,setor,foto_url,senha,nivel_acesso) VALUES (?,?,?,?,?,?,?,'Funcionario')",[...$values,password_hash($password,PASSWORD_DEFAULT)]);
        } else {
            $person=$this->user($id);
            if (!$person || $person['nivel_acesso']!=='Funcionario') throw new DomainException('Funcionário inválido.');
            $this->run('UPDATE tb_funcionarios SET nome_completo=?,email=?,cpf=?,cargo=?,setor=?,foto_url=?,senha=? WHERE id_funcionario=?',[...$values,$password!==''?password_hash($password,PASSWORD_DEFAULT):$person['senha'],$id]);
        }
    }
    public function employees(string $search = ''): array {
        return $this->all("SELECT id_funcionario,nome_completo,email,cpf,cargo,setor,foto_url FROM tb_funcionarios WHERE nivel_acesso='Funcionario' AND (nome_completo LIKE ? OR email LIKE ? OR setor LIKE ? OR cpf LIKE ?) ORDER BY nome_completo", array_fill(0, 4, '%' . $search . '%'));
    }
    public function epis(): array { return $this->all('SELECT * FROM tb_epis ORDER BY nome_epi'); }
    public function requests(?int $userId = null): array {
        return $this->all('SELECT s.*,e.nome_epi,f.nome_completo FROM tb_solicitacoes s JOIN tb_epis e ON e.id_epi=s.id_epi JOIN tb_funcionarios f ON f.id_funcionario=s.id_funcionario' . ($userId !== null ? ' WHERE s.id_funcionario=?' : '') . ' ORDER BY s.data_solicitacao DESC,s.id_solicitacao DESC', $userId !== null ? [$userId] : []);
    }
    public function purchases(): array { return $this->all('SELECT p.*,e.nome_epi FROM tb_pedidos p JOIN tb_epis e ON e.id_epi=p.id_epi ORDER BY p.data_pedido DESC,p.id_pedido DESC'); }
    public function equipment(int $userId): array {
        return $this->all('SELECT e.*,SUM(s.quantidade) AS quantidade,MAX(s.data) AS ultima_entrega FROM tb_saida s JOIN tb_epis e ON e.id_epi=s.id_epi WHERE s.id_funcionario=? GROUP BY e.id_epi ORDER BY e.nome_epi', [$userId]);
    }
    public function history(?int $userId = null): array {
        return $this->all("SELECT s.id_saida AS id,s.id_funcionario,'Saída' AS tipo,e.nome_epi,f.nome_completo,s.quantidade,s.data,s.motivo FROM tb_saida s JOIN tb_epis e ON e.id_epi=s.id_epi JOIN tb_funcionarios f ON f.id_funcionario=s.id_funcionario" . ($userId !== null ? ' WHERE s.id_funcionario=?' : " UNION ALL SELECT n.id_entrada,NULL,'Entrada',e.nome_epi,'Almoxarifado',n.quantidade,n.data,n.motivo FROM tb_entrada n JOIN tb_epis e ON e.id_epi=n.id_epi") . ' ORDER BY data DESC,id DESC', $userId !== null ? [$userId] : []);
    }
    public function transaction(callable $work): void {
        $this->db->begin_transaction();
        try { $work(); $this->db->commit(); } catch (Throwable $e) { $this->db->rollback(); throw $e; }
    }
    private function deliver(int $userId, int $epiId, int $quantity, string $reason, bool $requested = false): void {
        if ($quantity < 1) throw new DomainException('Informe uma quantidade positiva.');
        $person = $this->one("SELECT id_funcionario FROM tb_funcionarios WHERE id_funcionario=? AND nivel_acesso='Funcionario' FOR UPDATE", [$userId]);
        if (!$person) throw new DomainException('Funcionário não encontrado.');
        $stmt = $this->run('UPDATE tb_epis SET quantidade_estoque=quantidade_estoque-? WHERE id_epi=? AND quantidade_estoque>=?', [$quantity,$epiId,$quantity]);
        if ($stmt->affected_rows !== 1) throw new DomainException('Estoque insuficiente para esta entrega.');
        $this->run('INSERT INTO tb_saida (id_funcionario,id_epi,solicitacao,quantidade,data,motivo) VALUES (?,?,?,?,CURDATE(),?)', [$userId,$epiId,(int)$requested,$quantity,$reason]);
    }
    public function assign(int $userId, int $epiId, int $quantity, string $reason): void {
        $this->transaction(fn() => $this->deliver($userId,$epiId,$quantity,$reason));
    }
    public function changeRequest(int $id, string $next): void {
        $this->transaction(function() use ($id,$next) {
            $r = $this->one('SELECT * FROM tb_solicitacoes WHERE id_solicitacao=? FOR UPDATE', [$id]);
            $allowed = ['Pendente'=>['Aprovada','Recusada'], 'Aprovada'=>['Em andamento','Entregue','Recusada'], 'Em andamento'=>['Entregue','Recusada']];
            if (!$r || !in_array($next, $allowed[$r['status_solicitacao']] ?? [], true)) throw new DomainException('Transição inválida ou solicitação já finalizada. Atualize a página.');
            if ($next === 'Entregue') $this->deliver((int)$r['id_funcionario'], (int)$r['id_epi'], (int)$r['quantidade'], 'Entrega da solicitação #' . $id, true);
            $this->run('UPDATE tb_solicitacoes SET status_solicitacao=? WHERE id_solicitacao=?', [$next,$id]);
        });
    }
    public function changePurchase(int $id, string $next): void {
        $this->transaction(function() use ($id,$next) {
            $p = $this->one('SELECT * FROM tb_pedidos WHERE id_pedido=? FOR UPDATE', [$id]);
            if (!$p || $p['status_pedido'] !== 'Pendente' || !in_array($next,['Comprado','Não comprado'],true)) throw new DomainException('Compra já finalizada ou status inválido.');
            if ($next === 'Comprado') {
                $this->run('UPDATE tb_epis SET quantidade_estoque=quantidade_estoque+? WHERE id_epi=?', [(int)$p['quantidade'],(int)$p['id_epi']]);
                $this->run('INSERT INTO tb_entrada (id_epi,quantidade,data,motivo) VALUES (?,?,CURDATE(),?)', [(int)$p['id_epi'],(int)$p['quantidade'],'Recebimento da compra #' . $id]);
            }
            $this->run('UPDATE tb_pedidos SET status_pedido=? WHERE id_pedido=?', [$next,$id]);
        });
    }
    public function deleteMovement(int $id, string $type): void {
        $table=match($type) { 'Entrada'=>'tb_entrada', 'Saída'=>'tb_saida', default=>throw new DomainException('Tipo de movimentação inválido.') };
        $key=$type==='Entrada'?'id_entrada':'id_saida';
        if ($this->run("DELETE FROM $table WHERE $key=?",[$id])->affected_rows!==1) throw new DomainException('Movimentação não encontrada ou já excluída.');
    }
    public function deleteEmployee(int $id): void {
        $this->transaction(function() use ($id) {
            $person = $this->one("SELECT id_funcionario FROM tb_funcionarios WHERE id_funcionario=? AND nivel_acesso='Funcionario' FOR UPDATE", [$id]);
            if (!$person) throw new DomainException('Funcionário não encontrado.');
            if ($this->one('SELECT id_saida FROM tb_saida WHERE id_funcionario=? LIMIT 1',[$id]) || $this->one('SELECT id_solicitacao FROM tb_solicitacoes WHERE id_funcionario=? LIMIT 1',[$id])) throw new DomainException('Este funcionário possui histórico de EPIs ou solicitações e não pode ser excluído.');
            $this->run('DELETE FROM tb_funcionarios WHERE id_funcionario=?',[$id]);
        });
    }
}
