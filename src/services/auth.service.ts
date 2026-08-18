import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { v4 as uuidv4 } from 'uuid';
import db from '../db/index.js';
import { config } from '../config/env.js';

export interface UserRow {
  id: string;
  name: string;
  email: string;
  password_hash: string;
  birth_date?: string;
  height_cm?: number;
  water_goal_ml?: number;
  sleep_goal_hours?: number;
  created_at?: string;
}

export class AuthService {
  static async register(data: {
    name: string;
    email: string;
    password: string;
    birth_date?: string;
    height_cm?: number;
    water_goal_ml?: number;
    sleep_goal_hours?: number;
  }) {
    const existingUser = db.prepare('SELECT id FROM users WHERE email = ?').get(data.email);
    if (existingUser) {
      throw new Error('Email is already registered');
    }

    const userId = uuidv4();
    const passwordHash = await bcrypt.hash(data.password, 10);

    const stmt = db.prepare(`
      INSERT INTO users (id, name, email, password_hash, birth_date, height_cm, water_goal_ml, sleep_goal_hours)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `);

    stmt.run(
      userId,
      data.name,
      data.email,
      passwordHash,
      data.birth_date || null,
      data.height_cm || null,
      data.water_goal_ml || 2000,
      data.sleep_goal_hours || 8.0
    );

    const user = db.prepare('SELECT id, name, email, birth_date, height_cm, water_goal_ml, sleep_goal_hours, created_at FROM users WHERE id = ?').get(userId);

    const token = jwt.sign({ userId, email: data.email }, config.jwtSecret, {
      expiresIn: '7d',
    });

    return { user, token };
  }

  static async login(data: { email: string; password: string }) {
    const user = db.prepare('SELECT * FROM users WHERE email = ?').get(data.email) as UserRow | undefined;
    if (!user) {
      throw new Error('Invalid email or password');
    }

    const isMatch = await bcrypt.compare(data.password, user.password_hash);
    if (!isMatch) {
      throw new Error('Invalid email or password');
    }

    const token = jwt.sign({ userId: user.id, email: user.email }, config.jwtSecret, {
      expiresIn: '7d',
    });

    const { password_hash, ...userWithoutPassword } = user;
    return { user: userWithoutPassword, token };
  }
}
