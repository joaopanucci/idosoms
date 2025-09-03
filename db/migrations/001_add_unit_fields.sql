-- db/migrations/001_add_unit_fields.sql
ALTER TABLE patients 
  ADD COLUMN unit_cnes VARCHAR(15) NULL AFTER municipality_code,
  ADD COLUMN unit_name VARCHAR(160) NULL AFTER unit_cnes;
