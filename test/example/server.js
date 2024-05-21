import { readFileSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname } from 'path';
import express from 'express';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const app = express();

app.get('/jwk.json', (_, res) => {
  res.json(JSON.parse(readFileSync(__dirname + '/jwk.json', 'utf8')));
});

app.listen(3000, () => {
  console.log('Server running on port 3000');
});
