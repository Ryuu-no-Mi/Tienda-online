# Diagramas del Proyecto - Tienda Online de Zapatillas

---

## 1. Diagrama de Arquitectura de 3 Capas

```
┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃                    CAPA CLIENTE (Presentación)                 ┃
┃                     HTML5, CSS3, JavaScript                    ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃                                                                 ┃
┃  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         ┃
┃  │   Login      │  │   Admin      │  │   Cliente    │         ┃
┃  │ Registro     │  │   Panel      │  │  Catálogo    │         ┃
┃  │ index.php    │  │ dashboard.php│  │ catalogo.php │         ┃
┃  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘         ┃
┃         │                 │                 │                  ┃
┃         └─────────────────┼─────────────────┘                  ┃
┃                           │                                     ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━╋━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
                            │
                       HTTP REQUEST
                            │
┏━━━━━━━━━━━━━━━━━━━━━━━━━━╋━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃                           ▼                                     ┃
┃     CAPA LÓGICA DE NEGOCIO (Business Logic)                   ┃
┃              PHP - Validaciones y Reglas                       ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃                                                                 ┃
┃  ┌────────────────────┐  ┌────────────────────┐               ┃
┃  │  UsuarioManager    │  │  ProductoManager   │               ┃
┃  ├────────────────────┤  ├────────────────────┤               ┃
┃  │ + registrar()      │  │ + crear()          │               ┃
┃  │ + login()          │  │ + actualizar()     │               ┃
┃  │ + actualizar()     │  │ + eliminar()       │               ┃
┃  │ + eliminar()       │  │ + obtenerTodos()   │               ┃
┃  └────────┬───────────┘  └────────┬───────────┘               ┃
┃           │                       │                            ┃
┃           │         ┌─────────────┼─────────────┐             ┃
┃           │         ▼             ▼             ▼             ┃
┃           │    ┌─────────────────────────────────────┐        ┃
┃           │    │      CarritoManager                 │        ┃
┃           │    ├─────────────────────────────────────┤        ┃
┃           │    │ + agregarProducto()                 │        ┃
┃           │    │ + actualizarCantidad()              │        ┃
┃           │    │ + confirmarCompra()                 │        ┃
┃           │    │ + calcularTotal()                   │        ┃
┃           │    └────────────┬────────────────────────┘        ┃
┃           │                 │                                  ┃
┗━━━━━━━━━━━╋━━━━━━━━━━━━━━━╋━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
             │                │
        DATABASE QUERIES
             │                │
┏━━━━━━━━━━━╋━━━━━━━━━━━━━━━╋━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃            ▼                ▼                                 ┃
┃     CAPA DE DATOS (Data Access Layer)                         ┃
┃          PHP + MySQL - Acceso a Base de Datos                 ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃                                                                 ┃
┃  ┌────────────────┐  ┌────────────────┐  ┌──────────────┐    ┃
┃  │  UsuarioDAO    │  │  ProductoDAO   │  │  PedidoDAO   │    ┃
┃  ├────────────────┤  ├────────────────┤  ├──────────────┤    ┃
┃  │ + obtenerById()│  │ + obtenerById()│  │ + crear()    │    ┃
┃  │ + obtenerTodos│  │ + obtenerTodos │  │ + obtenerPor │    ┃
┃  │ + crear()     │  │ + crear()      │  │   Usuario()  │    ┃
┃  │ + actualizar()│  │ + actualizar() │  │ + obtenerDet │    ┃
┃  │ + eliminar()  │  │ + eliminar()   │  │   alles()    │    ┃
┃  │ + existe()    │  │ + buscarPorMa  │  └──────────────┘    ┃
┃  │ + existeEmail │  │   rca()        │                       ┃
┃  └─────┬─────────┘  └─────┬─────────┘                        ┃
┃        │                  │                                   ┃
┃        └──────────┬───────┘                                   ┃
┃                   │                                           ┃
┃              config.php                                       ┃
┃          (Conexión a BD)                                      ┃
┃                   │                                           ┃
┃                   ▼                                           ┃
┃        ┌─────────────────────┐                               ┃
┃        │   MySQLi Connection │                               ┃
┃        └──────────┬──────────┘                               ┃
┗━━━━━━━━━━━━━━━━━━╋━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
                    │
                    ▼
          ┌─────────────────────────┐
          │   MySQL Database        │
          │  tienda_zapatillas      │
          ├─────────────────────────┤
          │ ├─ usuarios             │
          │ ├─ productos            │
          │ ├─ pedidos              │
          │ └─ detallepedidos       │
          └─────────────────────────┘
```

