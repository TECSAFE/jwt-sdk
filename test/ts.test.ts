import { afterAll, beforeAll, describe, expect, test } from '@jest/globals';
import { spawn } from 'child_process';
import { readFileSync } from 'fs';
import { createLocalJWKSet } from 'jose';

import { getJWK, parseUnknownJwt, parseCustomerJwt, parseInternalJwt, parseSalesChannelJwt } from '../src/ts/index';

describe('jwk', () => {
  let server: any;
  let invalid: string;
  let customer: string;
  let customerObject: any;
  let internal: string;
  let internalObject: any;
  let salesChannel: string;
  let salesChannelObject: any;
  let jwk: any;

  beforeAll(async () => {
    server = spawn('node', [
      __dirname + '/example/server.js'
    ], { stdio: 'inherit' });
    const read = (file: string): string => readFileSync(__dirname + '/example/keys/' + file).toString();
    invalid = read('invalid.jwt');
    customer = read('JwtCustomer.json.jwt');
    customerObject = JSON.parse(read('JwtCustomer.json'));
    internal = read('JwtInternal.json.jwt');
    internalObject = JSON.parse(read('JwtInternal.json'));
    salesChannel = read('JwtSalesChannel.json.jwt');
    salesChannelObject = JSON.parse(read('JwtSalesChannel.json'));
    jwk = createLocalJWKSet(JSON.parse(read('jwks.json')));
    await new Promise(resolve => setTimeout(resolve, 1000));
  })

  describe('getJWK', () => {
    it('should return a JWK', async () => {
      const jwk = await getJWK('http://127.0.0.1:3000/jwk.json');
      expect(jwk).toBeDefined();
      expect(jwk).toEqual(jwk);
    });
  })

  describe('parseInternalJwt', () => {
    test.each([[true, jwk], [false, undefined]])('should parse a valid internal token with jwk %p', async (_, jwk) => {
      const obj = await parseInternalJwt(internal, jwk);
      expect(obj).toBeDefined();
      expect(obj).toEqual(internalObject);
    });
    test.each([[true, jwk], [false, undefined]])('should return null for an invalid token with jwk %p', async (_, jwk) => {
      const obj = await parseInternalJwt(invalid, jwk);
      expect(obj).toBeNull();
    });
    test.each([[true, jwk], [false, undefined]])('should return null for a customer token with jwk %p', async (_, jwk) => {
      const obj = await parseInternalJwt(customer, jwk);
      expect(obj).toBeNull();
    });
    test.each([[true, jwk], [false, undefined]])('should return null for a sales channel token with jwk %p', async (_, jwk) => {
      const obj = await parseInternalJwt(salesChannel, jwk);
      expect(obj).toBeNull();
    });
  });

  describe('parseCustomerJwt', () => {
    test.each([[true, jwk], [false, undefined]])('should parse a valid customer token with jwk %p', async (_, jwk) => {
      const obj = await parseCustomerJwt(customer, jwk);
      expect(obj).toBeDefined();
      expect(obj).toEqual(customerObject);
    });
    test.each([[true, jwk], [false, undefined]])('should return null for an invalid token with jwk %p', async (_, jwk) => {
      const obj = await parseCustomerJwt(invalid, jwk);
      expect(obj).toBeNull();
    });
    test.each([[true, jwk], [false, undefined]])('should return null for an internal token with jwk %p', async (_, jwk) => {
      const obj = await parseCustomerJwt(internal, jwk);
      expect(obj).toBeNull();
    });
    test.each([[true, jwk], [false, undefined]])('should return null for a sales channel token with jwk %p', async (_, jwk) => {
      const obj = await parseCustomerJwt(salesChannel, jwk);
      expect(obj).toBeNull();
    });
  });

  describe('parseSalesChannelJwt', () => {
    test.each([[true, jwk], [false, undefined]])('should parse a valid sales channel token with jwk %p', async (_, jwk) => {
      const obj = await parseSalesChannelJwt(salesChannel, jwk);
      expect(obj).toBeDefined();
      expect(obj).toEqual(salesChannelObject);
    });
    test.each([[true, jwk], [false, undefined]])('should return null for an invalid token with jwk %p', async (_, jwk) => {
      const obj = await parseSalesChannelJwt(invalid, jwk);
      expect(obj).toBeNull();
    });
    test.each([[true, jwk], [false, undefined]])('should return null for an internal token with jwk %p', async (_, jwk) => {
      const obj = await parseSalesChannelJwt(internal, jwk);
      expect(obj).toBeNull();
    });
    test.each([[true, jwk], [false, undefined]])('should return null for a customer token with jwk %p', async (_, jwk) => {
      const obj = await parseSalesChannelJwt(customer, jwk);
      expect(obj).toBeNull();
    });
  });

  describe('parseUnknownJwt', () => {
    test.each([[true, jwk], [false, undefined]])('should parse a valid customer token with jwk %p', async (_, jwk) => {
      const obj = await parseUnknownJwt(customer, jwk);
      expect(obj).toBeDefined();
      expect(obj).toEqual(customerObject);
    });
    test.each([[true, jwk], [false, undefined]])('should parse a valid internal token with jwk %p', async (_, jwk) => {
      const obj = await parseUnknownJwt(internal, jwk);
      expect(obj).toBeDefined();
      expect(obj).toEqual(internalObject);
    });
    test.each([[true, jwk], [false, undefined]])('should parse a valid sales channel token with jwk %p', async (_, jwk) => {
      const obj = await parseUnknownJwt(salesChannel, jwk);
      expect(obj).toBeDefined();
      expect(obj).toEqual(salesChannelObject);
    });
    test.each([[true, jwk], [false, undefined]])('should return null for an invalid token with jwk %p', async (_, jwk) => {
      const obj = await parseUnknownJwt(invalid, jwk);
      expect(obj).toBeNull();
    });
  });

  afterAll(() => {
    server.kill();
  })
})
