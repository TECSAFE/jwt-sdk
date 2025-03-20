import {readFileSync, writeFileSync, existsSync, mkdirSync, rmSync} from 'fs';

const schema = JSON.parse(readFileSync('./schema.json', 'utf8'));

const OUTPUT_DIR = "./schemas";

if (existsSync(OUTPUT_DIR)) rmSync(OUTPUT_DIR, {recursive: true});
mkdirSync(OUTPUT_DIR);

const jwtType = schema.definitions.JwtType;
const roleEnum = schema.definitions.CockpitRole;

for (const [key, value] of Object.entries(schema.definitions)) {
  if (key === "JwtType") continue;
  if (value.properties?.type?.$ref) value.properties.type = jwtType;
  if (value.properties?.meta?.properties?.role?.$ref) value.properties.meta.properties.role = roleEnum;
  writeFileSync(`${OUTPUT_DIR}/${key}.json`, JSON.stringify(value, null, 2));
}
