import { JSONSchemaFaker as jsf } from 'json-schema-faker';
import { readFileSync, writeFileSync, readdirSync, existsSync, mkdirSync, rmSync } from 'fs';
import { join } from 'path';
import jwt from 'jsonwebtoken';
import * as crypto from 'crypto';

const __dirname = new URL('.', import.meta.url).pathname;
const SCHEMA_DIR = join(__dirname, '..', '..', 'schemas');
const OUTPUT_DIR = join(__dirname, 'keys');

if (!existsSync(SCHEMA_DIR)) {
  console.error('Schema directory not found at', SCHEMA_DIR);
  process.exit(1);
}

(async () => {
  if (!existsSync(OUTPUT_DIR)) mkdirSync(OUTPUT_DIR);
  for (const file of readdirSync(OUTPUT_DIR)) {
    rmSync(join(OUTPUT_DIR, file), { recursive: true });
  }
  
  const keyPair = crypto.generateKeyPairSync('rsa', {
    modulusLength: 2048,
    publicKeyEncoding: { type: 'spki', format: 'pem' },
    privateKeyEncoding: { type: 'pkcs8', format: 'pem' },
  });

  const jwks = {
    keys: [
      {
        kid: 'test',
        alg: 'RS256',
        ...crypto.createPublicKey(keyPair.publicKey).export({ format: 'jwk' }),
      },
    ],
  }

  writeFileSync(join(OUTPUT_DIR, 'jwks.json'), JSON.stringify(jwks, null, 2));
  console.log('Generated jwks.json');

  for (const file of readdirSync(SCHEMA_DIR)) {
    const schema = JSON.parse(readFileSync(join(SCHEMA_DIR, file), 'utf8'));
    const data = {
      ...jsf.generate(schema),
      nbf: 0,
      exp: Number.MAX_SAFE_INTEGER,
      iat: 1000,
    }
    writeFileSync(join(OUTPUT_DIR, file), JSON.stringify(data, null, 2));
    console.log(`Generated ${file}`);
    const signed = jwt.sign(
      data,
      keyPair.privateKey,
      {
        algorithm: 'RS256',
        keyid: 'test',
      },
    );
    writeFileSync(join(OUTPUT_DIR, `${file}.jwt`), signed);
    console.log(`Generated ${file}.jwt`);
  }

  const invalid = jwt.sign(
    {
      type: 'invalid',
    },
    crypto.generateKeyPairSync('rsa', {
      modulusLength: 2048,
      publicKeyEncoding: { type: 'spki', format: 'pem' },
      privateKeyEncoding: { type: 'pkcs8', format: 'pem' },
    }).privateKey,
    {
      algorithm: 'RS256',
      keyid: 'invalid',
    },
  );
  writeFileSync(join(OUTPUT_DIR, 'invalid.jwt'), invalid);
  console.log('Generated invalid.jwt');

  console.log('Done');
})();
