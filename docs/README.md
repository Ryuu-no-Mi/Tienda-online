# Tienda Online de Zapatillas Deportivas

## Documentación Técnica Completa

---

## 1. Introducción

Este proyecto es una aplicación web académica que implementa una **tienda online de zapatillas deportivas** usando arquitectura de 3 capas (MVC simplificado). El proyecto está desarrollado en **PHP**, **MySQL**, **HTML5** y **CSS3**.

### Objetivos:
- Gestión de usuarios (login/registro)
- Gestión de productos (CRUD)
- Carrito de compras
- Confirmación de pedidos
- Panel de administrador

---

## 2. Arquitectura de 3 Capas

```
┌─────────────────────────────────────────────────┐
│         CAPA CLIENTE (Presentación)             │
│  HTML5, CSS3, JavaScript, Formularios           │
│  ├─ index.php (Login/Registro)                  │
│  ├─ admin/ (Panel Admin)                        │
│  └─ public/ (Vistas Cliente)                    │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────┴──────────────────────────────┐
│      CAPA LÓGICA DE NEGOCIO (Business)          │
│  PHP - Validaciones y Reglas de Negocio         │
│  ├─ UsuarioManager.php                          │
│  ├─ ProductoManager.php                         │
│  └─ CarritoManager.php                          │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────┴──────────────────────────────┐
│         CAPA DE DATOS (Data Access)             │
│  PHP + MySQL - Acceso a Base de Datos           │
│  ├─ UsuarioDAO.php                              │
│  ├─ ProductoDAO.php                             │
│  ├─ PedidoDAO.php                               │
│  └─ config.php (Conexión BD)                    │
└─────────────────────────────────────────────────┘
```

---

## 3. Estructura de Carpetas del Proyecto

```
Tienda online/
├── index.php                      # Login y Registro (CAPA CLIENTE)
│
├── includes/
│   ├── config.php                # Conexión a Base de Datos
│   └── funciones.php             # Funciones auxiliares globales
│
├── business/                      # CAPA LÓGICA DE NEGOCIO
│   ├── UsuarioManager.php
│   ├── ProductoManager.php
│   └── CarritoManager.php
│
├── data/                          # CAPA DE DATOS
│   ├── UsuarioDAO.php
│   ├── ProductoDAO.php
│   └── PedidoDAO.php
│
├── admin/                         # PANEL ADMINISTRADOR
│   ├── dashboard.php
│   ├── usuarios.php
│   ├── productos.php
│   ├── pedidos.php
│   └── logout.php
│
├── public/                        # VISTAS CLIENTE
│   ├── catalogo.php
│   ├── carrito.php
│   ├── confirmacion.php
│   ├── historial.php
│   ├── get_detalles_pedido.php
│   └── logout.php
│
├── assets/
│   ├── css/
│   │   └── styles.css            # (Estilos globales, opcional)
│   ├── js/
│   │   └── script.js             # (Scripts globales, opcional)
│   └── images/
│
└── docs/
    ├── database.sql               # Script SQL de creación
    └── README.md                  # Esta documentación
```

---

## 4. Modelo Entidad-Relación (E-R)

### 4.1 Entidades y Atributos

#### **USUARIO**
```
Atributos:
- id (PK, INT, AUTO_INCREMENT)
- usuario (VARCHAR 50, UNIQUE)
- email (VARCHAR 100, UNIQUE)
- contraseña (VARCHAR 255)
- telefono (VARCHAR 20, NULL)
- direccion (VARCHAR 200, NULL)
- rol (ENUM 'cliente', 'administrador')
- fecha_registro (DATETIME)
- activo (TINYINT 0/1)
```

#### **PRODUCTO**
```
Atributos:
- id (PK, INT, AUTO_INCREMENT)
- nombre (VARCHAR 100)
- marca (VARCHAR 50)
- descripcion (TEXT, NULL)
- precio (DECIMAL 10,2)
- stock (INT)
- imagen (VARCHAR 255, NULL)
- fecha_creacion (DATETIME)
- activo (TINYINT 0/1)
```

#### **PEDIDO**
```
Atributos:
- id (PK, INT, AUTO_INCREMENT)
- usuario_id (FK → USUARIO.id)
- total (DECIMAL 10,2)
- estado (ENUM 'pendiente', 'confirmado', 'entregado', 'cancelado')
- fecha_pedido (DATETIME)
```

