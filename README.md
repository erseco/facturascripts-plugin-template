# PluginTemplate para FacturaScripts

Plantilla base para crear plugins de FacturaScripts con una estructura mínima, tests, Docker y workflows de CI/release.

## Qué incluye

- **Estructura base**: `Init`, controladores, modelo, tabla XML y traducciones de ejemplo
- **Entorno Docker**: arranque rápido para desarrollar sobre FacturaScripts
- **Tests de ejemplo**: base para pruebas de plugin con PHPUnit
- **CI y releases**: workflows preparados para validar y empaquetar el plugin
- **Compatibilidad declarada**: FacturaScripts 2025 y PHP 8.1 o superior

## Instalación

1. Usa este repositorio como base para tu plugin
2. Renombra `PluginTemplate` al nombre real de tu plugin
3. Actualiza namespaces, `facturascripts.ini`, `README` y textos de traducción
4. Revisa las cabeceras de licencia y autoría antes de publicar

## Desarrollo

- `make upd`
- `make lint`
- `make test`
- `make format`
- `make package VERSION=1.0.0`

## Licencia

LGPL-3.0. Ver [LICENSE](LICENSE) para más detalles.
