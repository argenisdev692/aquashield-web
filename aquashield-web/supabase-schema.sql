-- AquaShield Restoration - Database Schema for Supabase
-- Ejecuta este SQL en tu proyecto de Supabase (SQL Editor)

-- Enable UUID extension if not already enabled
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- =====================================================
-- Tabla: contact_supports
-- Descripción: Almacena solicitudes de soporte/contacto
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_supports (
  id BIGSERIAL PRIMARY KEY,
  uuid UUID UNIQUE NOT NULL DEFAULT uuid_generate_v4(),
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  message TEXT NOT NULL,
  sms_consent BOOLEAN DEFAULT FALSE,
  readed BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  deleted_at TIMESTAMP WITH TIME ZONE
);

-- Índices para contact_supports
CREATE INDEX IF NOT EXISTS idx_contact_supports_email ON contact_supports(email);
CREATE INDEX IF NOT EXISTS idx_contact_supports_created_at ON contact_supports(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_contact_supports_readed ON contact_supports(readed);

-- =====================================================
-- Tabla: appointments
-- Descripción: Almacena solicitudes de inspección/leads
-- =====================================================
CREATE TABLE IF NOT EXISTS appointments (
  id BIGSERIAL PRIMARY KEY,
  uuid VARCHAR(255) UNIQUE NOT NULL,
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  email VARCHAR(255),
  address VARCHAR(500) NOT NULL,
  address_2 VARCHAR(255),
  city VARCHAR(255) NOT NULL,
  state VARCHAR(10) NOT NULL,
  zipcode VARCHAR(20) NOT NULL,
  country VARCHAR(100) NOT NULL,
  insurance_property BOOLEAN DEFAULT FALSE,
  message TEXT,
  sms_consent BOOLEAN DEFAULT FALSE,
  registration_date TIMESTAMP WITH TIME ZONE,
  inspection_date DATE,
  inspection_time TIME,
  inspection_status VARCHAR(50) CHECK (inspection_status IN ('Confirmed', 'Completed', 'Pending', 'Declined')),
  status_lead VARCHAR(50) CHECK (status_lead IN ('New', 'Called', 'Pending', 'Declined')),
  lead_source VARCHAR(50) CHECK (lead_source IN ('Website', 'Facebook Ads', 'Reference', 'Retell AI')),
  follow_up_calls JSON,
  notes TEXT,
  owner VARCHAR(255),
  damage_detail TEXT,
  intent_to_claim BOOLEAN,
  follow_up_date DATE,
  additional_note TEXT,
  latitude DOUBLE PRECISION,
  longitude DOUBLE PRECISION,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  deleted_at TIMESTAMP WITH TIME ZONE
);

-- Índices para appointments
CREATE INDEX IF NOT EXISTS idx_appointments_email ON appointments(email);
CREATE INDEX IF NOT EXISTS idx_appointments_status_lead ON appointments(status_lead);
CREATE INDEX IF NOT EXISTS idx_appointments_inspection_status ON appointments(inspection_status);
CREATE INDEX IF NOT EXISTS idx_appointments_registration_date ON appointments(registration_date DESC);
CREATE INDEX IF NOT EXISTS idx_appointments_inspection_date ON appointments(inspection_date);
CREATE INDEX IF NOT EXISTS idx_appointments_lead_source ON appointments(lead_source);

-- =====================================================
-- Triggers para updated_at automático
-- =====================================================

-- Function para actualizar updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Trigger para contact_supports
DROP TRIGGER IF EXISTS update_contact_supports_updated_at ON contact_supports;
CREATE TRIGGER update_contact_supports_updated_at
    BEFORE UPDATE ON contact_supports
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Trigger para appointments
DROP TRIGGER IF EXISTS update_appointments_updated_at ON appointments;
CREATE TRIGGER update_appointments_updated_at
    BEFORE UPDATE ON appointments
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- =====================================================
-- Row Level Security (RLS) - OPCIONAL
-- Descomenta si quieres habilitar seguridad a nivel de fila
-- =====================================================

-- Para desarrollo, puedes deshabilitar RLS temporalmente
-- o configurar políticas según tus necesidades

-- ALTER TABLE contact_supports ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE appointments ENABLE ROW LEVEL SECURITY;

-- Política de ejemplo: permitir inserción desde service_role
-- CREATE POLICY "Enable insert for service_role" ON contact_supports
--   FOR INSERT
--   TO service_role
--   WITH CHECK (true);

-- CREATE POLICY "Enable insert for service_role" ON appointments
--   FOR INSERT
--   TO service_role
--   WITH CHECK (true);

-- =====================================================
-- Verificación de estructura
-- =====================================================

-- Verifica que las tablas se crearon correctamente
SELECT 'contact_supports' AS table_name, COUNT(*) AS row_count FROM contact_supports
UNION ALL
SELECT 'appointments', COUNT(*) FROM appointments;

-- Verifica los índices
SELECT 
    tablename,
    indexname,
    indexdef
FROM pg_indexes
WHERE schemaname = 'public'
  AND tablename IN ('contact_supports', 'appointments')
ORDER BY tablename, indexname;

-- =====================================================
-- Datos de prueba (OPCIONAL - solo para desarrollo)
-- =====================================================

-- Ejemplo de inserción para probar
/*
INSERT INTO contact_supports (first_name, last_name, email, phone, message, sms_consent)
VALUES ('John', 'Doe', 'john@example.com', '+15551234567', 'Test message', true);

INSERT INTO appointments (
  uuid, first_name, last_name, phone, email, address, city, state, zipcode, country,
  insurance_property, registration_date, inspection_status, status_lead, lead_source
)
VALUES (
  uuid_generate_v4()::text,
  'Jane',
  'Smith',
  '+15559876543',
  'jane@example.com',
  '123 Main St',
  'Houston',
  'TX',
  '77001',
  'USA',
  true,
  NOW(),
  'Pending',
  'New',
  'Website'
);
*/

-- =====================================================
-- IMPORTANTE: Después de ejecutar este script
-- =====================================================
-- 1. Ve a Project Settings > API en Supabase Dashboard
-- 2. Copia las siguientes keys a tu archivo .env:
--    - Project URL → PUBLIC_SUPABASE_URL
--    - anon/public key → PUBLIC_SUPABASE_ANON_KEY
--    - service_role key → SUPABASE_SERVICE_ROLE_KEY (¡mantén esto seguro!)
-- 3. Si habilitas RLS, configura las políticas según tus necesidades
