"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const child_process_1 = require("child_process");
const axios_1 = __importDefault(require("axios"));
const API_URL = 'http://localhost:5000/api';
let authToken = '';
let userId = '';
let medicationId = '';
const api = axios_1.default.create({
    baseURL: API_URL,
    validateStatus: () => true,
});
const results = [];
function logTest(endpoint, method, status, success, message) {
    const result = { endpoint, method, status, success, message };
    results.push(result);
    const emoji = success ? '✅' : '❌';
    console.log(`${emoji} ${method.padEnd(6)} ${endpoint.padEnd(35)} [${status}] ${message}`);
}
async function runTests() {
    console.log('\n🧪 Testing PulseTrack API Endpoints\n');
    console.log('━'.repeat(80));
    try {
        console.log('\n📝 Auth Module\n');
        const registerRes = await api.post('/auth/register', {
            name: 'Test User',
            email: 'test@example.com',
            password: 'test123456',
            height_cm: 180,
            water_goal_ml: 2000,
            sleep_goal_hours: 8,
        });
        logTest('/auth/register', 'POST', registerRes.status, registerRes.status === 201, 'User registration');
        if (registerRes.status === 201 && registerRes.data.data?.token) {
            authToken = registerRes.data.data.token;
            userId = registerRes.data.data.user?.id || '';
        }
        const loginRes = await api.post('/auth/login', {
            email: 'test@example.com',
            password: 'test123456',
        });
        logTest('/auth/login', 'POST', loginRes.status, loginRes.status === 200, 'User login');
        if (!authToken && loginRes.data.data?.token) {
            authToken = loginRes.data.data.token;
            userId = loginRes.data.data.user?.id || '';
        }
        const headers = { Authorization: `Bearer ${authToken}` };
        console.log('\n📊 Daily Logs Module\n');
        const getTodayRes = await api.get('/logs/today', { headers });
        logTest('/logs/today', 'GET', getTodayRes.status, getTodayRes.status === 200, 'Get today log');
        const updateWaterRes = await api.post('/logs/water', { delta_ml: 250 }, { headers });
        logTest('/logs/water', 'POST', updateWaterRes.status, updateWaterRes.status === 200, 'Update water intake');
        const sleepStart = new Date(Date.now() - 8 * 60 * 60 * 1000).toISOString();
        const sleepEnd = new Date().toISOString();
        const updateSleepRes = await api.post('/logs/sleep', {
            start_time: sleepStart,
            end_time: sleepEnd,
        }, { headers });
        logTest('/logs/sleep', 'POST', updateSleepRes.status, updateSleepRes.status === 200, 'Update sleep log');
        const updateStepsRes = await api.post('/logs/steps', { steps: 5000 }, { headers });
        logTest('/logs/steps', 'POST', updateStepsRes.status, updateStepsRes.status === 200, 'Update steps count');
        console.log('\n❤️ Health Metrics Module\n');
        const createMetricRes = await api.post('/metrics', {
            metric_type: 'weight',
            value_primary: 72.5,
        }, { headers });
        logTest('/metrics', 'POST', createMetricRes.status, createMetricRes.status === 201, 'Create weight metric (auto BMI)');
        const getMetricsRes = await api.get('/metrics', { headers });
        logTest('/metrics', 'GET', getMetricsRes.status, getMetricsRes.status === 200, 'Get health metrics history');
        const bpRes = await api.post('/metrics', {
            metric_type: 'blood_pressure',
            value_primary: 120,
            value_secondary: 80,
        }, { headers });
        logTest('/metrics', 'POST', bpRes.status, bpRes.status === 201, 'Create blood pressure metric');
        console.log('\n💊 Medications Module\n');
        const createMedRes = await api.post('/medications', {
            name: 'Aspirin',
            dosage: '100mg',
            schedule_time: '08:00:00',
        }, { headers });
        logTest('/medications', 'POST', createMedRes.status, createMedRes.status === 201, 'Create medication');
        if (createMedRes.data.data?.id) {
            medicationId = createMedRes.data.data.id;
        }
        const getTodayMedsRes = await api.get('/medications/today', { headers });
        logTest('/medications/today', 'GET', getTodayMedsRes.status, getTodayMedsRes.status === 200, 'Get today medications');
        if (medicationId) {
            const checkMedRes = await api.post(`/medications/${medicationId}/check`, {
                status: 'taken',
            }, { headers });
            logTest(`/medications/${medicationId}/check`, 'POST', checkMedRes.status, checkMedRes.status === 200, 'Check medication');
        }
        console.log('\n📈 Analytics Module\n');
        const weeklySummaryRes = await api.get('/analytics/weekly', { headers });
        logTest('/analytics/weekly', 'GET', weeklySummaryRes.status, weeklySummaryRes.status === 200, 'Get weekly summary');
        console.log('\n' + '━'.repeat(80));
        console.log('\n📊 Test Summary\n');
        const totalTests = results.length;
        const passedTests = results.filter((r) => r.success).length;
        const failedTests = totalTests - passedTests;
        console.log(`Total Tests: ${totalTests}`);
        console.log(`✅ Passed: ${passedTests}`);
        console.log(`❌ Failed: ${failedTests}`);
        console.log(`Success Rate: ${((passedTests / totalTests) * 100).toFixed(2)}%\n`);
        if (failedTests === 0) {
            console.log('🎉 All tests passed!\n');
        }
        else {
            console.log('⚠️  Some tests failed. Check results above.\n');
        }
        process.exit(failedTests === 0 ? 0 : 1);
    }
    catch (error) {
        console.error('❌ Test execution failed:', error);
        process.exit(1);
    }
}
async function startServerAndTest() {
    console.log('🚀 Starting server and running tests...\n');
    const server = (0, child_process_1.spawn)('npx', ['tsx', 'src/index.ts'], {
        stdio: 'pipe',
        shell: true,
    });
    server.stdout?.on('data', (data) => {
        const output = data.toString();
        if (output.includes('running on')) {
            console.log('✅ Server started successfully\n');
            setTimeout(() => runTests(), 1000);
        }
    });
    server.stderr?.on('data', (data) => {
        console.error('Server error:', data.toString());
    });
    process.on('exit', () => {
        server.kill();
    });
}
startServerAndTest();
