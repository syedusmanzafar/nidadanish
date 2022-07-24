REPLACE INTO ?:sd_labels (label_id, background_color, text_color, display_type, label_type, attachable, position, status) VALUES(1, 'rgb(106, 168, 79)', 'rgb(0, 0, 0)', 'text', 'freeshipping', 'N', 1, 'A');
REPLACE INTO ?:sd_labels (label_id, background_color, text_color, display_type, label_type, attachable, position, status) VALUES(2, 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'text', 'discount', 'N', 2, 'A');
REPLACE INTO ?:sd_labels (label_id, background_color, text_color, display_type, label_type, attachable, position, status) VALUES(3, 'rgb(106, 168, 79)', 'rgb(0, 0, 0)', 'text', 'new', 'Y', 3, 'A');
REPLACE INTO ?:sd_labels (label_id, background_color, text_color, display_type, label_type, attachable, position, status) VALUES(4, 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'text', 'hit', 'Y', 4, 'A');
REPLACE INTO ?:sd_labels (label_id, background_color, text_color, display_type, label_type, attachable, position, status) VALUES(5, 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'text', 'onlyat', 'Y', 5, 'A');
REPLACE INTO ?:sd_labels (label_id, background_color, text_color, display_type, label_type, attachable, position, status) VALUES(6, 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'text', 'outofstock', 'N', 6, 'A');
REPLACE INTO ?:sd_labels (label_id, background_color, text_color, display_type, label_type, attachable, position, status) VALUES(7, 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'text', 'product_running_out', 'N', 7, 'A');
REPLACE INTO ?:sd_labels (label_id, background_color, text_color, display_type, label_type, attachable, position, status) VALUES(8, 'rgb(255, 0, 0)', 'rgb(0, 0, 0)', 'text', 'on_backorder', 'N', 8, 'A');

REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(1, 'Free shipping', 'en');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(1, 'Бесплатная доставка', 'ru');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(2, 'Save [discount]', 'en');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(2, 'Скидка [discount]', 'ru');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(3, 'New', 'en');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(3, 'Новинка', 'ru');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(4, 'Hit', 'en');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(4, 'Хит', 'ru');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(5, 'Only at [company]', 'en');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(5, 'Только в [company]', 'ru');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(6, 'Out of stock', 'en');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(6, 'Нет в наличии', 'ru');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(7, 'Product is running out', 'en');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(7, 'Товар заканчивается', 'ru');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(8, 'On backorder', 'en');
REPLACE INTO ?:sd_labels_descriptions (label_id, name, lang_code) VALUES(8, 'Предзаказ', 'ru');