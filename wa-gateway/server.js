const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const cors = require('cors'); // Need CORS for frontend fetch
const app = express();
const port = 3000;

app.use(express.json());
app.use(cors());

console.log("Initializing WhatsApp Client...");

// Global variables to store state
let qrCodeData = null;
let clientStatus = 'INITIALIZING';
let clientInfo = null;

const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    }
});

client.on('qr', (qr) => {
    console.log('QR Code Received');
    qrCodeData = qr; // Store QR string
    clientStatus = 'QR_READY';
    // Optional: Still show in terminal for debugging
    // qrcode.generate(qr, { small: true });
});

client.on('ready', () => {
    console.log('WhatsApp Client is Ready!');
    clientStatus = 'READY';
    qrCodeData = null;
    clientInfo = client.info;
});

client.on('authenticated', () => {
    console.log('WhatsApp Authenticated!');
    clientStatus = 'AUTHENTICATED';
    qrCodeData = null;
});

client.on('auth_failure', msg => {
    console.error('AUTHENTICATION FAILURE', msg);
    clientStatus = 'AUTH_FAILURE';
});

client.on('disconnected', (reason) => {
    console.log('Client was disconnected', reason);
    clientStatus = 'DISCONNECTED';
    qrCodeData = null;
    clientInfo = null;
    // Re-initialize to allow re-scan
    client.initialize();
});

// --- API ENDPOINTS ---

// Get Status & QR
app.get('/status', (req, res) => {
    res.json({
        status: clientStatus,
        qr: qrCodeData,
        info: clientInfo ? {
            wid: clientInfo.wid,
            pushname: clientInfo.pushname,
            platform: clientInfo.platform
        } : null
    });
});

// Logout
app.post('/logout', async (req, res) => {
    try {
        await client.logout();
        res.json({ status: true, message: 'Logged out successfully' });
    } catch (error) {
        res.status(500).json({ status: false, message: 'Logout failed', error: error.toString() });
    }
});

// Send Message
app.post('/send-message', async (req, res) => {
    const { number, message } = req.body;

    if (!number || !message) {
        return res.status(400).json({ status: false, message: 'Number and message required' });
    }

    // Format number to 'number@c.us'
    let formattedNumber = number.replace(/\D/g, '');
    if (formattedNumber.startsWith('0')) {
        formattedNumber = '62' + formattedNumber.slice(1);
    }
    const chatId = formattedNumber + "@c.us";

    try {
        // Double check state
        /* const state = await client.getState();
           Note: getState() sometimes buggy in headless, rely on internal event status for now or try/catch send
        */

        await client.sendMessage(chatId, message, { sendSeen: false });
        console.log(`Message sent to ${formattedNumber}`);
        res.json({ status: true, message: 'Message sent successfully' });
    } catch (error) {
        console.error('Error sending message:', error);
        res.status(500).json({ status: false, message: 'Failed to send message', error: error.toString() });
    }
});

client.initialize();

app.listen(port, () => {
    console.log(`WA Gateway Server running at http://localhost:${port}`);
});