---

## 2. Diagrama de Navegación - Panel Administrador

```
                    ┌──────────────┐
                    │  INDEX.PHP   │
                    │  Login Form  │
                    └───────┬──────┘
                            │
                         LOGIN
                            │
                    ┌───────▼──────────┐
                    │  DASHBOARD.PHP   │◄──────┐
                    │  Panel Admin     │       │
                    └───┬──┬──┬────────┘       │
                        │  │  │               │
            ┌───────────┘  │  │               │
            │              │  │               │
       ┌────▼────┐    ┌────▼───┐    ┌────▼────┐
       │USUARIOS │    │PRODUCTOS│   │ PEDIDOS │
       ├─────────┤    ├────────┤    ├────────┤
       │ Crear   │    │ Crear  │    │ Ver    │
       │ Editar  │    │ Editar │    │ Estado │
       │ Eliminar│    │Eliminar│    │Cambiar │
       └────┬────┘    └───┬────┘    └───┬────┘
            │             │             │
            └─────────────┼─────────────┘
                          │
                      LOGOUT
                          │
                    ┌─────▼──────┐
                    │  INDEX.PHP  │
                    │  (Sesión    │
                    │  destruida) │
                    └─────────────┘
```

---

## 3. Diagrama de Navegación - Cliente

```
                    ┌──────────────┐
                    │  INDEX.PHP   │
                    │ Login/Registro
                    └───────┬──────┘
                            │
                    REGISTRO│LOGIN
                            │
                    ┌───────▼──────────────┐
                    │  CATALOGO.PHP        │
                    │  Ver Productos       │
                    └───────┬──────────────┘
                            │
                   AGREGAR AL CARRITO
                            │
                    ┌───────▼──────────┐
                    │  CARRITO.PHP     │
                    │  Productos       │
                    │  Actualizar      │
                    └─────┬──┬─────────┘
                          │  │
                          │  └─────────┐
                          │            │
                    CONFIRMAR       CONTINUAR
                      COMPRA         COMPRANDO
                          │            │
                          ▼            └───┐
                    ┌──────────────┐       │
                    │ CONFIRMACION │       │
                    │ Resumen      │       │
                    └──────┬───────┘       │
                           │              │
                           │              │
                    VER HISTORIAL         │
                           │              │
                    ┌──────▼───────┐      │
                    │ HISTORIAL.PHP│      │
                    │ Mis Pedidos   │      │
                    │ Ver Detalles  │      │
                    └───────┬──────┘      │
                            │            │
                        LOGOUT           │
                            │            │
                    ┌────────▼──────────┐│
                    │  INDEX.PHP        ││
                    │  (Sesión          ││
                    │  destruida)       ││
                    └───────────────────┘│
                                         │
                                         │
                                    (Vuelve)
                                         │
                                         └────────────┐
                                                      │
                                                      ▼
                                        Se repite el ciclo...
```

---

## 4. Diagrama Entidad-Relación (E-R)

