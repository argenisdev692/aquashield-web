import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const pagesDir = path.join(__dirname, 'src', 'pages');

const files = fs.readdirSync(pagesDir).filter(f => f.endsWith('.astro'));

for (const file of files) {
  const filePath = path.join(pagesDir, file);
  let content = fs.readFileSync(filePath, 'utf8');
  
  // Transform filename, e.g., contact-support.astro -> ContactSupport
  const baseName = file.replace('.astro', '');
  const expectedComponentName = baseName.split('-').map(part => part.charAt(0).toUpperCase() + part.slice(1)).join('');
  
  // See if there's an import for this expected piece
  const importRegex = new RegExp(`import\\s+${expectedComponentName}\\s+from\\s+['"]\\.\\.[/|\\\\]components[/|\\\\]${expectedComponentName}\\.astro['"];?`);
  
  if (importRegex.test(content)) {
    // Replace the import
    content = content.replace(importRegex, `import ${expectedComponentName}Component from '../components/${expectedComponentName}.astro';`);
    
    // Replace the usage tag
    const tagRegex1 = new RegExp(`<${expectedComponentName}\\s*/>`, 'g');
    const tagRegex2 = new RegExp(`<${expectedComponentName}>`, 'g');
    const tagRegex3 = new RegExp(`</${expectedComponentName}>`, 'g');
    
    content = content.replace(tagRegex1, `<${expectedComponentName}Component />`);
    content = content.replace(tagRegex2, `<${expectedComponentName}Component>`);
    content = content.replace(tagRegex3, `</${expectedComponentName}Component>`);
    
    fs.writeFileSync(filePath, content);
    console.log(`Updated ${file}`);
  }
}
console.log('Done checking and fixing components imports.');
