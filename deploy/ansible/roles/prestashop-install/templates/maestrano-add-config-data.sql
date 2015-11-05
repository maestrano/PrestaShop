UPDATE ps_configuration SET value = '1' WHERE name = 'PS_REWRITING_SETTINGS';
UPDATE ps_configuration SET value = '{{ server_hostname }}' WHERE name = 'PS_SHOP_DOMAIN';
UPDATE ps_configuration SET value = '{{ server_hostname }}' WHERE name = 'PS_SHOP_DOMAIN_SSL';
