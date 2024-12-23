# Changelog v1.3.38

## Added

- **DLHost Class** en el módulo `DLRoute\Server`:
  - Implementada la clase `DLHost` para gestionar configuraciones relacionadas con el protocolo y el dominio en la aplicación.
  - Añadidos los métodos estáticos documentados con `@method` en la clase:
    - `get_hostname()`: Devuelve el nombre del host actual.
    - `get_domain()`: Devuelve el nombre de dominio en uso, limpiando posibles puertos en la URL.
    - `is_https()`: Determina si el usuario está accediendo al sitio web a través del protocolo HTTPS, soportando proxies inversos y balanceadores de carga.
  - Método `https()`: Obliga al sitio web a redirigir a HTTPS para los dominios especificados en el array `hostnames`, configurado en el constructor de la clase.
  - Constructor `__construct(array $hostnames = [])`: Permite definir los dominios que deben redirigirse a HTTPS.

## Modified

- **DLServer Class**:

  - Se actualizó el método `get_protocol()` para usar el nuevo método `DLHost::is_https()` y determinar el protocolo en uso (`http` o `https`), mejorando así la detección de HTTPS en entornos con proxies y balanceadores.

- **DLUpload Trait**:
  - Se cambiaron los métodos de protegidos a públicos para permitir un acceso más flexible:
    - `upload_file(string $field, string $type = "*/*"): array`: Maneja la carga de archivos en el servidor.
    - `get_filenames(): array`: Devuelve los nombres de los archivos cargados.
    - `set_basedir(string $basedir): void`: Establece el directorio base para los archivos cargados.
    - `set_thumbnail_width(int $width): void`: Configura la anchura de los thumbnails.
    - `get_absolute_path(string $relative_path): string`: Devuelve la ruta absoluta de un archivo dado su ruta relativa.

## Documentation

- Añadida documentación PHPDoc para cada método de `DLHost` y `DLServer` que detalla el propósito de cada método y sus retornos esperados.
- Documentados los métodos de `DLUpload` reflejando el cambio en su nivel de acceso.