#### **DETALLEPEDIDO**
```
Atributos:
- id (PK, INT, AUTO_INCREMENT)
- pedido_id (FK → PEDIDO.id)
- producto_id (FK → PRODUCTO.id)
- cantidad (INT)
- precio_unitario (DECIMAL 10,2)
- subtotal (DECIMAL 10,2)
```

### 4.2 Relaciones

```
USUARIO (1) ──── (N) PEDIDO
  │
  └─ Un usuario puede tener múltiples pedidos
  └─ Un pedido pertenece a un usuario (relación obligatoria)

PEDIDO (1) ──── (N) DETALLEPEDIDO
  │
  └─ Un pedido contiene múltiples detalles
  └─ Un detalle pertenece a un pedido (relación obligatoria)

PRODUCTO (1) ──── (N) DETALLEPEDIDO
  │
  └─ Un producto puede estar en múltiples detalles
  └─ Un detalle hace referencia a un producto (relación obligatoria)
```

### 4.3 Cardinalidades

| Relación | Cardinalidad | Descripción |
|----------|--------------|-------------|
| USUARIO - PEDIDO | 1:N | Un usuario compra múltiples veces |
| PEDIDO - DETALLEPEDIDO | 1:N | Un pedido contiene múltiples productos |
| PRODUCTO - DETALLEPEDIDO | 1:N | Un producto aparece en múltiples pedidos |

---

## 5. Funcionalidades Implementadas

### 5.1 RF01 - Gestión de Usuarios (Administrador)

**Acceso:** `/admin/dashboard.php?pagina=usuarios`

| Funcionalidad | Descripción |
|--------------|-------------|
| Crear usuario | Formulario con usuario, email, contraseña, rol, teléfono, dirección |
| Modificar usuario | Editar email, teléfono, dirección, rol |
| Eliminar usuario | Desactivar usuario (soft delete) |
| Listar usuarios | Tabla con todos los usuarios registrados |
| Control de acceso | Solo administrador accede al panel |

**Validaciones:**
- Usuario mínimo 3 caracteres
- Email válido (RFC 5322)
- Contraseña mínimo 6 caracteres
- Contraseñas coinciden en registro
- Usuario/Email únicos
- Control de rol (cliente/administrador)

**Sesiones utilizadas:**
```php
$_SESSION['usuario_id']
$_SESSION['usuario']
$_SESSION['rol']
$_SESSION['email']
```

### 5.2 RF02 - Gestión de Productos (Administrador)

**Acceso:** `/admin/dashboard.php?pagina=productos`

| Funcionalidad | Descripción |
|--------------|-------------|
| Crear producto | Nombre, marca, descripción, precio, stock, imagen |
| Modificar producto | Actualizar todos los campos |
| Eliminar producto | Desactivar producto |
| Listar productos | Vista admin con todos (activos e inactivos) |
| Buscar productos | Por marca (en capa negocio) |

**Campos Mínimos:**
- Nombre (obligatorio, mín 3 caracteres)
- Marca (obligatorio)
- Descripción (opcional)
- Precio (obligatorio, > 0)
- Stock (obligatorio, ≥ 0)
- Imagen (opcional, nombre archivo)

### 5.3 RF03 - Compra de Productos (Cliente)

**Acceso:** `/public/catalogo.php`

| Funcionalidad | Descripción |
|--------------|-------------|
| Visualizar catálogo | Todos los productos activos en grid |
| Agregar al carrito | Seleccionar cantidad, agregar a sesión |
| Ver carrito | Tabla con productos, cantidad, precios |
| Actualizar cantidad | Modificar cantidad antes de confirmar |
| Eliminar del carrito | Quitar productos individuales |
| Confirmar compra | Crear pedido, registrar detalles, vaciar carrito |
| Ver historial | Tabla con todos los pedidos del usuario |
| Ver detalles | Popup con productos de cada pedido |

**Carrito en Sesión:**
```php
$_SESSION['carrito'] = [
    'producto_id' => [
        'id' => 1,
        'nombre' => 'Nike Air Max',
        'marca' => 'Nike',
        'precio' => 89.99,
        'cantidad' => 2,
        'imagen' => 'nike.jpg'
    ]
]
```

---

## 6. Flujos de Usuario

### 6.1 Flujo de Administrador

```
Login (index.php)
    ↓
Panel Admin (dashboard.php)
    ├─ Gestionar Usuarios (usuarios.php)
    │  ├─ Crear usuario
    │  ├─ Editar usuario
    │  └─ Eliminar usuario
    ├─ Gestionar Productos (productos.php)
    │  ├─ Crear producto
    │  ├─ Editar producto
    │  └─ Eliminar producto
    └─ Ver Pedidos (pedidos.php)
       ├─ Listar pedidos
       └─ Cambiar estado del pedido
```

