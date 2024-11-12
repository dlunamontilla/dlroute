# Changelog v1.3.37

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

## Documentation

- Añadida documentación PHPDoc para cada método de `DLHost` y `DLServer` que detalla el propósito de cada método y sus retornos esperados.
  