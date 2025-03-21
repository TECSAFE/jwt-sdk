import { JSONSchemaFaker as jsf } from 'json-schema-faker';
import { readFileSync, writeFileSync, readdirSync, existsSync, mkdirSync, rmSync } from 'fs';
import { join } from 'path';
import jwt from 'jsonwebtoken';
import * as crypto from 'crypto';

const __dirname = new URL('.', import.meta.url).pathname;
const SCHEMA_FILE = join(__dirname, '..', '..', 'schema.json');
const OUTPUT_DIR = join(__dirname, 'keys');

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

  const schema = JSON.parse(readFileSync(SCHEMA_FILE), 'utf8');

  for (const [key, definition] of Object.entries(schema.definitions)) {
    const file = `${key}.json`;

      // Eine Kopie des Definitionsobjekts erstellen, die auch die anderen Definitionen enthält
    const definitionWithContext = {
      ...definition,
      definitions: schema.definitions
    };

    const data = {
      ...jsf.generate(definitionWithContext),
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
