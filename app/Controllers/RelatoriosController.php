<?php

/**
 * RelatoriosController
 *
 * Reservado para a etapa de relatorios. Mantido como esqueleto nesta aula
 * para preservar a estrutura oficial do projeto sem introduzir rotas novas.
 */
class RelatoriosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }
}