### 6.2 Flujo de Cliente

```
Login/Registro (index.php)
    ↓
Catálogo (catalogo.php)
    ├─ Ver productos disponibles
    └─ Agregar al carrito
        ↓
    Carrito (carrito.php)
        ├─ Ver productos
        ├─ Actualizar cantidad
        ├─ Eliminar productos
        └─ Confirmar compra
            ↓
        Confirmación (confirmacion.php)
            └─ Ver resumen del pedido
                ↓
            Historial (historial.php)
                └─ Ver todos los pedidos
```

---

## 7. Casos de Uso Detallados

### Caso de Uso 1: Registro de Usuario

**Actor:** Cliente no autenticado
**Precondiciones:** Acceso a index.php
**Flujo Principal:**
1. Usuario abre index.php
2. Hace clic en "Regístrate"
3. Completa formulario (usuario, email, contraseña, opcional: teléfono, dirección)
4. Valida datos:
   - Usuario único (UsuarioDAO.existe)
   - Email válido (filter_var FILTER_VALIDATE_EMAIL)
   - Contraseñas coinciden
   - Mínimo 6 caracteres contraseña
5. UsuarioManager.registrar() valida y crea usuario
6. Contraseña se encripta con PASSWORD_BCRYPT
7. Usuario registrado exitosamente

### Caso de Uso 2: Compra de Productos

**Actor:** Cliente autenticado
**Precondiciones:** Usuario logeado, acceso a /public/
**Flujo Principal:**
1. Cliente accede a catalogo.php
2. ProductoManager.obtenerTodos() carga productos activos
3. Cliente selecciona cantidad y hace clic "Agregar"
4. CarritoManager.agregarProducto() valida:
   - Stock disponible
   - Cantidad válida
5. Producto se agrega a $_SESSION['carrito']
6. Cliente accede a carrito.php
7. Actualiza cantidades según necesidad
8. Hace clic "Confirmar Compra"
9. CarritoManager.confirmarCompra():
   - PedidoDAO.crear() crea pedido
   - PedidoDAO.crearDetalle() para cada producto
   - $_SESSION['carrito'] se vacía
10. Redirige a confirmacion.php con resumen

---

## 8. Seguridad y Validaciones

### 8.1 Validaciones en Capa de Negocio

```php
// UsuarioManager
- Usuario: 3+ caracteres
- Email: Formato válido (RFC)
- Contraseña: 6+ caracteres
- Confirmación: Coincide con contraseña
- Roles: 'cliente' o 'administrador'

// ProductoManager
- Nombre: 3+ caracteres
- Precio: > 0
- Stock: >= 0

// CarritoManager
- Cantidad: > 0
- Stock suficiente para cantidad
- Carrito no vacío al confirmar
```

### 8.2 Control de Acceso

```php
// Funciones en includes/funciones.php
verificarAutenticacion()   // Requiere $_SESSION['usuario_id']
verificarAdmin()           // Requiere $_SESSION['rol'] === 'administrador'
verificarCliente()         // Requiere $_SESSION['rol'] === 'cliente'
```

### 8.3 Protecciones

- **Contraseñas:** Encriptadas con PASSWORD_BCRYPT
- **SQL Injection:** Prepared Statements (mysqli->prepare)
- **XSS:** Función escapar() con htmlspecialchars()
- **Sesiones:** Validación en cada página protegida
- **Datos Sensibles:** No se transmiten en URLs (POST para pedidos)

---

## 9. Base de Datos

### 9.1 Instalación

1. Abre tu cliente MySQL (phpMyAdmin, Workbench, CLI)
2. Copia el contenido de `docs/database.sql`
3. Ejecuta el script completo
4. Verifica creación de:
   - Base de datos `tienda_zapatillas`
   - 4 tablas (usuarios, productos, pedidos, detallepedidos)
   - Índices para optimización
   - 2 usuarios de prueba
   - 5 productos de ejemplo

### 9.2 Usuarios de Prueba

| Usuario | Contraseña | Rol | Email |
|---------|-----------|-----|-------|
| admin | admin123 | administrador | admin@tienda.com |
| cliente | cliente123 | cliente | cliente@ejemplo.com |

### 9.3 Conexión a Base de Datos

**Archivo:** `includes/config.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Cambiar según tu configuración
define('DB_NAME', 'tienda_zapatillas');
```

