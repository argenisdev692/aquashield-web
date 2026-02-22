const fs = require('fs');
const path = require('path');

const services = [
  { slug: 'water-damage', name: 'Water Damage Restoration', title: 'Restauración y Mitigación de Daño por Agua', desc: 'Respuesta rápida 24/7 para mitigación, secado y deshumidificación después de inundaciones, fugas de agua o roturas de tuberías.' },
  { slug: 'fire-damage', name: 'Fire Damage Remediation', title: 'Restauración por Daño de Fuego y Humo', desc: 'Servicios integrales de limpieza, desodorización y reconstrucción para propiedades afectadas por incendios.' },
  { slug: 'mold-remediation', name: 'Mold Remediation', title: 'Remediación y Eliminación de Moho', desc: 'Detección, contención y eliminación profesional de moho para proteger su salud y propiedad residencial o comercial.' },
  { slug: 'hvac-decontamination', name: 'HVAC Decontamination', title: 'Descontaminación de HVAC', desc: 'Limpieza profunda y descontaminación especializada de sistemas de aire acondicionado y ductos, crucial tras desastres.' },
  { slug: 'drying-dehumidification', name: 'Drying & Dehumidification', title: 'Secado Estructural y Deshumidificación', desc: 'Tecnología industrial avanzada para extraer humedad de paredes, pisos y estructuras, evitando daño a largo plazo y moho.' },
  { slug: 'insurance-restoration', name: 'Insurance Restoration', title: 'Restauración de Seguros y Coordinación', desc: 'Trabajamos directamente con su aseguradora. Documentamos daños, gestionamos reclamos y agilizamos la aprobación.' },
  { slug: 'large-loss', name: 'Large Loss & Commercial', title: 'Respuesta a Grandes Pérdidas', desc: 'Capacidad logística e industrial para atender desastres a gran escala en almacenes, fábricas y propiedades multifamiliares.' },
  { slug: 'emergency-board-up', name: 'Emergency Board-Up', title: 'Reparaciones de Emergencia y Board-Up', desc: 'Aseguramos su propiedad tras siniestros o vandalismo sellando puertas, ventanas y techos temporalmente.' },
  { slug: 'packout-restoration', name: 'Packout & Content Restoration', title: 'Recuperación de Contenido, Documentos y Packout', desc: 'Salvado, embalaje, almacenamiento temporal y restauración de muebles, ropa, documentos valiosos y efectos personales.' },
  { slug: 'reconstruction', name: 'Reconstruction & Remodeling', title: 'Reconstrucción y Remodelación Completa', desc: 'De la mitigación a la reconstrucción total (residencial, comercial y multifamiliar) para entregar la propiedad lista para usar.' },
  { slug: 'controlled-demolition', name: 'Controlled Demolition', title: 'Demoliciones Controladas', desc: 'Remoción segura y estratégica de material dañado o contaminado preparando la estructura para la fase de reconstrucción.' },
  { slug: 'sewage-cleanup', name: 'Sewage Cleanup', title: 'Limpieza de Aguas Negras', desc: 'Desinfección protocolar y extracción de aguas residuales y biorepelentes peligrosos garantizando un ambiente seguro.' }
];

