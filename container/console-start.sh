#!/usr/bin/env bash

printf "\n"
cat << EOF
  ---------------------------------------------------
   PHP Photo Gallery Container
  ---------------------------------------------------
EOF
printf "\n$(php -v | sed "s/^/  /g")\n\n"
apache2 -v
printf "\n\n"

/bin/bash