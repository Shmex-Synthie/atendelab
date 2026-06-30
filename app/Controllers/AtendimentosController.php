<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void
    {
        $sql = 'SELECT a.id,
                       p.nome AS pessoa_nome,
                       t.nome AS tipo_nome,
                       u.nome AS usuario_nome,
                       a.descricao,
                       a.status,
                       a.data_atendimento,
                       a.horario_atendimento,
                       a.observacao_final,
                       a.criado_em
                FROM atendimentos a
                JOIN pessoas p ON p.id = a.pessoa_id
                JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                JOIN usuarios u ON u.id = a.usuario_id
                ORDER BY a.id DESC';

        $this->json($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscarPorId(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT a.id,
                    a.pessoa_id,
                    a.tipo_atendimento_id,
                    a.usuario_id,
                    p.nome AS pessoa_nome,
                    t.nome AS tipo_nome,
                    u.nome AS usuario_nome,
                    a.descricao,
                    a.status,
                    a.data_atendimento,
                    a.horario_atendimento,
                    a.observacao_final,
                    a.criado_em
             FROM atendimentos a
             JOIN pessoas p ON p.id = a.pessoa_id
             JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
             JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.id = :id'
        );
        $stmt->execute(['id' => $id]);

        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->json(['erro' => 'Atendimento não encontrado.'], 404);
            return;
        }

        $this->json($atendimento);
    }

    // Alias para a action "visualizar" usada na tela.
    public function visualizar(): void
    {
        $this->buscarPorId();
    }

    public function criar(): void
    {
        $pessoaId = filter_var($_POST['pessoa_id'] ?? null, FILTER_VALIDATE_INT);
        $tipoId = filter_var($_POST['tipo_atendimento_id'] ?? null, FILTER_VALIDATE_INT);
        $descricao = trim($_POST['descricao'] ?? '');
        $data = $_POST['data_atendimento'] ?? '';
        $horario = $_POST['horario_atendimento'] ?? '';
        $status = $_POST['status'] ?? 'aberto';

        // O responsavel pelo atendimento e sempre o usuario logado (sessao),
        // nunca um campo livre vindo do formulario.
        $usuarioId = $_SESSION['usuario']['id'] ?? null;

        if (!$usuarioId) {
            $this->json(['erro' => 'Usuário não autenticado.'], 401);
            return;
        }

        if (!$pessoaId || !$tipoId || $descricao === '' || $data === '' || $horario === '') {
            $this->json(['erro' => 'Preencha os campos obrigatórios.'], 422);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento'], true)) {
            $this->json(['erro' => 'Status inicial inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO atendimentos (pessoa_id, tipo_atendimento_id, usuario_id, descricao, status, data_atendimento, horario_atendimento)
             VALUES (:pessoa_id, :tipo_id, :usuario_id, :descricao, :status, :data_atendimento, :horario_atendimento)'
        );

        $stmt->execute([
            'pessoa_id' => $pessoaId,
            'tipo_id' => $tipoId,
            'usuario_id' => $usuarioId,
            'descricao' => $descricao,
            'status' => $status,
            'data_atendimento' => $data,
            'horario_atendimento' => $horario,
        ]);

        $this->json(['mensagem' => 'Atendimento registrado com sucesso.'], 201);
    }

    public function atualizar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $pessoaId = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipoId = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $dataAtendimento = trim($_POST['data_atendimento'] ?? '');
        $horario = trim($_POST['horario_atendimento'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $observacao = trim($_POST['observacao_final'] ?? '');
        $status = $_POST['status'] ?? 'aberto';

        $usuarioId = $_SESSION['usuario']['id'] ?? null;

        if (!$id || !$pessoaId || !$tipoId || !$usuarioId || $dataAtendimento === '' || $horario === '') {
            $this->json(['erro' => 'ID, pessoa, tipo, data e hora são obrigatórios.'], 422);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento', 'concluido', 'cancelado'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE atendimentos
             SET pessoa_id = :pessoa_id,
                 tipo_atendimento_id = :tipo_atendimento_id,
                 usuario_id = :usuario_id,
                 data_atendimento = :data_atendimento,
                 horario_atendimento = :horario_atendimento,
                 descricao = :descricao,
                 observacao_final = :observacao_final,
                 status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            'pessoa_id' => $pessoaId,
            'tipo_atendimento_id' => $tipoId,
            'usuario_id' => $usuarioId,
            'data_atendimento' => $dataAtendimento,
            'horario_atendimento' => $horario,
            'descricao' => $descricao,
            'observacao_final' => $observacao,
            'status' => $status,
            'id' => $id,
        ]);

        $this->json(['mensagem' => 'Atendimento atualizado com sucesso.']);
    }

    public function alterarStatus(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $observacao = trim($_POST['observacao_final'] ?? '');

        if (!$id || !in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'ID ou status inválido.'], 422);
            return;
        }

        if ($status === 'concluido' && $observacao === '') {
            $this->json(['erro' => 'Informe a observação final para concluir.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE atendimentos SET status = :status, observacao_final = :observacao_final WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'observacao_final' => $observacao !== '' ? $observacao : null,
        ]);

        $this->json(['mensagem' => 'Status atualizado com sucesso.']);
    }

    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql = 'DELETE FROM atendimentos WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['mensagem' => 'Atendimento excluído com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir atendimento.']);
        }
    }
}
