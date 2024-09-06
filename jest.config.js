/** @type {import('ts-jest').JestConfigWithTsJest} */
export default {
  preset: 'ts-jest/presets/default-esm',
  extensionsToTreatAsEsm: ['.ts', '.tsx'], // Treat TypeScript files as ESM
  transform: {
    '^.+\\.tsx?$': ['ts-jest', { // Use ts-jest for TypeScript files with inlined configuration
      useESM: true, // Ensures that ts-jest knows to handle ESM syntax
    }],
  },
  moduleNameMapper: {
    // Map .js extensions back to .ts for Jest
    '^(.*)\\.js$': '$1',
  },
};
