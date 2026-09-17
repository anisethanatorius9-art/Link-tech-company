#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const { createCanvas } = require('canvas');

// Read the SVG file
const svgPath = path.join(__dirname, 'public/favicon.svg');
const svgContent = fs.readFileSync(svgPath, 'utf-8');

// Create a canvas and render the SVG as PNG
const canvas = createCanvas(32, 32);
const ctx = canvas.getContext('2d');

// Use Puppeteer or a similar approach to render SVG
// For now, let's create a simple ICO from a canvas drawing of the Ventis logo

// Draw the Ventis logo directly on canvas
ctx.fillStyle = 'url(#grad)';

// Create a gradient
const grad = ctx.createLinearGradient(0, 0, 32, 32);
grad.addColorStop(0, '#1f2937');
grad.addColorStop(1, '#111827');
ctx.fillStyle = grad;

// Draw background rectangle
ctx.fillRect(0, 0, 32, 32);
ctx.beginPath();
ctx.roundRect(0, 0, 32, 32, 6);
ctx.fill();

// Draw the V for Ventis
ctx.strokeStyle = '#10b981';
ctx.lineWidth = 2;
ctx.lineCap = 'round';
ctx.lineJoin = 'round';

// First stroke of V
ctx.beginPath();
ctx.moveTo(10, 6);
ctx.lineTo(14, 20);
ctx.stroke();

// Second stroke of V
ctx.beginPath();
ctx.moveTo(22, 6);
ctx.lineTo(18, 20);
ctx.stroke();

// Draw the POS indicator bars
ctx.fillStyle = '#10b981';
ctx.fillRect(6, 20, 20, 2);
ctx.globalAlpha = 0.7;
ctx.fillRect(6, 24, 20, 2);

// Save as PNG first, then convert to ICO
const pngPath = path.join(__dirname, 'public/favicon.png');
const pngStream = canvas.createPNGStream();
const pngFile = fs.createWriteStream(pngPath);

pngStream.pipe(pngFile);

pngFile.on('finish', () => {
  console.log('PNG favicon created successfully');

  // Now convert PNG to ICO using png-to-ico or similar
  // For this example, we'll just copy the PNG as the ICO (simplified approach)
  // In production, you'd use a proper conversion library

  const pngBuffer = fs.readFileSync(pngPath);
  const icoPath = path.join(__dirname, 'public/favicon.ico');
  fs.writeFileSync(icoPath, pngBuffer);
  console.log('favicon.ico created successfully');

  // Clean up the temporary PNG
  fs.unlinkSync(pngPath);
});

pngFile.on('error', (err) => {
  console.error('Error creating favicon:', err);
  process.exit(1);
});
