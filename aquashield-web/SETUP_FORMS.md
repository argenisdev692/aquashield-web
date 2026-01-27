# AquaShield Restoration - Formularios de Contacto y Lead

Sistema completo de formularios con Astro + Supabase + Nodemailer

## 📋 Características

- ✅ Formulario de contacto en página principal (`Contact.astro`)
- ✅ Formulario de soporte (`ContactSupport.astro`)
- ✅ Modal de Facebook Lead con Google Maps Autocomplete (`FacebookLeadModal.astro`)
- ✅ Integración con Supabase para almacenamiento de datos
- ✅ Envío de emails con Nodemailer (Gmail SMTP)
- ✅ Protección con reCAPTCHA v3
- ✅ Templates HTML profesionales para emails

## 🚀 Instalación

### 1. Instalar dependencias

Las dependencias ya están instaladas. Si necesitas reinstalar:

\`\`\`bash
npm install @supabase/supabase-js nodemailer uuid @types/nodemailer
\`\`\`

### 2. Configurar variables de entorno

Crea un archivo `.env` en la raíz del proyecto con las siguientes variables:

\`\`\`env
# Supabase Configuration
PUBLIC_SUPABASE_URL=https://tu-proyecto.supabase.co
PUBLIC_SUPABASE_ANON_KEY=tu_anon_key
SUPABASE_SERVICE_ROLE_KEY=tu_service_role_key

# Google Maps API
PUBLIC_GOOGLE_MAPS_API_KEY=tu_google_maps_api_key

# Google reCAPTCHA v3
PUBLIC_RECAPTCHA_SITE_KEY=tu_recaptcha_site_key
RECAPTCHA_SECRET_KEY=tu_recaptcha_secret_key

# Email Configuration (Gmail SMTP)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=false
SMTP_USER=tu_email@gmail.com
SMTP_PASS=tu_gmail_app_password
EMAIL_FROM=tu_email@gmail.com
EMAIL_FROM_NAME=AquaShield Restoration LLC

# Admin Email (donde se envían las notificaciones)
ADMIN_EMAIL=admin@aquashieldrestoration.com

# Company Information
COMPANY_NAME=AquaShield Restoration LLC
COMPANY_PHONE=(555) 123-4567
COMPANY_EMAIL=info@aquashieldrestoration.com
COMPANY_ADDRESS=123 Restoration Way, Suite 100
COMPANY_CITY=Houston, TX 77001
COMPANY_FACEBOOK=https://facebook.com/aquashieldrestoration
COMPANY_INSTAGRAM=https://instagram.com/aquashieldrestoration
COMPANY_LINKEDIN=https://linkedin.com/company/aquashieldrestoration
COMPANY_TWITTER=https://twitter.com/aquashieldrest
\`\`\`

## 🔧 Configuración de Servicios

### Supabase

1. Crea un proyecto en [https://supabase.com](https://supabase.com)
2. Ejecuta los siguientes SQL para crear las tablas:

\`\`\`sql
-- Tabla contact_supports
CREATE TABLE contact_supports (
  id BIGSERIAL PRIMARY KEY,
  uuid UUID UNIQUE NOT NULL DEFAULT uuid_generate_v4(),
  first_name VARCHAR NOT NULL,
  last_name VARCHAR NOT NULL,
  email VARCHAR NOT NULL,
  phone VARCHAR NOT NULL,
  message TEXT NOT NULL,
  sms_consent BOOLEAN DEFAULT FALSE,
  readed BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW(),
  deleted_at TIMESTAMP
);

-- Tabla appointments
CREATE TABLE appointments (
  id BIGSERIAL PRIMARY KEY,
  uuid VARCHAR UNIQUE NOT NULL,
  first_name VARCHAR NOT NULL,
  last_name VARCHAR NOT NULL,
  phone VARCHAR NOT NULL,
  email VARCHAR,
  address VARCHAR NOT NULL,
  address_2 VARCHAR,
  city VARCHAR NOT NULL,
  state VARCHAR NOT NULL,
  zipcode VARCHAR NOT NULL,
  country VARCHAR NOT NULL,
  insurance_property BOOLEAN DEFAULT FALSE,
  message TEXT,
  sms_consent BOOLEAN DEFAULT FALSE,
  registration_date TIMESTAMP,
  inspection_date DATE,
  inspection_time TIME,
  inspection_status VARCHAR CHECK (inspection_status IN ('Confirmed', 'Completed', 'Pending', 'Declined')),
  status_lead VARCHAR CHECK (status_lead IN ('New', 'Called', 'Pending', 'Declined')),
  lead_source VARCHAR CHECK (lead_source IN ('Website', 'Facebook Ads', 'Reference', 'Retell AI')),
  follow_up_calls JSON,
  notes TEXT,
  owner VARCHAR,
  damage_detail TEXT,
  intent_to_claim BOOLEAN,
  follow_up_date DATE,
  additional_note TEXT,
  latitude DOUBLE PRECISION,
  longitude DOUBLE PRECISION,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW(),
  deleted_at TIMESTAMP
);

-- Índices para mejorar rendimiento
CREATE INDEX idx_contact_supports_email ON contact_supports(email);
CREATE INDEX idx_appointments_email ON appointments(email);
CREATE INDEX idx_appointments_status ON appointments(status_lead);
\`\`\`

3. Copia las keys desde Project Settings > API:
   - `PUBLIC_SUPABASE_URL`: Project URL
   - `PUBLIC_SUPABASE_ANON_KEY`: anon/public key
   - `SUPABASE_SERVICE_ROLE_KEY`: service_role key (mantén esto secreto)

### Google Maps API

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un proyecto o selecciona uno existente
3. Habilita "Maps JavaScript API" y "Places API"
4. Crea una API key en Credentials
5. Restricciones recomendadas:
   - Application restrictions: HTTP referrers
   - Agrega tu dominio (ej: `https://aquashieldrestoration.com/*`)
   - API restrictions: Maps JavaScript API, Places API

