#!/bin/bash

echo "🔄 Running composer update..."
composer update

echo "🚀 Starting Apache..."
exec apache2-foreground