```
┌──────────────────────────────┐
│          USUARIOS            │
├──────────────────────────────┤
│ PK  id (INT)                 │
│     usuario (VARCHAR 50)     │◄──┐
│     email (VARCHAR 100)      │   │
│     contraseña (VARCHAR 255) │   │
│     telefono (VARCHAR 20)    │   │
│     direccion (VARCHAR 200)  │   │ 1
│     rol (ENUM)               │   │
│     fecha_registro (DATETIME)│   │
│     activo (TINYINT)         │   │
└──────────────────────────────┘   │
                                    │
                                    │
                                    │ (1:N)
                                    │
                     ┌──────────────┴──────────┐
                     │                         │
         ┌───────────▼──────────────┐         │
         │      PEDIDOS             │         │
         ├──────────────────────────┤         │
         │ PK  id (INT)             │         │
         │ FK  usuario_id ───────────┼─────────┘
         │     total (DECIMAL)      │
         │     estado (ENUM)        │
         │     fecha_pedido (DATETIME)
         │                          │
         └───────────┬──────────────┘
                     │
                     │ (1:N)
                     │
         ┌───────────▼──────────────────────┐
         │    DETALLEPEDIDOS                │
         ├────────────────────────────────┤
         │ PK  id (INT)                    │
         │ FK  pedido_id ─────────┐        │
         │ FK  producto_id ───────┼────┐   │
         │     cantidad (INT)     │    │   │
         │     precio_unitario    │    │   │
         │     subtotal (DECIMAL) │    │   │
         └────────────────────────┘    │   │
                                       │   │
                                       │   │ (1:N)
                                       │   │
                                       │   │
         ┌─────────────────────────────┘   │
         │                                 │
         │                     ┌───────────▼──────────────┐
         │                     │     PRODUCTOS           │
         │                     ├──────────────────────────┤
         │                     │ PK  id (INT)             │
         │                     │     nombre (VARCHAR 100) │
         │                     │     marca (VARCHAR 50)   │
         │                     │     descripcion (TEXT)   │
         │                     │     precio (DECIMAL)     │
         │                     │     stock (INT)          │
         │                     │     imagen (VARCHAR)     │
         │                     │     fecha_creacion (DATE)│
         │                     │     activo (TINYINT)     │
         │                     └────────────────────────┘
         │
         └─────────────────────────────────────────────────

RELACIONES:
- USUARIO (1) ───────→ (N) PEDIDO    [usuario_id → id]
- PEDIDO (1) ────────→ (N) DETALLEPEDIDOS [pedido_id → id]
- PRODUCTO (1) ──────→ (N) DETALLEPEDIDOS [producto_id → id]

CARDINALIDADES:
- 1:N (Uno a Muchos): Un usuario tiene muchos pedidos
- 1:N (Uno a Muchos): Un pedido tiene muchos detalles
- 1:N (Uno a Muchos): Un producto aparece en muchos detalles
```

---

## 5. Diagrama de Flujo - Registrar Usuario

```
                        ┌─────────────────┐
                        │ INICIO - SUBMIT │
                        │ FORMULARIO      │
                        └────────┬────────┘
                                 │
                    ┌────────────▼──────────────┐
                    │  VALIDAR FORMULARIO       │
                    │  UsuarioManager.registrar │
                    └────────┬────────┬────────┘
                             │        │
                        OK   │        │  ERROR
                             │        │
                    ┌────────▼──┐  ┌─▼──────────────────┐
                    │  VALIDAR  │  │ MOSTRAR ERRORES    │
                    │ USUARIO   │  │ - Usuario existe   │
                    │ - Min 3   │  │ - Email inválido   │
                    │   chars   │  │ - Contraseñas no   │
                    │ - Único   │  │   coinciden        │
                    └────┬──────┘  │ - Contraseña < 6   │
                         │        │ - Email existe     │
                         ▼        └────────────────────┘
                    ┌────────────┐      │
                    │ VALIDAR    │      │
                    │ EMAIL      │      │
                    │ - RFC 5322 │      │
                    └─┬──────────┘      │
                      │                 │
                      ▼                 │
                    ┌────────────┐      │
                    │ VALIDAR    │      │
                    │ PASSWORD   │      │
                    │ - Coinciden│      │
                    │ - Min 6    │      │
                    └─┬──────────┘      │
                      │                 │
                      ▼                 │
                    ┌──────────────────┐│
                    │ ENCRIPTAR PASS   ││
                    │ PASSWORD_BCRYPT  ││
                    └────┬─────────────┘│
                         │              │
                         ▼              │
                    ┌──────────────┐   │
                    │ CREAR EN BD  │   │
                    │ UsuarioDAO   │   │
                    │ .crear()     │   │
                    └────┬─────────┘   │
                         │             │
                    ┌────▼─────────────▼───┐
                    │  REDIRIGIR CON ESTADO│
                    └──────────────────────┘
```

---

## 6. Diagrama de Flujo - Comprar Producto

