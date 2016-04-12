UPDATE ps_configuration SET value = '1' WHERE name = 'PS_REWRITING_SETTINGS';
UPDATE ps_configuration SET value = '{{ server_hostname }}' WHERE name = 'PS_SHOP_DOMAIN';
UPDATE ps_configuration SET value = '{{ server_hostname }}' WHERE name = 'PS_SHOP_DOMAIN_SSL';
UPDATE ps_configuration SET value = {% if maestrano_environment == 'local' %}'0'{% else %}'1'{% endif %} WHERE name = 'PS_SSL_ENABLED';
INSERT INTO ps_configuration (name, value, date_add, date_upd) VALUES('PS_SSL_ENABLED_EVERYWHERE', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00') ON DUPLICATE KEY UPDATE value=VALUES(value);
