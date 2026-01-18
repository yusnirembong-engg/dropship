#!/bin/bash

echo "🚀 Deploying Dropship System to Vercel..."

# Install Vercel CLI if not installed
if ! command -v vercel &> /dev/null; then
    echo "Installing Vercel CLI..."
    npm install -g vercel
fi

# Login to Vercel
echo "Checking Vercel login status..."
vercel whoami &> /dev/null
if [ $? -ne 0 ]; then
    echo "Please login to Vercel..."
    vercel login
fi

# Create necessary directories
mkdir -p website/assets/images/products
mkdir -p panel/assets/css
mkdir -p panel/assets/js

# Create placeholder images if they don't exist
if [ ! -f "website/assets/images/products/default.jpg" ]; then
    echo "Creating placeholder images..."
    # Create a simple placeholder image
    convert -size 400x400 xc:#cccccc website/assets/images/products/default.jpg 2>/dev/null || 
    echo "Note: ImageMagick not installed, skipping placeholder creation"
fi

# Create a simple logo if it doesn't exist
if [ ! -f "website/assets/images/logo.png" ]; then
    echo "Creating logo placeholder..."
    convert -size 200x100 xc:#0d6efd -fill white -pointsize 36 -gravity center -annotate 0 "Dropship\nStore" website/assets/images/logo.png 2>/dev/null || true
fi

# Create payment methods image
if [ ! -f "website/assets/images/payment-methods.png" ]; then
    echo "Creating payment methods placeholder..."
    convert -size 300x50 xc:#f8f9fa website/assets/images/payment-methods.png 2>/dev/null || true
fi

# Deploy to Vercel
echo "Deploying to Vercel..."
vercel --prod

echo "✅ Deployment complete!"
echo ""
echo "🌐 Your sites are now live at:"
echo "   - Customer Website: https://your-project.vercel.app"
echo "   - Admin Panel: https://your-project.vercel.app/admin"
echo ""
echo "🔑 Admin Login:"
echo "   Username: admin"
echo "   Password: admin123"
