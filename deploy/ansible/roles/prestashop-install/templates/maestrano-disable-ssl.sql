UPDATE ps_configuration SET value = '0' WHERE name = 'PS_SSL_ENABLED';
INSERT INTO ps_configuration (name, value, date_add, date_upd) VALUES('PS_SSL_ENABLED_EVERYWHERE', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00') ON DUPLICATE KEY UPDATE value=VALUES(value);
