#!/bin/bash
echo "---------------------------------------------------"
echo "------------- The container is running ------------"
echo "---------------------------------------------------"

if [ ! -f "fixed-permissions" ]; then
  chown www-data:www-data -Rv ./
  chmod 775 -Rv ./
  echo "true" > fixed-permissions
fi