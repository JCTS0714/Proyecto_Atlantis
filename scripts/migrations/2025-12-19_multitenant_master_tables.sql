-- Multi-tenant (semi-automatic) master tables
-- Run this in the MASTER database (current production DB).
-- Safe-ish to re-run: uses IF NOT EXISTS.

-- Tenants catalog
CREATE TABLE IF NOT EXISTS tenants (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ruc VARCHAR(32) NOT NULL,
  subdomain VARCHAR(63) NOT NULL,
  status ENUM('provisioning','active','suspended') NOT NULL DEFAULT 'provisioning',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tenants_ruc (ruc),
  UNIQUE KEY uq_tenants_subdomain (subdomain),
  KEY ix_tenants_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Database mapping for each tenant (one DB per tenant)
CREATE TABLE IF NOT EXISTS tenant_db (
  tenant_id INT UNSIGNED NOT NULL,
  db_host VARCHAR(255) NOT NULL,
  db_name VARCHAR(64) NOT NULL,
  db_user VARCHAR(64) NOT NULL,
  db_pass VARCHAR(255) NOT NULL,
  db_charset VARCHAR(32) NOT NULL DEFAULT 'utf8mb4',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (tenant_id),
  UNIQUE KEY uq_tenant_db_name (db_name),
  CONSTRAINT fk_tenant_db_tenant
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin users for the panel (separate from tenant users)
CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(64) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admin_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit log for panel actions
CREATE TABLE IF NOT EXISTS audit_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_user_id INT UNSIGNED NULL,
  tenant_id INT UNSIGNED NULL,
  action VARCHAR(64) NOT NULL,
  details TEXT NULL,
  ip VARCHAR(64) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_audit_created_at (created_at),
  KEY ix_audit_action (action),
  KEY ix_audit_tenant_id (tenant_id),
  CONSTRAINT fk_audit_admin_user
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id)
    ON DELETE SET NULL,
  CONSTRAINT fk_audit_tenant
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
