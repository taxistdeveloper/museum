-- Создание таблицы ролей
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE COMMENT 'Название роли',
    description TEXT COMMENT 'Описание роли',
    permissions JSON COMMENT 'Права доступа в JSON формате',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Роли пользователей';

-- Создание таблицы пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE COMMENT 'Имя пользователя',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email пользователя',
    password VARCHAR(255) NOT NULL COMMENT 'Хешированный пароль',
    first_name VARCHAR(50) COMMENT 'Имя',
    last_name VARCHAR(50) COMMENT 'Фамилия',
    role_id INT DEFAULT 2 COMMENT 'ID роли (по умолчанию пользователь)',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Активен ли пользователь',
    last_login TIMESTAMP NULL COMMENT 'Последний вход',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Пользователи системы';

-- Добавляем индексы для оптимизации
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_is_active ON users(is_active);

-- Вставляем базовые роли
INSERT INTO roles (name, description, permissions) VALUES
('admin', 'Администратор', '{"all": true, "users": {"create": true, "read": true, "update": true, "delete": true}, "content": {"create": true, "read": true, "update": true, "delete": true}}'),
('editor', 'Редактор', '{"users": {"read": true}, "content": {"create": true, "read": true, "update": true, "delete": false}}'),
('user', 'Пользователь', '{"content": {"read": true}}');

-- Создаем администратора по умолчанию (пароль: admin123)
INSERT INTO users (username, email, password, first_name, last_name, role_id, is_active) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор', 'Системы', 1, TRUE);

-- Создаем тестового редактора (пароль: editor123)
INSERT INTO users (username, email, password, first_name, last_name, role_id, is_active) VALUES
('editor', 'editor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Редактор', 'Контента', 2, TRUE);

-- Создаем обычного пользователя (пароль: user123)
INSERT INTO users (username, email, password, first_name, last_name, role_id, is_active) VALUES
('user', 'user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Обычный', 'Пользователь', 3, TRUE);
