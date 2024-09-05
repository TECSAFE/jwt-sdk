import fs from 'fs';

function rename(prop, path = null) {
  if (!path) path = `dist/${prop}`
  const files = fs.readdirSync(path);
  files.forEach((file) => {
    const filePath = `${path}/${file}`;
    if (fs.lstatSync(filePath).isDirectory()) {
      rename(prop, filePath);
    } else if (file.endsWith('.js')) {
      const newFilePath = filePath.replace('.js', `.${prop}`);
      fs.renameSync(filePath, newFilePath);
    }
  });
}

rename('cjs');
