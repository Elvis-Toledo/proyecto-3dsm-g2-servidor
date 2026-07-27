#!/bin/bash
# limpieza.sh - Automatiza la limpieza de archivos temporales
# Proyecto 3DSM-G2 - Elvis
# Programado en cron para ejecutarse diariamente a las 8:00 AM

echo "Limpiando archivos temporales..."
rm -rf /tmp/*
echo "Limpieza completada"
