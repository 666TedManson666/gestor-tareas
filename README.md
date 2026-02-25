# Gestor de Tareas

Aplicación web para gestionar tareas (To-Do) con sub-ítems, prioridades y estados.

**Stack:** Vue 3 · Laravel 10 · PostgreSQL · Tailwind CSS · Docker

---

## Requisitos previos

| Herramienta | Versión mínima |
|-------------|---------------|
| Docker      | 24+           |
| Docker Compose | v2+        |
| Node.js *(solo dev local)* | 20+ |
| PHP *(solo dev local)* | 8.2+ |
| Composer *(solo dev local)* | 2+ |

---

## Levantar con Docker (recomendado)

### Modo desarrollo (hot-reload Vue)

```bash
# Clonar el repositorio
git clone <url-repo>
cd gestor-tareas

# Levantar todos los servicios (postgres + backend + frontend dev)
docker compose --profile dev up --build
```

| Servicio  | URL                          |
|-----------|------------------------------|
| Frontend  | http://localhost:5173        |
| Backend   | http://localhost:8000        |
| API docs  | http://localhost:8000/api/tasks |
| PostgreSQL| localhost:5432               |

### Modo producción (nginx)

```bash
docker compose --profile prod up --build
```

| Servicio  | URL                    |
|-----------|------------------------|
| Frontend  | http://localhost       |
| Backend   | http://localhost:8000  |

---

## Levantar manualmente (sin Docker)

### Backend

```bash
cd backend

# 1. Instalar dependencias
composer install

# 2. Copiar .env
cp .env.example .env

# 3. Generar clave de aplicación
php artisan key:generate

# 4. Configurar la base de datos en .env
#    DB_HOST=127.0.0.1
#    DB_DATABASE=gestor_tareas
#    DB_USERNAME=postgres
#    DB_PASSWORD=secret

# 5. Ejecutar migraciones
php artisan migrate

# 6. (Opcional) Cargar datos de prueba
php artisan db:seed

# 7. Iniciar el servidor
php artisan serve
```

El backend estará disponible en `http://localhost:8000`.

### Frontend

```bash
cd frontend

# 1. Instalar dependencias
npm install

# 2. Copiar .env
cp .env.example .env
# Ajustar VITE_API_URL si el backend corre en otro puerto

# 3. Iniciar el servidor de desarrollo
npm run dev
```

El frontend estará disponible en `http://localhost:5173`.

---

## API REST

### Tareas

| Método | Endpoint           | Descripción          |
|--------|--------------------|----------------------|
| GET    | `/api/tasks`       | Listar todas las tareas (con ítems) |
| POST   | `/api/tasks`       | Crear una tarea      |
| PUT    | `/api/tasks/{id}`  | Actualizar una tarea |
| DELETE | `/api/tasks/{id}`  | Eliminar una tarea   |

**Body para crear/actualizar tarea:**
```json
{
  "title": "Mi tarea",
  "description": "Descripción opcional",
  "status": "pending"
}
```

### Ítems

| Método | Endpoint                              | Descripción        |
|--------|---------------------------------------|--------------------|
| POST   | `/api/tasks/{taskId}/items`           | Crear un ítem      |
| PUT    | `/api/tasks/{taskId}/items/{itemId}`  | Actualizar un ítem |
| DELETE | `/api/tasks/{taskId}/items/{itemId}`  | Eliminar un ítem   |

**Body para crear/actualizar ítem:**
```json
{
  "title": "Sub-tarea",
  "is_completed": false,
  "priority": "high"
}
```

---

## Reglas de negocio

- Una tarea solo puede marcarse como **"done"** si todos sus ítems tienen `is_completed = true`.
- Si se intenta actualizar el estado a `done` con ítems incompletos, la API retorna `422` con un mensaje descriptivo.
- En el frontend, el botón "Marcar como terminada" se **deshabilita** automáticamente y muestra un **tooltip** explicativo.

---

## Estructura del proyecto

```
gestor-tareas/
├── backend/                    # Laravel 10
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   │   ├── TaskController.php
│   │   │   │   └── ItemController.php
│   │   │   └── Requests/
│   │   │       ├── StoreTaskRequest.php
│   │   │       ├── UpdateTaskRequest.php
│   │   │       ├── StoreItemRequest.php
│   │   │       └── UpdateItemRequest.php
│   │   └── Models/
│   │       ├── Task.php
│   │       └── Item.php
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/api.php
│   └── Dockerfile
│
├── frontend/                   # Vue 3 + Tailwind
│   ├── src/
│   │   ├── api/index.js        # Capa HTTP (axios)
│   │   ├── composables/
│   │   │   └── useToast.js     # Sistema de notificaciones
│   │   └── components/
│   │       ├── TaskList.vue
│   │       ├── TaskCard.vue
│   │       ├── TaskForm.vue
│   │       ├── ItemList.vue
│   │       ├── ItemForm.vue
│   │       └── ToastNotification.vue
│   ├── Dockerfile              # Producción (nginx)
│   └── Dockerfile.dev          # Desarrollo (vite)
│
└── docker-compose.yml
```

---

## Características implementadas

### Requeridos
- [x] API REST completa (CRUD tareas + CRUD ítems)
- [x] Modelo Task con `title`, `description`, `status`, `created_at`
- [x] Modelo Item con `task_id`, `title`, `is_completed`, `priority`
- [x] Relación 1:N Task → Items
- [x] Validación con FormRequests en Laravel
- [x] Regla de negocio: no `done` si hay ítems incompletos
- [x] Vue 3 Composition API
- [x] axios para comunicación HTTP
- [x] Componentes reutilizables (TaskList, TaskCard, TaskForm, ItemList, ItemForm, ToastNotification)
- [x] Mensajes de éxito y error

### Extras implementados
- [x] Filtros por estado (todas / pendientes / en progreso / terminadas)
- [x] Migraciones y seeders
- [x] FormRequests para validación
- [x] Tailwind CSS (diseño limpio y responsivo)
- [x] Docker Compose (desarrollo y producción)
- [x] README completo
- [x] Barra de progreso por tarea
- [x] Tooltip en botón "Marcar como terminada" cuando está deshabilitado
- [x] Estadísticas en tiempo real (contador por estado)
- [x] Animaciones y transiciones suaves

---

## Decisiones técnicas

- **Composition API** sobre Options API: mejor tipado futuro, composables reutilizables.
- **`useToast` como singleton compartido**: el composable usa `ref` a nivel de módulo para compartir estado entre componentes sin Pinia/Vuex.
- **Cascade delete** en migraciones: los ítems se eliminan automáticamente cuando se elimina la tarea padre.
- **Dos Dockerfiles para frontend**: `Dockerfile` (producción con nginx + build estático) y `Dockerfile.dev` (Vite dev server con HMR).
