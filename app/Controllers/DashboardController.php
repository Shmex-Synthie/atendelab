<?php

class DashboardController
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

    public function resumo(): void
    {
        $totalPessoas = (int) $this->pdo->query('SELECT COUNT(*) FROM pessoas')->fetchColumn();
        $pessoasAtivas = (int) $this->pdo->query("SELECT COUNT(*) FROM pessoas WHERE status = 'ativo'")->fetchColumn();
        $totalTipos = (int) $this->pdo->query('SELECT COUNT(*) FROM tipos_atendimentos')->fetchColumn();
        $totalAtendimentos = (int) $this->pdo->query('SELECT COUNT(*) FROM atendimentos')->fetchColumn();

        // Contagem de atendimentos por status (chave => total)
        $porStatus = $this->pdo->query(
            'SELECT status, COUNT(*) AS total FROM atendimentos GROUP BY status'
        )->fetchAll(PDO::FETCH_KEY_PAIR);

        $abertos = (int) ($porStatus['aberto'] ?? 0);
        $emAndamento = (int) ($porStatus['em_andamento'] ?? 0);
        $concluidos = (int) ($porStatus['concluido'] ?? 0);
        $cancelados = (int) ($porStatus['cancelado'] ?? 0);

        // Top pessoas por numero de atendimentos
        $topPessoas = $this->pdo->query(
            'SELECT p.nome,
                    COUNT(a.id) AS total,
                    SUM(a.status IN (\'aberto\', \'em_andamento\')) AS em_aberto
             FROM pessoas p
             JOIN atendimentos a ON a.pessoa_id = p.id
             GROUP BY p.id, p.nome
             ORDER BY total DESC, p.nome ASC
             LIMIT 5'
        )->fetchAll(PDO::FETCH_ASSOC);

        $recentes = $this->pdo->query(
            'SELECT a.id,
                    p.nome AS pessoa_nome,
                    t.nome AS tipo_nome,
                    u.nome AS usuario_nome,
                    a.status,
                    a.data_atendimento
             FROM atendimentos a
             JOIN pessoas p ON p.id = a.pessoa_id
             JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
             JOIN usuarios u ON u.id = a.usuario_id
             ORDER BY a.id DESC
             LIMIT 5'
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->json([
            'indicadores' => [
                'total_pessoas' => $totalPessoas,
                'pessoas_ativas' => $pessoasAtivas,
                'total_tipos' => $totalTipos,
                'total_atendimentos' => $totalAtendimentos,
                'abertos' => $abertos,
                'em_andamento' => $emAndamento,
                'concluidos' => $concluidos,
                'cancelados' => $cancelados,
            ],
            'top_pessoas' => $topPessoas,
            'atendimentos_recentes' => $recentes,
        ]);
    }
}
