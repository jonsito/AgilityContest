# Usuario 'admin' Dios (yomismo :-)
GRANT USAGE ON `agility`.* TO 'agility_admin'@'localhost' IDENTIFIED BY PASSWORD '*08B004F5A4F0C12BC39ACA150D08073AC58298C7';
GRANT ALL PRIVILEGES ON `agility`.* TO 'agility_admin'@'localhost' WITH GRANT OPTION;
# Usuario 'operator' anyadir/borrar/modificar datos (sala de control)
GRANT USAGE ON `agility`.* TO 'agility_operator'@'localhost' IDENTIFIED BY PASSWORD '*133E83223499F08EAABCB24B71D08169FA241A9B';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE TEMPORARY TABLES, LOCK TABLES, TRIGGER, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, EVENT ON `agility`.* TO 'agility_operator'@'localhost';
# Usuario 'user' insertar datos (ayudante de pista)
GRANT USAGE ON `agility`.* TO 'agility_user'@'localhost' IDENTIFIED BY PASSWORD '*FA79D4F71FE9C4BEF173D2D0D8535A9A38253676';
GRANT SELECT, INSERT, UPDATE, CREATE TEMPORARY TABLES, EXECUTE, SHOW VIEW ON `agility`.* TO 'agility_user'@'localhost';
# Usuario 'guest' consultar datos (invitados)
GRANT USAGE ON `agility`.* TO 'agility_guest'@'localhost' IDENTIFIED BY PASSWORD '*A93683F47D32463876747B97C62B8BF4CDD14E7C';
GRANT SELECT, EXECUTE, SHOW VIEW ON `agility`.* TO 'agility_guest'@'localhost';
