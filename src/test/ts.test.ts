import { afterAll, beforeAll, describe, expect, test } from '@jest/globals';
import { spawn } from 'child_process';
import { readFileSync } from 'fs';

import { getJWK, parseUnknownJwt, parseCustomerJwt } from '../ts';

describe('jwk', () => {
  let server: any;
  let customer: string;
  let unknown: string;

  beforeAll(async () => {
    server = spawn('node', [
      __dirname + '/example/server.js'
    ], { stdio: 'inherit' });
    customer = readFileSync(__dirname + '/example/customer.key').toString();
    unknown = readFileSync(__dirname + '/example/unknown.key').toString();
    await new Promise(resolve => setTimeout(resolve, 1000));
  })

  test('getJWK', async () => {
    const jwk = await getJWK('http://127.0.0.1:3000/jwk.json');
    expect(jwk).toBeDefined();
  })

  test('parseCustomerJwt', async () => {
    const jwtError = await parseCustomerJwt(unknown, true);
    expect(jwtError).toBeNull();
    const jwt = await parseCustomerJwt(customer);
    expect(jwt).toBeDefined();
  });

  test('parseUnknownJwt', async () => {
    const jwt = await parseUnknownJwt(unknown, true);
    expect(jwt).toBeDefined();
  });

  afterAll(() => {
    server.kill();
  })
})
