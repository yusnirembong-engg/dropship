// server.js - Node.js server for Vercel
const { createServer } = require('http');
const { parse } = require('url');
const next = require('next');

const dev = process.env.NODE_ENV !== 'production';
const app = next({ dev });
const handle = app.getRequestHandler();

app.prepare().then(() => {
  createServer((req, res) => {
    const parsedUrl = parse(req.url, true);
    const { pathname } = parsedUrl;
    
    // Handle PHP routes
    if (pathname.startsWith('/api/') && pathname.endsWith('.php')) {
      // This will be handled by Vercel's PHP runtime
      return handle(req, res, parsedUrl);
    }
    
    // Handle static files
    if (pathname.match(/\.(css|js|jpg|jpeg|png|gif|ico|svg)$/)) {
      return handle(req, res, parsedUrl);
    }
    
    // Default to Next.js handler
    handle(req, res, parsedUrl);
  }).listen(3000, (err) => {
    if (err) throw err;
    console.log('> Ready on http://localhost:3000');
  });
});
