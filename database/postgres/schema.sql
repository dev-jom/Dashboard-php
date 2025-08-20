-- database/postgres/schema.sql

-- Crie usuário e banco se necessário (opcional; pode pular se já existir)
-- CREATE USER planilha_user WITH PASSWORD 'strong_password_here';
-- CREATE DATABASE planilha_db OWNER planilha_user;
-- GRANT ALL PRIVILEGES ON DATABASE planilha_db TO planilha_user;

-- Use o banco (se estiver dentro do psql)
-- \c planilha_db

CREATE TABLE IF NOT EXISTS tests (
  id BIGSERIAL PRIMARY KEY,
  tipo_teste VARCHAR(255) NOT NULL,
  numero_ticket VARCHAR(255) NOT NULL,
  resumo_tarefa TEXT NOT NULL,
  estrutura VARCHAR(255) NOT NULL,
  atribuido_a VARCHAR(255) NOT NULL,
  resultado VARCHAR(20) NOT NULL CHECK (resultado IN ('Aprovado','Reprovado','Validado')),
  data_teste DATE NOT NULL,
  created_at TIMESTAMP(0) NULL,
  updated_at TIMESTAMP(0) NULL
);

-- Índices úteis para os agrupamentos do dashboard
CREATE INDEX IF NOT EXISTS idx_tests_resultado ON tests (resultado);
CREATE INDEX IF NOT EXISTS idx_tests_estrutura ON tests (estrutura);
CREATE INDEX IF NOT EXISTS idx_tests_atribuido_a ON tests (atribuido_a);
