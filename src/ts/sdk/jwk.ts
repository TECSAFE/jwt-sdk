import { createLocalJWKSet } from 'jose';

const JWK_CACHE = {
  cache: {} as ReturnType<typeof createLocalJWKSet>,
  expiry: 0,
}

/**
 * Fetches a JWK from a given URL and caches it for a given time.
 * @param url The URL to fetch the JWK from
 * @param refresh The time in milliseconds to cache the JWK
 * @returns The JWK
 */
export async function getJWK(
  url: string = 'https://api-gateway.tecsafe.example.com/.well-known/jwks',
  refresh: number = 15 * 60 * 1000,
): Promise<ReturnType<typeof createLocalJWKSet>> {
  if (Date.now() < JWK_CACHE.expiry) return JWK_CACHE.cache;
  const response = await fetch(url);
  const data = await response.json();
  JWK_CACHE.cache = createLocalJWKSet(data);
  JWK_CACHE.expiry = Date.now() + refresh;
  return data;
}
