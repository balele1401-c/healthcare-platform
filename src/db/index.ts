import Database from 'better-sqlite3';
import path from 'path';
import fs from 'fs';
import { config } from '../config/env.js';

const dbPath = path.resolve(process.cwd(), config.databaseUrl);
const dbDir = path.dirname(dbPath);

if (!fs.existsSync(dbDir)) {
  fs.mkdirSync(dbDir, { recursive: true });
}

export const db = new Database(dbPath);
db.pragma('journal_mode = WAL');
db.pragma('foreign_keys = ON');

export default db;
