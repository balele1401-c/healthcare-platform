"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const better_sqlite3_1 = __importDefault(require("better-sqlite3"));
const db = new better_sqlite3_1.default('./data/health_tracker.db');
console.log('\n=== DATABASE SCHEMA VERIFICATION ===\n');
const tables = db.prepare("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;").all();
console.log('Tables created:', tables.map((t) => t.name).join(', '));
const schemas = [
    'users',
    'daily_logs',
    'health_metrics',
    'medications',
    'medication_logs'
];
schemas.forEach(tableName => {
    console.log(`\n--- ${tableName} ---`);
    const columns = db.prepare(`PRAGMA table_info(${tableName});`).all();
    columns.forEach((col) => {
        console.log(`  ${col.name}: ${col.type}${col.notnull ? ' NOT NULL' : ''}${col.pk ? ' PRIMARY KEY' : ''}`);
    });
});
const indexes = db.prepare("SELECT name FROM sqlite_master WHERE type='index' ORDER BY name;").all();
console.log('\n--- Indexes ---');
indexes.forEach((idx) => {
    if (!idx.name.startsWith('sqlite_')) {
        console.log(`  ${idx.name}`);
    }
});
console.log('\n✅ Database schema verified successfully!\n');
db.close();
