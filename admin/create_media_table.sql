-- Создание таблицы для публикаций СМИ
CREATE TABLE IF NOT EXISTS media_publications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    media_name VARCHAR(255) NOT NULL COMMENT 'Название СМИ',
    media_type ENUM('newspaper', 'tv', 'radio', 'online', 'magazine') DEFAULT 'newspaper' COMMENT 'Тип СМИ',
    description TEXT NOT NULL COMMENT 'Описание публикации',
    link VARCHAR(500) DEFAULT NULL COMMENT 'Ссылка на публикацию',
    publication_date DATE NOT NULL COMMENT 'Дата публикации',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания записи',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата обновления записи'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Публикации СМИ о организации';

-- Добавляем индексы для оптимизации запросов
CREATE INDEX idx_publication_date ON media_publications(publication_date);
CREATE INDEX idx_media_type ON media_publications(media_type);
CREATE INDEX idx_media_name ON media_publications(media_name);

-- Вставляем примеры данных
INSERT INTO media_publications (media_name, media_type, description, link, publication_date) VALUES
('Казахстанская правда', 'newspaper', 'Инновационные проекты студентов нашего университета привлекают внимание ведущих СМИ страны. В статье рассказывается о достижениях в области технологий и образования.', 'https://kazpravda.kz', '2024-03-15'),
('Хабар 24', 'tv', 'Телевизионный репортаж о научных исследованиях и инновационных разработках наших студентов. Особое внимание уделено проектам в области искусственного интеллекта.', 'https://habar24.kz', '2024-03-08'),
('Tengrinews.kz', 'online', 'Интервью с руководством университета о перспективах развития образования и внедрении новых технологий в учебный процесс.', 'https://tengrinews.kz', '2024-02-22'),
('Радио Астана', 'radio', 'Радиопередача о культурных мероприятиях и студенческой жизни. Обсуждение роли университета в развитии молодежного творчества.', 'https://radioastana.kz', '2024-02-14'),
('Digital Kazakhstan', 'online', 'Статья о цифровизации образования и внедрении современных IT-решений в учебный процесс. Особое внимание уделено нашим инновационным проектам.', 'https://digitalkz.kz', '2024-02-05'),
('Образование.kz', 'online', 'Публикация о достижениях наших выпускников и их вкладе в развитие различных отраслей экономики Казахстана.', 'https://obrazovanie.kz', '2024-01-28');
