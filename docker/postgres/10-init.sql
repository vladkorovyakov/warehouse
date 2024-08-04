DROP DATABASE IF EXISTS warehouse;
DROP USER IF EXISTS warehouse;

CREATE USER warehouse WITH PASSWORD 'warehouse';
ALTER ROLE warehouse WITH CREATEDB;

CREATE DATABASE warehouse WITH OWNER = 'warehouse';