```
┌──────────────────────────────┐
│ 1. VISUALIZAR CATÁLOGO       │
│    ProductoManager.obtenerTodos()
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ 2. SELECCIONAR PRODUCTO      │
│    + Cantidad                │
│    + Validar Stock           │
└────────┬─────────────────────┘
         │
    ERROR│
         │◄────────────────┐
         │              INSUFICIENTE
         │                 │
         ▼                 │
┌──────────────────────────────┐
│ 3. AGREGAR AL CARRITO        │
│    CarritoManager            │
│    .agregarProducto()        │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ 4. $_SESSION['carrito'] +=   │
│    {producto}                │
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ 5. VER CARRITO               │
│    Mostrar productos         │
│    Mostrar total             │
└────────┬─────────────────────┘
         │
    ┌────┴─────────────────┐
    │                      │
    ▼                      ▼
┌──────────────┐    ┌──────────────────┐
│CONTINUAR     │    │ CONFIRMAR COMPRA │
│COMPRANDO     │    └────────┬─────────┘
└────┬─────────┘             │
     │                       ▼
     │              ┌──────────────────┐
     │              │ PedidoDAO        │
     │              │ .crear()         │
     │              │ (Crear pedido)   │
     │              └────────┬─────────┘
     │                       │
     │                       ▼
     │              ┌──────────────────┐
     │              │ PedidoDAO        │
     │              │ .crearDetalle()  │
     │              │ (X productos)    │
     │              └────────┬─────────┘
     │                       │
     │                       ▼
     │              ┌──────────────────┐
     │              │ $_SESSION        │
     │              │ ['carrito'] = [] │
     │              │ (Vaciar)         │
     │              └────────┬─────────┘
     │                       │
     │                       ▼
     │              ┌──────────────────┐
     │              │ CONFIRMACION.PHP │
     │              │ (Mostrar resumen)│
     │              └────────┬─────────┘
     │                       │
     └───────────┬───────────┘
                 │
                 ▼
        ┌──────────────────┐
        │ HISTORIAL.PHP    │
        │ Ver todos pedidos│
        └──────────────────┘
```

---

## 7. Matriz de Roles y Permisos

```
┌────────────────────────┬──────────┬─────────┐
│ FUNCIONALIDAD          │ ADMIN    │ CLIENTE │
├────────────────────────┼──────────┼─────────┤
│ Ver Catálogo           │    ✓     │    ✓    │
│ Crear Usuario          │    ✓     │    ✗    │
│ Editar Usuario         │    ✓     │    ✗    │
│ Eliminar Usuario       │    ✓     │    ✗    │
│ Listar Usuarios        │    ✓     │    ✗    │
│ Crear Producto         │    ✓     │    ✗    │
│ Editar Producto        │    ✓     │    ✗    │
│ Eliminar Producto      │    ✓     │    ✗    │
│ Listar Productos Admin │    ✓     │    ✗    │
│ Agregar al Carrito     │    ✗     │    ✓    │
│ Ver Carrito            │    ✗     │    ✓    │
│ Confirmar Compra       │    ✗     │    ✓    │
│ Ver Mis Pedidos        │    ✗     │    ✓    │
│ Ver Todos Pedidos      │    ✓     │    ✗    │
│ Cambiar Estado Pedido  │    ✓     │    ✗    │
│ Cerrar Sesión          │    ✓     │    ✓    │
└────────────────────────┴──────────┴─────────┘
```

---

## 8. Diagrama de Sesiones PHP

```
SESSION VARIABLES PARA USUARIO AUTENTICADO:

$_SESSION = [
    'usuario_id'  => 1,                    // ID del usuario
    'usuario'     => 'admin',              // Nombre de usuario
    'email'       => 'admin@tienda.com',  // Email
    'rol'         => 'administrador',      // Rol: 'admin' o 'cliente'
    
    // SOLO PARA CLIENTES:
    'carrito'     => [
        'producto_id' => [
            'id'        => 1,
            'nombre'    => 'Nike Air Max 90',
            'marca'     => 'Nike',
            'precio'    => 89.99,
            'cantidad'  => 2,
            'imagen'    => 'nike_airmax90.jpg'
        ]
    ]
]
```

---

**Leyenda de Símbolos:**
- PK = Primary Key (Clave Primaria)
- FK = Foreign Key (Clave Foránea)
- 1:N = Relación Uno a Muchos
- ✓ = Permitido
- ✗ = No permitido
