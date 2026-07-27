# Proyecto 3DSM-G2 — Aplicación Web de Gestión de Usuarios

**Unidad V — Instalación de servicios para aplicaciones Web**

Desarrollo y despliegue de una aplicación web con base de datos sobre un servidor CentOS Stream 9, integrando servicios de red (DNS, DHCP, HTTP, SSH, MariaDB) y control de versiones con Git/GitHub.

**Autor:** Elvis Ragel Toledo Aleman — Grupo 3DSM-G2

---

## Arquitectura

```
┌──────────────────────────────┐        ┌──────────────────────────────┐
│  VM SERVIDOR                 │        │  VM CLIENTE                  │
│  CentOS Stream 9             │◄──────►│  Fedora 44 Live              │
│  IP estática: 192.168.0.10   │  Red   │  IP dinámica: 192.168.0.150  │
│                              │ Bridge │                              │
│  Servicios:                  │        │  Pruebas:                    │
│  • DNS (BIND 9.16)           │        │  • ip a, ping                │
│  • DHCP (ISC dhcpd)          │        │  • nslookup                  │
│  • HTTP (Apache + PHP-FPM)   │        │  • ssh                       │
│  • MariaDB 10.5              │        │  • Firefox → app web         │
│  • SSH (OpenSSH)             │        │                              │
└──────────────────────────────┘        └──────────────────────────────┘
```

## Servicios configurados

| Servicio | Puerto | Descripción |
|---|---|---|
| DNS (BIND) | 53 | Resuelve `www.empresa.local` → `192.168.0.10` |
| DHCP (dhcpd) | 67 | Asigna IPs en rango `192.168.0.150-200` |
| HTTP (Apache) | 80 | Sirve la aplicación PHP |
| MariaDB | 3306 | BD `app_db` con tabla `usuarios` |
| SSH | 22 | Acceso remoto al servidor |

## Aplicación web

`web/index.php` implementa los dos requerimientos funcionales de la FASE 1:

- **RF-01 — Alta de usuarios:** formulario con nombre y correo, usando *prepared statements* para prevenir inyección SQL.
- **RF-02 — Listado de usuarios:** tabla con todos los registros de la base de datos.

## Instalación

```bash
# Como root en CentOS Stream 9
dnf update -y
dnf install -y bind bind-utils dhcp-server httpd mariadb-server php php-mysqlnd git nano
```

```bash
systemctl enable --now named dhcpd httpd mariadb sshd
```

```bash
for svc in http https dns dhcp ssh mysql; do firewall-cmd --permanent --add-service=$svc; done
firewall-cmd --reload
```

```bash
nmcli connection modify "Wired connection 1" ipv4.method manual ipv4.addresses 192.168.0.10/24 ipv4.gateway 192.168.0.1 ipv4.dns "192.168.0.10 8.8.8.8"
nmcli connection up "Wired connection 1"
```

## Base de datos

```sql
CREATE DATABASE app_db CHARACTER SET utf8mb4;
USE app_db;
CREATE TABLE usuarios (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    nombre  VARCHAR(100) NOT NULL,
    correo  VARCHAR(100) NOT NULL UNIQUE
);
```

## Despliegue

```bash
cd /var/www/html
git clone https://github.com/Elvis-Toledo/proyecto-3dsm-g2-servidor.git repo
cp repo/web/*.php repo/web/*.html /var/www/html/
chown -R apache:apache /var/www/html
restorecon -Rv /var/www/html
systemctl restart httpd
```

## Configuración de credenciales

La contraseña de MariaDB **no está en el código**. Se lee de la variable de entorno `DB_PASS`:

```bash
echo 'SetEnv DB_PASS "tu_contrasena"' > /etc/httpd/conf.d/app.conf
systemctl restart httpd
```

En un entorno real debe usarse un usuario de BD con permisos limitados (no `root`) y una contraseña fuerte.

## Flujo de trabajo con Git

El proyecto se desarrolló de forma individual, aplicando el flujo colaborativo de *feature branches* y Pull Requests:

```bash
git checkout -b feature/nombre-funcionalidad
git add .
git commit -m "Descripción del cambio"
git push origin feature/nombre-funcionalidad
# → abrir Pull Request en GitHub → revisar → merge a main
```

## Estructura del repositorio

```
.
├── README.md
├── .gitignore
├── Capturas/                          35 evidencias del proyecto
├── Documentos de entrega en .docx/
│   ├── FASE1_Planeacion_Elvis.docx    Requerimientos y modelo de BD
│   ├── Actividades_Individuales_Elvis.docx
│   └── Documento_Tecnico_Elvis.docx
├── Documentos de entrega en PDF/
├── scripts/
│   └── limpieza.sh                    Automatización con cron
└── web/
    ├── index.php                      Aplicación principal
    ├── index.html                     Página informativa
    └── usuarios.php                   Vista de listado
```

## Pruebas de validación

| Prueba | Comando | Resultado |
|---|---|---|
| DHCP | `ip a` | IP automática asignada |
| DNS | `nslookup www.empresa.local` | Dominio resuelto a 192.168.0.10 |
| Conectividad | `ping www.empresa.local` | Respuesta correcta |
| HTTP | Firefox → `www.empresa.local` | Aplicación visible |
| SSH | `ssh elvis@192.168.0.10` | Acceso remoto exitoso |