### Google reCAPTCHA v3

1. Ve a [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)
2. Registra un nuevo sitio:
   - Tipo: reCAPTCHA v3
   - Dominios: tu dominio y localhost (para desarrollo)
3. Copia:
   - Site Key → `PUBLIC_RECAPTCHA_SITE_KEY`
   - Secret Key → `RECAPTCHA_SECRET_KEY`

### Email Provider - Resend (Recomendado) o Gmail SMTP

#### Opción 1: Resend (Recomendado)

Resend es la mejor opción para emails transaccionales profesionales.

1. Ve a [https://resend.com](https://resend.com) y crea una cuenta
2. Verifica tu dominio o usa el dominio de prueba (`onboarding@resend.dev`)
3. Crea una API Key en el dashboard
4. Configura en `.env`:
   ```env
   EMAIL_PROVIDER=resend
   RESEND_API_KEY=re_tu_api_key
   EMAIL_FROM=onboarding@resend.dev  # O tu dominio verificado
   ```

**Ventajas de Resend:**
- ✅ Fácil configuración (solo API key)
- ✅ Alta deliverability
- ✅ Sin límites de App Passwords
- ✅ Dashboard con analytics
- ✅ Free tier generoso (3,000 emails/mes)

#### Opción 2: Gmail SMTP (Alternativa)

Si prefieres usar Gmail:

1. Habilita 2-Step Verification en tu cuenta de Gmail
2. Ve a [App Passwords](https://myaccount.google.com/apppasswords)
3. Genera una contraseña de aplicación:
   - Selecciona "Mail" y "Other (Custom name)"
   - Copia la contraseña de 16 caracteres
4. Configura en `.env`:
   ```env
   EMAIL_PROVIDER=smtp
   SMTP_HOST=smtp.gmail.com
   SMTP_PORT=587
   SMTP_SECURE=false
   SMTP_USER=tu_email@gmail.com
   SMTP_PASS=tu_app_password_16_caracteres
   EMAIL_FROM=tu_email@gmail.com
   ```

**Nota:** El sistema soporta ambos proveedores y cambia automáticamente según `EMAIL_PROVIDER`.

## 📁 Estructura de Archivos Creados

\`\`\`
aquashield-web/
├── src/
│   ├── lib/
│   │   └── supabase.ts              # Cliente Supabase + tipos TypeScript
│   ├── utils/
│   │   └── email.ts                 # Utilidades de email + templates HTML
│   ├── components/
│   │   ├── Contact.astro            # Formulario contacto (actualizado)
│   │   ├── ContactSupport.astro     # Formulario soporte (actualizado)
│   │   └── FacebookLeadModal.astro  # Modal de lead con Google Maps
│   └── pages/
│       └── api/
│           ├── contact-support.ts   # API endpoint para contact support
│           └── facebook-lead.ts     # API endpoint para leads
├── env.example                      # Ejemplo de variables de entorno
└── SETUP_FORMS.md                   # Este archivo
\`\`\`

## 🎯 Uso de los Componentes

### Formulario de Contacto Principal (Contact.astro)

Ya está integrado en `index.astro`. No requiere cambios adicionales.

### Formulario de Contact Support (ContactSupport.astro)

Crea una página para contact support:

\`\`\`astro
---
// src/pages/contact-support.astro
import Layout from '../layouts/Layout.astro';
import Navbar from '../components/Navbar.astro';
import ContactSupport from '../components/ContactSupport.astro';
import Footer from '../components/Footer.astro';
---

<Layout title="Contact Support - AquaShield Restoration">
  <Navbar />
  <ContactSupport />
  <Footer />
</Layout>
\`\`\`

### Facebook Lead Modal

Agrega el modal a tu página principal:

\`\`\`astro
---
// src/pages/index.astro
import FacebookLeadModal from '../components/FacebookLeadModal.astro';
// ... otros imports
---

<Layout title="...">
  <Navbar />
  <main>
    <!-- Tus secciones -->
  </main>
  <Footer />
  <FacebookLeadModal />  <!-- Agrega esto -->
</Layout>
\`\`\`

## 🧪 Testing

### Desarrollo Local

\`\`\`bash
npm run dev
\`\`\`

1. Prueba el formulario de contacto en la página principal
2. Prueba el modal de Facebook Lead (botón flotante en la esquina inferior derecha)
3. Verifica que los emails lleguen al `ADMIN_EMAIL`
4. Revisa las tablas en Supabase para confirmar que los datos se guardan

### Producción

1. Actualiza las restricciones de API en Google Cloud:
   - Agrega tu dominio de producción
   - Actualiza reCAPTCHA domains

2. Actualiza CORS en Supabase si es necesario:
   - Dashboard > Settings > API > CORS

3. Variables de entorno en hosting (Vercel/Netlify):
   - Asegúrate de configurar TODAS las variables de entorno

## 📧 Templates de Email

Los emails se envían automáticamente con los siguientes templates:

### Contact Support Notification
- Enviado al admin cuando alguien envía el formulario de contacto
- Incluye: nombre, email, teléfono, mensaje, consentimiento SMS

### New Lead Notification  
- Enviado al admin cuando alguien solicita inspección (Facebook Lead)
- Incluye: datos de contacto, dirección completa, estado de seguro

Ambos templates incluyen:
- Logo/nombre de la empresa
- Diseño profesional responsive
- Links a redes sociales
- Información de la empresa en el footer

## 🔒 Seguridad

- ✅ reCAPTCHA v3 para protección anti-spam
- ✅ Validación de datos en servidor (API endpoints)
- ✅ Service Role Key de Supabase solo en servidor
- ✅ Rate limiting y detección de duplicados
- ✅ Sanitización de inputs

## 🐛 Troubleshooting

### Los emails no se envían

1. Verifica que `SMTP_USER` y `SMTP_PASS` son correctos
2. Asegúrate de usar App Password de Gmail, no tu contraseña regular
3. Revisa los logs del servidor para errores de SMTP
4. Prueba con una herramienta como [https://www.smtper.net/](https://www.smtper.net/)

### reCAPTCHA falla

1. Verifica que las keys son correctas
2. Asegúrate de que el dominio está registrado en reCAPTCHA admin
3. Para desarrollo, agrega `localhost` a los dominios permitidos

### No se guarda en Supabase

1. Verifica la estructura de las tablas
2. Revisa que `SUPABASE_SERVICE_ROLE_KEY` tenga permisos de escritura
3. Revisa las policies de seguridad en Supabase (puede necesitar deshabilitar RLS temporalmente)

### Google Maps Autocomplete no funciona

1. Verifica que Places API está habilitada
2. Revisa que la API key tiene permisos para Places API
3. Verifica restricciones de dominio

## 📞 Soporte

Si tienes problemas, revisa:
1. Logs del navegador (Console)
2. Network tab para ver errores de API
3. Logs de Supabase
4. Variables de entorno correctamente configuradas

## ✅ Checklist de Implementación

- [ ] Crear proyecto en Supabase
- [ ] Ejecutar SQL para crear tablas
- [ ] Configurar Google Maps API
- [ ] Configurar reCAPTCHA v3
- [ ] Configurar Gmail App Password
- [ ] Copiar `.env.example` a `.env` y completar todas las variables
- [ ] Probar formulario de contacto
- [ ] Probar formulario de soporte
- [ ] Probar Facebook Lead Modal
- [ ] Verificar recepción de emails
- [ ] Verificar datos en Supabase
- [ ] Configurar variables en producción
- [ ] Actualizar restricciones de APIs para producción

---

**¡Todo listo!** 🎉 Los formularios están completamente funcionales con Astro, Supabase, reCAPTCHA y envío de emails.
