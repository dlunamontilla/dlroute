# Sistema de Enrutamiento

`DLRoute` es un sistema de enrutamiento diseñado para facilitar la gestión de rutas y direcciones URL en aplicaciones web.

## Características

- Definición de rutas simples y complejas.
- Manejo de diferentes métodos `HTTP` como `GET`, `POST`, `DELETE`, etc.
- Parámetros variables en las rutas.
- Uso de controladores y `callbacks` para manejar las rutas.
- Integración flexible en proyectos web.

## Instalación

Para comenzar a utilizar `DLRoute`, sigue estos pasos:

1. Instala `DLRoute` utilizando `Composer`:

   ```bash
   composer require dlunamontilla/dlroute
    ```

2. Configura el sistema de enrutamiento en tu aplicación.
3. Define las rutas utilizando el método adecuado.

### Sintaxis

Método GET:

```php
DLRoute::get(string $uri, callable|array|string $controller): void;
```

Método POST:

```php
DLRoute::post(string $uri, callable|array|string $controller): void;
```

Método PUT:

```php
DLRoute::put(string $uri, callable|array|string $controller): void;
```

Método DELETE:

```php
DLRoute::delete(string $uri, callable|array|string $controller): void;
```

### Ejemplos

Ejemplo de definición de rutas utilizando Array, cadenas de texto y `callbacks`.

Definición de rutas utilizando _array_:

```php
use DLRoute\Requests\DLRoute;

DLRoute::get('/home', [HomeController::class, 'index']);

DLRoute::init();
```

Definición de rutas utilizando cadenas de texto:

```php
DLRoute::get('/home', 'Ruta\Controller\HomeController@index');
```

Donde `Ruta\Controller\HomeController` es la ruta al controlador y `@index` es el método a ejecutar.

Definición de rutas utilizando un `callback`:

```php
DLRoute::get('/home', function(object|array $data) {
    # Lógica para la ruta definida.
});
```

## Otra cosa

Tomar en cuenta en CSS:

```css
@property --rotate {
  syntax: "<angle>";
  initial-value: 132deg;
  inherits: false;
}
```
