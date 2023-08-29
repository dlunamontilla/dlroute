# Clase ResourceManager

## Métodos de la clase | Estilos

### Método **`ResourceManager::css`**

**Sintaxis:**

```php
ResourceManager::css(string $path, bool $external = false): string
```

**Parámetros:**

- **`$path`:** Es la ruta relativa al archivo.

- **`$external`:** Si vale `false` (valor por defecto) significa que no se tomará como un archivo externo, por lo tanto, su contenido se incorpará directamente en una salida HTML con las etiquetas `<style>...</style>`. Si vale `true`, entonces, el contenido del archivo CSS será la salida, con el objeto de usarse en una ruta amigable.

Por ejemplo, las siguientes líneas:

```php
$css = ResourceManager::css('/ruta/al/archivo.css');
echo $css;
```

Devolverá una salida similar a la siguiente:

```html
<style>.clase {
    background-color: silver;
    color: black;
}</style>
```

Sin embargo, si se pasa como segundo argumento el valor `true`, por ejemplo:

```php
$css = ResourceManager::css('/ruta/al/archivo.css', true);
echo $css;
```

Devolverá la siguiente salida:

```css
.clase {
    background-color: silver;
    color: black;
}
```

Para que pueda incorporarse en una ruta amigable, como por ejemplo:

```html
<link rel="stylesheet" href="http://localhost/bundle/css/archivo?b64eaa1dbfbe0751d41b7746aad28ea34af155e3c844f51f68aeebab08989fb2" />
```

Esto está pensando para ser utilizando en el _mini-framework_ `DLUnire`.

Tome en cuenta que puede utilizar la ruta de esta forma también:

```php
$css = ResourceManager::css('ruta.al.archivo', true);
echo $css;
```

Dando el mismo resultado:

```css
.clase {
    background-color: silver;
    color: black;
}
```

Donde cada punto (`.`) será transformado automáticamente en una barra diagonal (`/`). Por otra parte, no necesita agregar la extensión al archivo, ya que será agregada automáticamente.

## Métodos de la clase ResourseManager | JavaScript

### Método **`ResourceManager::js`**

**Sintaxis:**

```php
ResourceManager::js(string $path, array $options): string
```

**Parámetros:**

- **`$path`:** Es la ruta relativa del archivo al archivo JavaScript.

- **`$options`:** Es un array asociativo que contiene las siguientes claves:

  - **`external`:** Si su valor es `false` se espera que se incorpore código JavaScript directamente en una salida HTML (comportamiento por defecto) con las etiquetas `<script...></script>` incluidas; o simplemente, si vale `true`, devuelve código JavaScript directamente sin usar las etiquetas antes mencionada con el objeto de que pueda utilizarse en una ruta amigable.
  
  - **`behavior_attributes`:** Permite indicar si el _script_ se carga en modo diferido `defer` o asíncrono `async`. Si `external` vale `true` esta opción no tendrá efecto.
  
  - **`type`:** Permite indicar si el archivo JavaScript es un módulo `type="module"` o no. NO tiene efecto si `external` es `true`.

  - **`token`:** Permite establecer un _token_ de seguridad para garantizar que solo se ejecuten _scripts_ que contenga dicho token. El _token_ debe ser aleatorio. De hecho, está pensado para usarse en el _mini-framework_ **DLUnire**. No tiene efecto si `external` es `true`.

Por defecto, incorpora código JavaScript directamente en una salida HTML; por ejemplo:

```php
$js = ResourceManager::js('ruta.al.archivo');
echo $js;
```

Produciendo una salida similar a esto:

```html
<script>
console.log({ test: "Archivo con contenido de prueba" });
console.log({ test: "Esta es otra prueba" });
</script>
```

Pero si queremos incorporar código JavaScript directamente, sin etiquetas HTML, entonces, podríamos indicarlo en `$config`, como por ejemplo:

```php
$js = ResourceManager::js('ruta.al.archivo', [
    $external => true
]);

echo $js;
```

Dando como resultado, la siguiente salida:

```js
console.log({ test: "Archivo con contenido de prueba" });
console.log({ test: "Esta es otra prueba" });
```

Para que pueda ser usando en una implementación donde se utilicen rutas amigables para archivos JavaScript.

> Esto está pensado para usarse en el _micro-framework_ `DLUnire`

## Método `ResourceManager::image`

Procesa las imágenes directamente en base64.

**Sintaxis:**

```php
ResourceManager::image(string $filename): string;
```

**Parámetros:**

- **`$filename`:** Ruta de la imagen.

## Método `ResourceManager::asset`

Establece la ruta HTTP de un recursos a partir de una URI. En este caso, los recursos deben encontrarse en el directorioi `public/` o cualquier otro directorio que pueda ser accedido desde el protocolo HTTP.

**Sintaxis:**

```php
ResourceManager::asset(string $filename): string;
```

**Parámetros:**

- **`$filename`:** Ruta del archivo o recurso.
