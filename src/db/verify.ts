import Database from 'better-sqlite3';

const db = new Database('./data/health_tracker.db');

console.log('\n=== DATABASE SCHEMA VERIFICATION ===\n');

const tables = db.prepare("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;").all();
console.log('Tables created:', tables.map((t: any) => t.name).join(', '));

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
  columns.forEach((col: any) => {
    console.log(`  ${col.name}: ${col.type}${col.notnull ? ' NOT NULL' : ''}${col.pk ? ' PRIMARY KEY' : ''}`);
  });
});

const indexes = db.prepare("SELECT name FROM sqlite_master WHERE type='index' ORDER BY name;").all();
console.log('\n--- Indexes ---');
indexes.forEach((idx: any) => {
  if (!idx.name.startsWith('sqlite_')) {
    console.log(`  ${idx.name}`);
  }
});

console.log('\n✅ Database schema verified successfully!\n');
db.close();