**Configuración típica:**
- **Host:** localhost (si es local)
- **Usuario:** root (por defecto en XAMPP/WAMP)
- **Contraseña:** vacía (por defecto en XAMPP/WAMP)
- **Base de datos:** tienda_zapatillas

---

## 10. Guía de Uso

### 10.1 Para Administradores

1. **Acceso al panel:**
   ```
   URL: http://localhost/Tienda online/index.php
   Usuario: admin
   Contraseña: admin123
   ```

2. **Gestionar usuarios:**
   - Panel → Usuarios
   - Crear, editar, eliminar usuarios
   - Asignar roles

3. **Gestionar productos:**
   - Panel → Productos
   - Crear nuevas zapatillas con precio, stock
   - Actualizar información
   - Desactivar productos

4. **Ver pedidos:**
   - Panel → Pedidos
   - Ver detalles de cada pedido
   - Cambiar estado (pendiente, confirmado, entregado, cancelado)

### 10.2 Para Clientes

1. **Registro:**
   - Abre index.php
   - Haz clic en "Regístrate"
   - Completa formulario
   - Confirma registro

2. **Compra:**
   - Login con tus credenciales
   - Accedes a catálogo automáticamente
   - Selecciona zapatillas, cantidad, "Agregar"
   - Visualiza carrito desde botón 🛒
   - Revisa productos y cantidades
   - Haz clic "Confirmar Compra"
   - Recibe confirmación

3. **Historial:**
   - Desde catálogo, puedes acceder a "Mis Pedidos"
   - Ver todos tus pedidos y detalles
   - Verificar estado de cada uno

---

## 11. Tecnologías y Dependencias

### 11.1 Requisitos del Servidor

- **PHP:** 7.4 o superior
- **MySQL:** 5.7 o superior
- **Servidor Web:** Apache (con soporte de .htaccess para URL rewriting, opcional)
- **Servidor local:** XAMPP, WAMP o LAMP

### 11.2 Funciones PHP Utilizadas

| Función | Propósito |
|---------|-----------|
| `session_start()` | Iniciar sesión |
| `password_hash()` | Encriptar contraseñas |
| `password_verify()` | Verificar contraseñas |
| `filter_var()` | Validar email |
| `htmlspecialchars()` | Escapar HTML (XSS) |
| `mysqli_prepare()` | Prepared statements |
| `mysqli_bind_param()` | Bindar parámetros |
| `mysqli_execute()` | Ejecutar sentencias |
| `mysqli_fetch_assoc()` | Obtener resultados |
| `header()` | Redirecciones |
| `date()` | Formatear fechas |
| `number_format()` | Formatear números |

---

## 12. Extensiones Futuras

### Funcionalidades Opcionales

- [ ] Búsqueda y filtrado avanzado de productos
- [ ] Sistema de comentarios/valoraciones
- [ ] Cupones y códigos de descuento
- [ ] Integración de pasarela de pago (PayPal, Stripe)
- [ ] Email de confirmación
- [ ] Dashboard de estadísticas
- [ ] Recuperación de contraseña
- [ ] Upload de imágenes de productos
- [ ] Sistema de notificaciones
- [ ] Exportación de pedidos a PDF

---

## 13. Troubleshooting

### Problemas Comunes

**Problema:** "Error de conexión a la base de datos"
- **Solución:** Verificar config.php, credenciales, servidor MySQL activo

**Problema:** "No aparecen los productos"
- **Solución:** Ejecutar script SQL completo, verificar tabla productos

**Problema:** "Sesión no se mantiene"
- **Solución:** Verificar session_start() al principio de cada archivo

**Problema:** "Acceso denegado a admin"
- **Solución:** Verificar rol en base de datos, $_SESSION['rol'] === 'administrador'

---

## 14. Conclusión

Este proyecto implementa con éxito una tienda online completa siguiendo arquitectura de 3 capas, con funcionalidades de gestión de usuarios, productos y carrito de compras. La seguridad, validación y control de acceso están debidamente implementados.

**Requisitos completados:**
✅ Arquitectura 3 capas (cliente, lógica, datos)
✅ Gestión de usuarios con roles
✅ Gestión de productos (CRUD)
✅ Carrito de compras con sesiones
✅ Base de datos relacional
✅ Validaciones en capa de negocio
✅ Interfaz funcional y clara
✅ Documentación técnica completa

---

**Autor:** Estudiante de DAW - MEDAC
**Fecha:** Enero 2026
**Versión:** 1.0
