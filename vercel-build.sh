#!/bin/bash
# Build script for Vercel

echo "Starting build process..."

# Create necessary directories
mkdir -p panel-dropship-admin/public/assets
mkdir -p website-dropship/public/assets
mkdir -p shared/database
mkdir -p php/conf.d

# Set permissions for SQLite database
if [ -f "shared/database/dropship.db" ]; then
    chmod 666 shared/database/dropship.db
fi

# Create default assets if they don't exist
if [ ! -f "website-dropship/public/assets/images/products/default.jpg" ]; then
    mkdir -p website-dropship/public/assets/images/products
    # Create a placeholder image
    convert -size 400x400 xc:#cccccc -pointsize 50 -fill white -gravity center -draw "text 0,0 'No Image'" website-dropship/public/assets/images/products/default.jpg 2>/dev/null || true
fi

echo "Build completed successfully!"
