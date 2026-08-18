"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.AuthService = void 0;
const bcryptjs_1 = __importDefault(require("bcryptjs"));
const jsonwebtoken_1 = __importDefault(require("jsonwebtoken"));
const uuid_1 = require("uuid");
const index_js_1 = __importDefault(require("../db/index.js"));
const env_js_1 = require("../config/env.js");
class AuthService {
    static async register(data) {
        const existingUser = index_js_1.default.prepare('SELECT id FROM users WHERE email = ?').get(data.email);
        if (existingUser) {
            throw new Error('Email is already registered');
        }
        const userId = (0, uuid_1.v4)();
        const passwordHash = await bcryptjs_1.default.hash(data.password, 10);
        const stmt = index_js_1.default.prepare(`
      INSERT INTO users (id, name, email, password_hash, birth_date, height_cm, water_goal_ml, sleep_goal_hours)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `);
        stmt.run(userId, data.name, data.email, passwordHash, data.birth_date || null, data.height_cm || null, data.water_goal_ml || 2000, data.sleep_goal_hours || 8.0);
        const user = index_js_1.default.prepare('SELECT id, name, email, birth_date, height_cm, water_goal_ml, sleep_goal_hours, created_at FROM users WHERE id = ?').get(userId);
        const token = jsonwebtoken_1.default.sign({ userId, email: data.email }, env_js_1.config.jwtSecret, {
            expiresIn: '7d',
        });
        return { user, token };
    }
    static async login(data) {
        const user = index_js_1.default.prepare('SELECT * FROM users WHERE email = ?').get(data.email);
        if (!user) {
            throw new Error('Invalid email or password');
        }
        const isMatch = await bcryptjs_1.default.compare(data.password, user.password_hash);
        if (!isMatch) {
            throw new Error('Invalid email or password');
        }
        const token = jsonwebtoken_1.default.sign({ userId: user.id, email: user.email }, env_js_1.config.jwtSecret, {
            expiresIn: '7d',
        });
        const { password_hash, ...userWithoutPassword } = user;
        return { user: userWithoutPassword, token };
    }
}
exports.AuthService = AuthService;
