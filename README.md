# Respuesta

**Proyecto Final de Ciclo (PFC)** — Aplicación web fullstack construida con **Symfony 6.2** y **PHP 8.1**.

Portal de gestión de contenido visual con panel de administración propio: permite subir fotografías con una estructura jerárquica definida y genera automáticamente rutas públicas y menú de navegación a partir de esa estructura. Una especie de micro-CMS orientado a galerías fotográficas con navegación dinámica.

## Funcionalidad principal

- **Panel de administración** con autenticación (Symfony Security)
- **Subida de fotos** con estructura jerárquica configurable
- **Generación automática de rutas** a partir de la estructura de contenido subido
- **Menú dinámico** generado desde la jerarquía de galerías
- Gestión de entidades con **Doctrine ORM** y migrations
- Plantillas con **Twig**

## Stack

- **PHP 8.1** + **Symfony 6.2**
- **Doctrine ORM** + **Doctrine Migrations**
- **Twig** — motor de plantillas
- **Symfony Security** — autenticación y control de acceso
- **Symfony Form** — gestión de formularios con validación
- **Symfony Mailer** — notificaciones
- **PHPUnit** — tests unitarios e integración
- **Docker** — entorno de desarrollo local

## Estructura

```
src/            # Lógica de la aplicación (controllers, entities, services…)
templates/      # Plantillas Twig
migrations/     # Migraciones de base de datos
tests/          # Tests PHPUnit
config/         # Configuración de Symfony
public/         # Entry point y assets estáticos
```

## Entorno local con Docker

```bash
docker compose up -d
```

Copia `.env` y ajusta los valores de base de datos:

```bash
cp .env .env.local
```

Instala dependencias y ejecuta migrations:

```bash
composer install
php bin/console doctrine:migrations:migrate
```

Servidor de desarrollo:

```bash
symfony server:start
```

## Tests

```bash
php bin/phpunit
```

Para tests con base de datos separada:

```bash
APP_ENV=test php bin/phpunit
```

> El archivo `.env.test` ya está configurado con la conexión de test.
