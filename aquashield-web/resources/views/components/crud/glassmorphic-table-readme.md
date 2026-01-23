# Componente de Tabla Glassmórfica

## Descripción

Este componente proporciona una tabla con efectos visuales glassmórficos para el sistema CRUD de Laravel. Incluye efectos como bordes con gradiente animado, fondos semitransparentes, efectos de hover en las filas, y animaciones en los encabezados.

## Características

- Bordes con gradiente animado
- Efectos de brillo y sombra
- Animaciones en los encabezados de columnas
- Efectos de hover en las filas (escala, indicador visual)
- Compatibilidad con el sistema CRUD existente
- Soporte para ordenación y paginación
- Diseño responsive

## Instalación

1. Asegúrate de que los siguientes archivos estén presentes en tu proyecto:
   - `resources/views/components/crud/glassmorphic-table.blade.php`
   - `public/css/glassmorphic-table.css`
   - `public/js/glassmorphic-table.js`

2. Verifica que las animaciones personalizadas estén definidas en `tailwind.config.js`

## Uso

### Uso Básico

Puedes usar el componente de la misma manera que usarías el componente `x-crud.advanced-table`:

```blade
<x-crud.glassmorphic-table 
    :id="$tableId" 
    :columns="$tableColumns" 
    :manager-name="$managerName" 
/>
```

### Propiedades

El componente acepta las siguientes propiedades:

- `id` (string): ID del elemento de tabla (por defecto: 'crud-glassmorphic-table')
- `columns` (array): Array de columnas con 'label' y 'field'
- `managerName` (string): Nombre del gestor CRUD (por defecto: 'crudManager')
- `loadingText` (string): Texto a mostrar durante la carga (por defecto: 'Loading...')
- `noDataText` (string): Texto a mostrar cuando no hay datos (por defecto: 'No records found')
- `responsive` (boolean): Si la tabla debe ser responsive (por defecto: true)
- `sortable` (boolean): Si las columnas deben ser ordenables (por defecto: true)
- `darkMode` (boolean): Si la tabla debe usar el tema oscuro (por defecto: true)

### Personalización

Puedes personalizar los colores y efectos modificando los archivos CSS o las clases de Tailwind en el componente Blade.

#### Ejemplo: Cambiar los colores del borde con gradiente

En `glassmorphic-table.blade.php`, modifica la clase `bg-gradient-to-r` con tus propios colores:

```blade
<div class="absolute inset-0 rounded-[5px] bg-gradient-to-r from-blue-400 via-purple-500 to-pink-400 bg-[length:300%_300%] animate-gradient-border opacity-80"></div>
```

## Integración con el Sistema CRUD

El componente está diseñado para integrarse perfectamente con el sistema CRUD existente. Funciona con:

- Ordenación de columnas
- Paginación
- Filtrado y búsqueda
- Eventos de clic en filas

## Consideraciones de Rendimiento

- Las animaciones pueden afectar el rendimiento en dispositivos de gama baja
- Para tablas con muchos datos, considera usar paginación con un número limitado de elementos por página
- En dispositivos móviles, algunas animaciones (como el escalado de filas) se desactivan automáticamente

## Accesibilidad

Para mejorar la accesibilidad, puedes agregar una opción para desactivar las animaciones. Esto se puede implementar agregando una preferencia de usuario o un toggle en la interfaz.

## Solución de Problemas

### Las animaciones no funcionan

- Verifica que las animaciones estén definidas correctamente en `tailwind.config.js`
- Asegúrate de que los archivos CSS y JS estén siendo cargados correctamente
- Comprueba la consola del navegador para errores JavaScript

### Problemas de rendimiento

- Reduce la complejidad de las animaciones
- Disminuye el número de elementos animados
- Considera usar `will-change` para optimizar el renderizado