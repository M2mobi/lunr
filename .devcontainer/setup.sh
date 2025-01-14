#!/bin/bash

composer install
for command in $(ls vendor/bin); do
    ln -s "$(pwd)/vendor/bin/$command" "/usr/local/bin/$command"
done