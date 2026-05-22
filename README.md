# CañonCosmoStore 

Una aplicación full-stack para gestionar una tienda de artículos de anime, manga, TCGs y videojuegos. Combina un backend robusto en Symfony 7 con un frontend responsivo en Angular 21.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![License](https://img.shields.io/badge/license-Proprietary-red)
![Status](https://img.shields.io/badge/status-Active-green)

## Tabla de Contenidos

- [Características](#características)
- [Arquitectura](#arquitectura-es)
- [Requisitos Previos](#requisitos-previos)
- [Instalación](#instalación-es)
- [Uso](#uso-es)
- [Estructura del Proyecto](#estructura-del-proyecto-es)
- [Backend (Symfony)](#backend-symfony-es)
- [Frontend (Angular)](#frontend-angular-es)
- [API Endpoints](#api-endpoints-es)
- [Testing](#testing-es)
- [Deployment](#deployment-es)
- [Contribución](#contribución)
- [Licencia](#licencia)

## Características

### Backend
- **API RESTful** completa con Symfony 7.2
- **Autenticación JWT** segura con Lexik JWT Authentication
- **Gestión de Base de Datos** con Doctrine ORM
- **Validación de Datos** con Symfony Validator
- **CORS** configurado para comunicación segura
- **Migraciones de Base de Datos** automáticas
- **Fixtures** para datos de prueba

### Frontend
- **Angular 21** framework moderno y reactivo
- **Tailwind CSS** para estilos modernos y responsive
- **TypeScript** para seguridad de tipos
- **RxJS** para manejo reactivo de datos
- **Vitest** para testing unitario
- **Prettier** para consistencia de código

<a name="arquitectura-es"></a>
## Arquitectura

```
Frikistore-API/
├── 📁 angular-frontend/          # Aplicación cliente Angular
│   ├── src/
│   │   ├── app/                  # Componentes y servicios
│   │   ├── assets/               # Imágenes y recursos
│   │   └── styles/               # Estilos globales
│   ├── package.json
│   └── angular.json
│
├── 📁 symfony-backend/           # API REST Symfony
│   ├── src/
│   │   ├── Controller/           # Controladores API
│   │   ├── Entity/               # Entidades Doctrine
│   │   ├── Service/              # Lógica de negocio
│   │   ├── Security/             # Autenticación y autorización
│   │   └── Fixture/              # Datos de prueba
│   ├── migrations/               # Migraciones BD
│   ├── config/                   # Configuración
│   ├── composer.json
│   └── symfony.lock
│
├── docker-compose.yaml           # Orquestación de servicios
├── .env                          # Variables de entorno
└── README.md                     # Este archivo
```

## Requisitos Previos

### Globales
- **Docker** y **Docker Compose** (recomendado)
- **Git** para control de versiones

### Backend (Alternativa sin Docker)
- **PHP** ≥ 8.2
- **Composer** (gestor de dependencias PHP)
- **Base de Datos**: MySQL 8.0+ o PostgreSQL 13+

### Frontend (Alternativa sin Docker)
- **Node.js** 18+ o 20+
- **npm** 9+ o **yarn**
- **Angular CLI** v21.2.0

<a name="instalación-es"></a>
## Instalación

### Opción 1: Con Docker (Recomendado)

```bash
# Clonar el repositorio
git clone <tu-url-repositorio>
cd frikistore-api

# Levantar servicios con Docker Compose
docker-compose up -d

# Instalar dependencias del backend
docker-compose exec symfony-backend composer install

# Ejecutar migraciones de BD
docker-compose exec symfony-backend php bin/console doctrine:migrations:migrate

# Instalar dependencias del frontend
docker-compose exec angular-frontend npm install

# Acceder a las aplicaciones
# Frontend: http://localhost:4200
# Backend API: http://localhost:8000
```

### Opción 2: Sin Docker (Local)

#### Backend Symfony

```bash
# Navegar al directorio del backend
cd symfony-backend

# Instalar dependencias
composer install

# Configurar variables de entorno
cp .env .env.local
# Editar .env.local con tu configuración de BD

# Crear base de datos
php bin/console doctrine:database:create

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate

# Cargar datos de prueba (opcional)
php bin/console doctrine:fixtures:load

# Iniciar servidor de desarrollo
symfony server:start
# Backend disponible en http://localhost:8000
```

#### Frontend Angular

```bash
# Navegar al directorio del frontend
cd angular-frontend

# Instalar dependencias
npm install

# Iniciar servidor de desarrollo
npm start
# Frontend disponible en http://localhost:4200

# La aplicación recarga automáticamente en cambios
```

<a name="uso-es"></a>
## 💻 Uso

### Desarrollo Frontend

```bash
cd angular-frontend

# Iniciar servidor con hot-reload
npm start

# Compilar para producción
npm run build

# Ejecutar tests unitarios
npm test

# Construir en modo observador
npm run watch
```

### Desarrollo Backend

```bash
cd symfony-backend

# Iniciar servidor Symfony
symfony server:start

# Ver logs en tiempo real
symfony server:log

# Ejecutar comandos Symfony
symfony console list

# Crear una nueva migración
symfony console make:migration

# Crear una nueva entidad
symfony console make:entity
```

### Variables de Entorno

#### Backend (`.env`)

```env
# Base de Datos
DATABASE_URL="mysql://user:password@localhost:3306/frikistore"

# Seguridad JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem

# CORS
CORS_ALLOW_ORIGIN=^https?://localhost(:[0-9]+)?$

# Ambiente
APP_ENV=dev
APP_DEBUG=true
```

#### Frontend (`.env` o `environment.ts`)

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api',
};
```

<a name="estructura-del-proyecto-es"></a>
## 📁 Estructura del Proyecto

### Backend Symfony

```
symfony-backend/
├── src/
│   ├── Controller/          # Controladores API (endpoints)
│   ├── Entity/              # Modelos de datos (Doctrine)
│   ├── Repository/          # Acceso a datos
│   ├── Service/             # Lógica de negocio
│   ├── Security/            # Autenticación y autorización
│   ├── EventListener/       # Listeners de eventos
│   └── Fixture/             # Datos de ejemplo
├── config/
│   ├── routes/              # Rutas API
│   ├── packages/            # Configuración de bundles
│   └── jwt/                 # Certificados JWT
├── migrations/              # Migraciones de BD
├── public/                  # Punto de entrada (index.php)
├── templates/               # Vistas (si aplica)
├── tests/                   # Tests del backend
└── var/                     # Cache y logs
```

### Frontend Angular

```
angular-frontend/
├── src/
│   ├── app/
│   │   ├── components/      # Componentes reutilizables
│   │   ├── pages/           # Componentes de páginas
│   │   ├── services/        # Servicios HTTP y lógica
│   │   ├── models/          # Interfaces y tipos
│   │   ├── guards/          # Guardias de rutas
│   │   └── app.module.ts    # Módulo principal
│   ├── assets/              # Imágenes y recursos estáticos
│   ├── styles/              # Estilos globales
│   ├── environment.ts       # Configuración por ambiente
│   ├── main.ts              # Punto de entrada
│   └── index.html           # HTML principal
├── public/                  # Recursos públicos
├── tests/                   # Tests unitarios y e2e
└── dist/                    # Build de producción
```

<a name="backend-symfony-es"></a>
## 🔌 Backend (Symfony)

### Autenticación JWT

El backend utiliza JWT (JSON Web Tokens) para autenticación:

```bash
# Generar claves JWT (si no existen)
cd symfony-backend
php bin/console lexik:jwt:generate-keypair
```

**Flujo de autenticación:**
1. Usuario envía credenciales al endpoint `/api/login`
2. Backend valida y retorna un JWT token
3. Cliente incluye el token en header `Authorization: Bearer <token>`
4. Backend valida el token en cada request

### Entidades Principales

- **User**: Usuarios del sistema
- **Product**: Productos de la tienda
- **Category**: Categorías de productos
- **Order**: Órdenes de compra
- **OrderItem**: Items dentro de una orden

<a name="api-endpoints-es"></a>
### Endpoints API Principales

```
POST   /api/login                    # Login
POST   /api/register                 # Registro
GET    /api/products                 # Listar productos
POST   /api/products                 # Crear producto (admin)
GET    /api/products/{id}            # Detalle del producto
PUT    /api/products/{id}            # Editar producto (admin)
DELETE /api/products/{id}            # Eliminar producto (admin)
GET    /api/categories               # Listar categorías
POST   /api/orders                   # Crear orden
GET    /api/orders/{id}              # Detalle de orden
GET    /api/users/profile            # Perfil del usuario autenticado
```

<a name="frontend-angular-es"></a>
## Frontend (Angular)

### Módulos Principales

- **AppModule**: Módulo raíz
- **AuthModule**: Autenticación
- **ProductModule**: Gestión de productos
- **CartModule**: Carrito de compras
- **OrderModule**: Gestión de órdenes

### Rutas Principales

```
/                    # Página de inicio
/products            # Catálogo de productos
/products/:id        # Detalle del producto
/cart                # Carrito de compras
/checkout            # Proceso de compra
/orders              # Mis pedidos
/profile             # Perfil de usuario
/login               # Login
/register            # Registro
/admin               # Panel administrativo
```

### Comunicación con API

```typescript
// Servicio HTTP de ejemplo
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ProductService {
  constructor(private http: HttpClient) {}

  getProducts(): Observable<Product[]> {
    return this.http.get<Product[]>('/api/products');
  }

  getProduct(id: number): Observable<Product> {
    return this.http.get<Product>(`/api/products/${id}`);
  }
}
```

<a name="testing-es"></a>
## Testing

### Backend (Symfony)

```bash
cd symfony-backend

# Ejecutar tests con PHPUnit
php bin/phpunit

# Tests de cobertura
php bin/phpunit --coverage-html=coverage
```

### Frontend (Angular)

```bash
cd angular-frontend

# Ejecutar tests unitarios con Vitest
npm test

# Tests con cobertura
npm test -- --coverage

# Tests end-to-end
npm run e2e
```

<a name="deployment-es"></a>
## Deployment

### Variables de Entorno Producción

```bash
# Backend
APP_ENV=prod
APP_DEBUG=false
DATABASE_URL=<url-produccion>
JWT_SECRET_KEY=<secret-key-produccion>

# Frontend
ng build --configuration production
```

### Docker Deployment

```bash
# Construir imágenes
docker-compose build

# Desplegar
docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml up -d

# Ver logs
docker-compose logs -f
```

### Servidor Remoto (Alternativa)

1. **Backend**:
   ```bash
   # SSH al servidor
   ssh usuario@servidor
   
   # Clonar y setup
   git clone <repositorio>
   cd frikistore-api/symfony-backend
   composer install --no-dev
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

2. **Frontend**:
   ```bash
   cd frikistore-api/angular-frontend
   npm install --production
   npm run build
   # Servir con nginx o apache
   ```

## Seguridad

- **CORS** configurado para origen específico
- **JWT** para autenticación stateless
- **Hash bcrypt** para contraseñas
- **Validación** de entrada en cliente y servidor
- **Sanitización** de datos
- **HTTPS** requerido en producción

## Convenciones de Código

### Backend (PHP/Symfony)
- PSR-12 para estilo de código
- Doctrine annotations para mapeos
- Services para inyección de dependencias
- Repositorios para acceso a datos

### Frontend (TypeScript/Angular)
- Prettier para formateo automático
- Componentes con OnPush change detection
- Servicios para lógica compartida
- Tipado fuerte con TypeScript

## Troubleshooting

### Backend
- **Error de conexión BD**: Verificar `.env` y credenciales
- **JWT keys no encontradas**: Ejecutar `lexik:jwt:generate-keypair`
- **Cache corrupto**: Limpiar con `cache:clear`

### Frontend
- **Módulos no encontrados**: Ejecutar `npm install`
- **API no responde**: Verificar que backend está corriendo y URL en `environment.ts`
- **Puertos en uso**: Cambiar puerto con `ng serve --port 4201`

## Recursos Adicionales

- [Documentación Symfony](https://symfony.com/doc)
- [Documentación Angular](https://angular.dev)
- [JWT.io - JSON Web Tokens](https://jwt.io)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)

## Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

### Estándares de Contribución

- Seguir las convenciones de código del proyecto
- Incluir tests para nuevas funcionalidades
- Documentar cambios importantes
- Hacer commits atómicos y descriptivos

## Licencia

Este proyecto es propietario y está protegido bajo licencia privada. Todos los derechos reservados.