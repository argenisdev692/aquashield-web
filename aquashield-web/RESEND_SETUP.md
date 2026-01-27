# Configuración de Email con Resend

Resend es el proveedor de email recomendado para AquaShield Restoration. Es más fácil de configurar que Gmail SMTP y tiene mejor deliverability.

## 🚀 Configuración Rápida

### 1. Crear cuenta en Resend

1. Ve a [https://resend.com](https://resend.com)
2. Regístrate con tu email
3. Verifica tu correo

### 2. Obtener API Key

1. En el dashboard de Resend, ve a **API Keys**
2. Click en "Create API Key"
3. Dale un nombre (ej: "AquaShield Production")
4. Copia la API key (comienza con `re_`)

### 3. Configurar dominio (Opcional pero recomendado)

#### Para desarrollo (sin dominio propio):
Usa el dominio de prueba de Resend:
```env
EMAIL_FROM=onboarding@resend.dev
```

#### Para producción (con dominio propio):
1. En Resend dashboard, ve a **Domains**
2. Click "Add Domain"
3. Ingresa tu dominio (ej: `aquashieldrestoration.com`)
4. Agrega los registros DNS que te proporciona Resend:
   - SPF record (TXT)
   - DKIM record (TXT)
   - DMARC record (TXT)
5. Espera la verificación (puede tomar hasta 48 horas)
6. Una vez verificado, usa:
   ```env
   EMAIL_FROM=noreply@aquashieldrestoration.com
   ```

### 4. Variables de entorno

Agrega a tu archivo `.env`:

```env
# Email Provider
EMAIL_PROVIDER=resend

# Resend Configuration
RESEND_API_KEY=re_tu_api_key_aqui

# Email From
EMAIL_FROM=onboarding@resend.dev  # O tu dominio verificado
EMAIL_FROM_NAME=AquaShield Restoration LLC

# Admin Email (donde recibes notificaciones)
ADMIN_EMAIL=admin@aquashieldrestoration.com
```

## 📊 Límites y Pricing

### Free Tier
- ✅ 3,000 emails/mes
- ✅ 100 emails/día
- ✅ Todos los features incluidos
- ✅ Perfecto para desarrollo y producción pequeña

### Pro Plan ($20/mes)
- 50,000 emails/mes
- Sin límite diario
- Recomendado para producción

## ✅ Ventajas de Resend vs Gmail SMTP

| Característica | Resend | Gmail SMTP |
|----------------|--------|------------|
| Configuración | 1 API key | App Password + 2FA |
| Límite diario | 100-1000+ | 500 emails |
| Deliverability | Alta | Media |
| Dominio propio | ✅ Sí | ❌ No |
| Analytics | ✅ Dashboard | ❌ No |
| Webhooks | ✅ Sí | ❌ No |
| Rate limiting | Generoso | Estricto |

## 🧪 Probar el envío

### Opción 1: Usar el sitio web
1. Inicia el servidor: `npm run dev`
2. Llena el formulario de contacto
3. Verifica que el email llegue a `ADMIN_EMAIL`

### Opción 2: Script de prueba

Crea `test-email.js`:

```javascript
import { Resend } from 'resend';

const resend = new Resend('re_tu_api_key');

async function testEmail() {
  try {
    const { data, error } = await resend.emails.send({
      from: 'onboarding@resend.dev',
      to: ['tu_email@example.com'],
      subject: 'Test Email',
      html: '<h1>Hello World!</h1>',
    });

    if (error) {
      console.error('Error:', error);
    } else {
      console.log('Success:', data);
    }
  } catch (error) {
    console.error('Failed:', error);
  }
}

testEmail();
```

Ejecuta:
```bash
node test-email.js
```

## 📧 Templates de Email

El sistema incluye 2 templates profesionales:

### 1. Contact Support Notification
Se envía cuando alguien usa el formulario de contacto.

**Incluye:**
- Nombre completo
- Email
- Teléfono formateado
- Mensaje completo
- Consentimiento SMS
- Fecha/hora de envío

### 2. New Lead Notification
Se envía cuando alguien solicita inspección gratuita.

**Incluye:**
- Información de contacto completa
- Dirección completa con coordenadas
- Estado de seguro
- Mensaje del cliente
- Consentimiento SMS

Ambos templates incluyen:
- Diseño responsive
- Branding de la empresa
- Links a redes sociales
- Footer profesional

## 🔒 Seguridad

### Buenas prácticas:

1. **Nunca expongas tu API key**
   ```env
   # ❌ MALO - no commitear a git
   RESEND_API_KEY=re_1234567890
   
   # ✅ BUENO - usar .env local
   ```

2. **Usa diferentes keys para dev y prod**
   - Desarrollo: `re_dev_xxx`
   - Producción: `re_prod_xxx`

3. **Configura `.gitignore`**
   ```gitignore
   .env
   .env.local
   .env.production
   ```

4. **Verifica el dominio en producción**
   - Mejora deliverability
   - Evita spam folder
   - Profesionaliza los emails

## 🐛 Troubleshooting

### Email no llega

1. **Verifica la API key**
   ```bash
   echo $RESEND_API_KEY  # Debe mostrar re_...
   ```

2. **Revisa los logs**
   ```javascript
   // En src/utils/email.ts
   console.log('Sending email via Resend...');
   console.log('To:', to);
   console.log('Subject:', subject);
   ```

3. **Verifica en Resend Dashboard**
   - Ve a "Logs" en Resend
   - Revisa el status del email
   - Verifica bounce/spam reports

### Email va a spam

1. **Verifica SPF/DKIM** (si usas dominio propio)
2. **Usa dominio verificado** (no `onboarding@resend.dev` en producción)
3. **Evita palabras spam** en subject/body
4. **Incluye un unsubscribe link** (opcional)

### Error "API Key invalid"

1. Asegúrate que la key comience con `re_`
2. Verifica que no haya espacios al inicio/final
3. Revoca y genera una nueva key

### Rate limit exceeded

1. Free tier: 100 emails/día
2. Espera 24 horas o upgrade a Pro
3. Implementa queue system para muchos emails

## 📞 Soporte

- **Documentación:** [https://resend.com/docs](https://resend.com/docs)
- **Discord:** [https://resend.com/discord](https://resend.com/discord)
- **Email:** support@resend.com

## 🎯 Siguiente paso

Una vez configurado Resend, continúa con el resto de la configuración en `SETUP_FORMS.md`:
- Google Maps API
- reCAPTCHA v3
- Supabase database

---

**¡Listo!** 🎉 Ahora tienes emails profesionales con Resend.
