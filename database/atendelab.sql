
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','atendente') NOT NULL DEFAULT 'atendente',
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `curso` varchar(100) DEFAULT NULL,
  `periodo` varchar(100) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `documento` (`documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `tipos_atendimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `atendimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pessoa_id` int(11) NOT NULL,
  `tipo_atendimento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('aberto','em_andamento','concluido','cancelado') NOT NULL DEFAULT 'aberto',
  `data_atendimento` date NOT NULL,
  `horario_atendimento` time NOT NULL,
  `observacao_final` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pessoa_id` (`pessoa_id`),
  KEY `tipo_atendimento_id` (`tipo_atendimento_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `atendimentos_ibfk_1` FOREIGN KEY (`pessoa_id`) REFERENCES `pessoas` (`id`),
  CONSTRAINT `atendimentos_ibfk_2` FOREIGN KEY (`tipo_atendimento_id`) REFERENCES `tipos_atendimentos` (`id`),
  CONSTRAINT `atendimentos_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Usuario administrador padrao
-- E-mail: admin@atendelab.com  |  Senha: 123456
--

--
-- Usuarios (senha de todos: 123456)
--
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `status`) VALUES
(1, 'Administrador', 'admin@atendelab.com', '$2y$10$3IZHNdb/O7p0ce6HDg6QmeF5HFpRZKDaP7ALUhbtT8FNJKrteDRmi', 'admin', 'ativo'),
(2, 'Carla Mendes', 'carla@atendelab.com', '$2y$10$3IZHNdb/O7p0ce6HDg6QmeF5HFpRZKDaP7ALUhbtT8FNJKrteDRmi', 'atendente', 'ativo'),
(3, 'Bruno Souza', 'bruno@atendelab.com', '$2y$10$3IZHNdb/O7p0ce6HDg6QmeF5HFpRZKDaP7ALUhbtT8FNJKrteDRmi', 'atendente', 'ativo');

--
-- Tipos de atendimento
--
INSERT INTO `tipos_atendimentos` (`id`, `nome`, `descricao`, `status`) VALUES
(1, 'Atendimento acadêmico', 'Orientações gerais sobre a vida acadêmica.', 'ativo'),
(2, 'Suporte técnico', 'Apoio com sistemas, portal do aluno e laboratórios.', 'ativo'),
(3, 'Orientação de TCC', 'Acompanhamento do trabalho de conclusão de curso.', 'ativo'),
(4, 'Financeiro e bolsas', 'Negociação de mensalidades, bolsas e financiamento.', 'ativo'),
(5, 'Estágio e carreira', 'Encaminhamento para vagas de estágio e orientação profissional.', 'ativo'),
(6, 'Apoio psicopedagógico', 'Suporte emocional e pedagógico ao estudante.', 'inativo');

--
-- Pessoas atendidas
--
INSERT INTO `pessoas` (`id`, `nome`, `documento`, `telefone`, `email`, `curso`, `periodo`, `observacoes`, `status`) VALUES
(1, 'Ana Beatriz Lima', '101.202.303-01', '(47) 99876-1010', 'ana.lima@univille.br', 'Engenharia de Software', '5', 'Aluna veterana, participa do PET.', 'ativo'),
(2, 'Carlos Eduardo Rocha', '102.203.304-02', '(47) 99876-2020', 'carlos.rocha@univille.br', 'Engenharia de Software', '5', NULL, 'ativo'),
(3, 'Mariana Oliveira', '103.204.305-03', '(47) 99876-3030', 'mariana.oliveira@univille.br', 'Direito', '3', 'Solicitou revisão de matrícula.', 'ativo'),
(4, 'Pedro Henrique Alves', '104.205.306-04', '(47) 99876-4040', 'pedro.alves@univille.br', 'Administração', '7', NULL, 'ativo'),
(5, 'Juliana Santos', '105.206.307-05', '(47) 99876-5050', 'juliana.santos@univille.br', 'Psicologia', '4', 'Acompanhamento psicopedagógico.', 'ativo'),
(6, 'Rafael Costa', '106.207.308-06', '(47) 99876-6060', 'rafael.costa@univille.br', 'Engenharia Civil', '8', NULL, 'ativo'),
(7, 'Fernanda Souza', '107.208.309-07', '(47) 99876-7070', 'fernanda.souza@univille.br', 'Medicina', '2', 'Bolsista PROUNI.', 'ativo'),
(8, 'Lucas Pereira', '108.209.310-08', '(47) 99876-8080', 'lucas.pereira@univille.br', 'Ciência da Computação', '6', NULL, 'ativo'),
(9, 'Beatriz Carvalho', '109.210.311-09', '(47) 99876-9090', 'beatriz.carvalho@univille.br', 'Arquitetura e Urbanismo', '5', NULL, 'ativo'),
(10, 'Gustavo Martins', '110.211.312-10', '(47) 99877-1112', 'gustavo.martins@univille.br', 'Engenharia de Software', '5', 'Representante de turma.', 'ativo'),
(11, 'Larissa Fernandes', '111.212.313-11', '(47) 99877-2223', 'larissa.fernandes@univille.br', 'Enfermagem', '1', 'Transferida de outra instituição.', 'inativo'),
(12, 'Diego Ramos', '112.213.314-12', '(47) 99877-3334', 'diego.ramos@univille.br', 'Educação Física', '9', 'Concluindo o curso.', 'inativo');

--
-- Atendimentos
--
INSERT INTO `atendimentos` (`id`, `pessoa_id`, `tipo_atendimento_id`, `usuario_id`, `descricao`, `status`, `data_atendimento`, `horario_atendimento`, `observacao_final`) VALUES
(1, 1, 1, 1, 'Dúvidas sobre a grade curricular do 5º período.', 'concluido', '2026-06-01', '09:00:00', 'Aluna orientada sobre pré-requisitos.'),
(2, 2, 2, 2, 'Sem acesso ao portal do aluno.', 'concluido', '2026-06-02', '10:30:00', 'Senha redefinida e acesso liberado.'),
(3, 3, 1, 1, 'Revisão de matrícula em disciplina optativa.', 'em_andamento', '2026-06-03', '14:00:00', NULL),
(4, 4, 4, 3, 'Negociação de mensalidade em atraso.', 'aberto', '2026-06-05', '11:15:00', NULL),
(5, 5, 6, 2, 'Encaminhamento para apoio psicopedagógico.', 'concluido', '2026-06-06', '16:00:00', 'Aluna encaminhada ao núcleo de apoio.'),
(6, 6, 3, 1, 'Definição do tema de TCC.', 'em_andamento', '2026-06-08', '08:30:00', NULL),
(7, 7, 4, 3, 'Entrega de documentação da bolsa PROUNI.', 'concluido', '2026-06-09', '13:45:00', 'Documentos validados e protocolados.'),
(8, 8, 5, 2, 'Busca por vaga de estágio em TI.', 'aberto', '2026-06-10', '15:20:00', NULL),
(9, 9, 2, 1, 'Erro ao enviar trabalho no AVA.', 'cancelado', '2026-06-11', '09:50:00', 'Aluna resolveu por conta própria antes do atendimento.'),
(10, 10, 1, 2, 'Orientação sobre colação de grau.', 'aberto', '2026-06-12', '10:10:00', NULL),
(11, 1, 3, 1, 'Ajuste do cronograma de entregas do TCC.', 'em_andamento', '2026-06-15', '14:30:00', NULL),
(12, 11, 1, 3, 'Processo de transferência de instituição.', 'concluido', '2026-05-20', '11:00:00', 'Transferência concluída no semestre anterior.'),
(13, 4, 5, 2, 'Orientação de carreira na área de gestão.', 'concluido', '2026-06-18', '16:40:00', 'Indicado programa de trainee parceiro.'),
(14, 6, 2, 1, 'Configuração de software do laboratório.', 'concluido', '2026-06-20', '09:15:00', 'Software instalado e testado.'),
(15, 8, 4, 3, 'Renovação de financiamento estudantil.', 'em_andamento', '2026-06-22', '13:00:00', NULL),
(16, 10, 5, 2, 'Revisão de currículo para vaga de estágio.', 'aberto', '2026-06-25', '17:00:00', NULL);

COMMIT;
