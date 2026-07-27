# Proyecto 3DSM-G2 — Servidor de Gestión de Usuarios

Configuración de un servidor Linux (CentOS Stream 9) que integra servicios de red y aplicación web con base de datos, probado desde un cliente Fedora conectado por bridge.

## Arquitectura

```
┌──────────────────────────────┐        ┌──────────────────────────────┐
│  VM SERVIDOR                 │        │  VM CLIENTE                  │
│  CentOS Stream 9             │◄──────►│  Fedora 44 Live              │
│  IP estática: 192.168.0.10   │  Red   │  IP dinámica: 192.168.0.150  │
│                              │ Bridge │                              │
│  Servicios:                  │        │  Pruebas:                    │
│  • DNS (BIND)                │        │  • ping, nslookup            │
│  • DHCP (ISC dhcpd)          │        │  • ssh                       │
│  • HTTP (Apache + PHP-FPM)   │        │  • Firefox HTTP + PHP        │
│  • MariaDB 10.5              │        │                              │
│  • SSH (OpenSSH)             │        │                              │
└──────────────────────────────┘        └──────────────────────────────┘
```

## Servicios configurados

| Servicio | Puerto | Descripción |
|---|---|---|
| DNS (BIND) | 53 | Resuelve `www.empresa.local` → `192.168.0.10` |
| DHCP (dhcpd) | 67 | Asigna IPs en rango `192.168.0.150-200` |
| HTTP (Apache) | 80 | Sirve `index.html` y `usuarios.php` |
| MariaDB | 3306 | BD `app_db` con tabla `usuarios` (3 registros) |
| SSH | 22 | Acceso remoto al servidor |

## Instalación (resumen)

```bash
# En el servidor CentOS Stream 9, como root:
dnf install -y bind bind-utils dhcp-server httpd mariadb-server php php-mysqlnd git nano

# Habilitar servicios
systemctl enable --now named dhcpd httpd mariadb sshd

# Firewall
firewall-cmd --permanent --add-service={http,https,dns,dhcp,ssh,mysql}
firewall-cmd --reload

# IP estática
nmcli connection modify "Wired connection 1" ipv4.method manual \
    ipv4.addresses 192.168.0.10/24 ipv4.gateway 192.168.0.1 \
    ipv4.dns "192.168.0.10 8.8.8.8"
nmcli connection up "Wired connection 1"
```

Los archivos de configuración completos (named.conf, dhcpd.conf, etc.) están en la sección "Procedimiento de instalación" del documento técnico.

## Estructura del repositorio

```
Entrega_Elvis/
├── Actividades_Individuales_Elvis.docx  — Días 1-2 (WSL + scripts)
├── Documento_Tecnico_Elvis.docx         — Días 3-5 (proyecto completo)
├── README.md                            — Este archivo
├── Capturas/                            — 35 capturas de evidencia
├── scripts/
│   └── limpieza.sh                      — Script Bash automatizado con cron
└── web/
    ├── index.html                       — Página principal del servidor
    └── usuarios.php                     — Consulta PHP → MariaDB
```

## Autor

**Elvis** — Grupo 3DSM-G2
Fecha de entrega: 27 de julio de 2026