services.forEach(s => {
  const componentPath = path.join(__dirname, 'src', 'components', `${s.slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('')}.astro`);
  const componentName = `${s.slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('')}`;
  const pagePath = path.join(__dirname, 'src', 'pages', `${s.slug}.astro`);
  
  const componentCode = `---
---

<style>
    .hero-section {
        margin-top: -2rem;
    }
</style>

<!-- Hero Section with Image Overlay -->
<div class="relative h-[500px] w-full hero-section bg-navy">
    <!-- Background Image -->
    <img src="/img/services/placeholder.webp" alt="${s.name}" class="absolute inset-0 w-full h-full object-cover opacity-40">

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-linear-to-b from-navy/80 to-navy/60"></div>

    <!-- Content -->
    <div class="relative z-10 h-full flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 drop-shadow-lg">
                ${s.title}
            </h1>
            <p class="text-lg sm:text-xl md:text-2xl text-white max-w-2xl mx-auto px-4 mb-12 drop-shadow-md">
                ${s.desc}
            </p>

            <!-- Breadcrumb Navigation -->
            <nav class="px-4 md:px-8 mt-8">
                <div class="mx-auto">
                    <ol class="flex items-center justify-center space-x-2 text-white">
                        <li>
                            <a href="/" class="hover:text-aqua transition-colors">Home</a>
                        </li>
                        <li>
                            <span class="mx-2">/</span>
                        </li>
                        <li class="text-aqua font-medium">${s.name}</li>
                    </ol>
                </div>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <!-- Emergency Contact Banner -->
    <div class="bg-aqua/10 border-l-4 border-aqua p-4 mb-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-aqua shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="ml-3 text-base sm:text-lg font-medium text-navy text-center sm:text-left">
                    For emergency restoration services, call us immediately at
                    <a href="tel:7135876423" class="font-bold hover:text-aqua whitespace-nowrap">(713) 587-6423</a>
                </p>
            </div>
            <button onclick="openInspectionModal()" class="btn-primary px-6 py-2 rounded-md whitespace-nowrap shrink-0 cursor-pointer">
                Get a Free Inspection
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start mb-8">
            <!-- Text Content Column -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-navy mb-4">
                        Professional ${s.name} Services
                    </h2>
                    <p class="text-base sm:text-lg text-gray-600">
                        At AquaShield Restoration LLC, we specialize in high-quality ${s.name.toLowerCase()} services. Our team is fully equipped and trained to handle any scale of disaster, providing you with swift, effective solutions to minimize loss and restore your property quickly.
                    </p>
                    <p class="text-base sm:text-lg text-gray-600 mt-4">
                        Customer satisfaction and safety are our highest priorities. We utilize advanced techniques and industry-leading equipment to ensure a thorough restoration process.
                    </p>
                </div>
            </div>

            <!-- Image Column -->
            <div class="relative rounded-lg overflow-hidden fade-in-section">
                <!-- PLACEHOLDER FOR IMAGE -->
                <img src="/img/services/placeholder.webp" alt="${s.name} process" class="w-full h-auto object-cover image-zoom bg-gray-200" style="min-height:300px;">
            </div>
        </div>

        <div class="space-y-6">
            <!-- Call to Action Box -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="text-lg sm:text-xl font-semibold text-navy mb-4">Why Choose Us</h3>
                <ul class="space-y-3 text-base sm:text-lg">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-aqua mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>24/7 Emergency Response</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-aqua mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>IICRC Certified Technician Specialists</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-aqua mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Direct Insurance Billing & Coordination</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-aqua mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Comprehensive Restoration from Start to Finish</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div id="schedule-inspection" class="mt-12">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-navy">Need Help? Contact Us Now!</h2>
            <p class="text-lg text-gray-600 mt-2">Don't wait. Schedule your free evaluation today or call for emergency support.</p>
        </div>
        <div class="text-center">
            <button onclick="openInspectionModal()" class="btn-primary inline-block px-8 py-3 text-lg font-bold rounded-lg cursor-pointer">Book Free Evaluation</button>
        </div>
    </div>
</main>
`;

  const pageCode = `---
import Layout from '../layouts/Layout.astro';
import Navbar from '../components/Navbar.astro';
import Footer from '../components/Footer.astro';
import ${componentName} from '../components/${componentName}.astro';
---

<Layout title="${s.name} | AquaShield Restoration LLC" description="${s.desc}">
  <Navbar />
  <main>
    <${componentName} />
  </main>
  <Footer />
</Layout>
`;

  fs.writeFileSync(componentPath, componentCode);
  fs.writeFileSync(pagePath, pageCode);
});
console.log('Generated successfully!');
