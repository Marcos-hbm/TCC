-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `sistema_escalacao` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_escalacao`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(120) NOT NULL,
  `email` VARCHAR(160) NOT NULL,
  `data_nascimento` DATE NOT NULL,
  `cpf` CHAR(11) NOT NULL,
  `telefone` VARCHAR(11) NOT NULL,
  `senha_hash` VARCHAR(255) NOT NULL,
  `foto_path` VARCHAR(255) DEFAULT NULL,
  `genero` ENUM('Masculino','Feminino','Outro','Prefiro não dizer') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`),
  UNIQUE KEY `uniq_cpf` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Companies table
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(160) NOT NULL,
  `cnpj` CHAR(14) NOT NULL,
  `email` VARCHAR(160) NOT NULL,
  `telefone` VARCHAR(11) NOT NULL,
  `senha_hash` VARCHAR(255) NOT NULL,
  `foto_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cnpj` (`cnpj`),
  UNIQUE KEY `uniq_email_empresa` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events table
CREATE TABLE IF NOT EXISTS `eventos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` INT UNSIGNED NOT NULL,
  `nome` VARCHAR(180) NOT NULL,
  `descricao` TEXT NULL,
  `data_evento` DATE NOT NULL,
  `valor_cache` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `observacoes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_eventos_empresa` (`empresa_id`),
  CONSTRAINT `fk_eventos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vínculos entre usuários e empresas (estilo follow/solicitação)
CREATE TABLE IF NOT EXISTS `vinculos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `empresa_id` INT UNSIGNED NOT NULL,
  `status` ENUM('pendente','aprovado','recusado') NOT NULL DEFAULT 'pendente',
  `solicitado_por` ENUM('user','empresa') NOT NULL,
  `favorito` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_empresa` (`user_id`,`empresa_id`),
  KEY `idx_vinculos_empresa` (`empresa_id`),
  CONSTRAINT `fk_vinculos_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vinculos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inscrições de usuários em eventos (apenas quando vínculo aprovado)
CREATE TABLE IF NOT EXISTS `evento_inscricoes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `evento_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `status` ENUM('inscrito','aprovado','recusado','cancelado') NOT NULL DEFAULT 'inscrito',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_evento_user` (`evento_id`,`user_id`),
  KEY `idx_insc_user` (`user_id`),
  CONSTRAINT `fk_insc_evento` FOREIGN KEY (`evento_id`) REFERENCES `eventos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_insc_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
