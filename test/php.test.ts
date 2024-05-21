import { afterAll, beforeAll, describe, expect, test } from '@jest/globals';
import { spawn, spawnSync } from 'child_process';
import { readFileSync, existsSync, rmSync } from 'fs';
import { join } from 'path';

describe('jwk', () => {
  const CACHE_FILE = join(__dirname, '../src/php/sdk/jwk-cache.json');

  let server: any;
  let customer: string;
  let unknown: string;

  beforeAll(async () => {
    server = spawn('node', [
      __dirname + '/example/server.js'
    ], { stdio: 'inherit' });
    customer = readFileSync(__dirname + '/example/customer.key').toString();
    unknown = readFileSync(__dirname + '/example/unknown.key').toString();
    if (existsSync(CACHE_FILE)) rmSync(CACHE_FILE);
    await new Promise(resolve => setTimeout(resolve, 1000));
  })

  const runPhpTest = async (stage: string) => {
    expect(spawnSync('php', [
      __dirname + '/php.test.php'
    ], {
      stdio: 'inherit',
      cwd: __dirname,
      env: {
        ...process.env,
        STAGE: stage,
      },
    }).status).toBe(0);
  };

  test('getJWK', async () => {
    await runPhpTest('getJWK');
  });

  test('parseCustomerJwt', async () => {
    await runPhpTest('parseCustomerJwt');
  });

  test('parseUnknownJwt', async () => {
    await runPhpTest('parseUnknownJwt');
  });

  afterAll(() => {
    if (existsSync(CACHE_FILE)) rmSync(CACHE_FILE);
    server.kill();
  })
})
