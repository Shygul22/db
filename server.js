// server.js
const express = require('express');
const { MongoClient } = require('mongodb');
const cors = require('cors');

const app = express();
const PORT = 3000;

// Middleware
app.use(cors());
app.use(express.json());

// Connection URL
const url = 'mongodb://localhost:27017';
const client = new MongoClient(url);
const dbName = 'consultingDB';

// Connect to MongoDB
async function connectToDB() {
    try {
        await client.connect();
        console.log('Connected successfully to MongoDB server');
        return client.db(dbName);
    } catch (error) {
        console.error('Error connecting to MongoDB:', error);
    }
}

// Routes
app.get('/clients', async (req, res) => {
    const db = await connectToDB();
    const clients = await db.collection('clients').find({}).toArray();
    res.json(clients);
});

app.post('/clients', async (req, res) => {
    const db = await connectToDB();
    const clientData = req.body;
    await db.collection('clients').insertOne(clientData);
    res.status(201).json(clientData);
});

app.put('/clients/:id', async (req, res) => {
    const db = await connectToDB();
    const { id } = req.params;
    const updatedClient = req.body;

    await db.collection('clients').updateOne({ clientId: id }, { $set: updatedClient });
    res.json(updatedClient);
});

app.delete('/clients/:id', async (req, res) => {
    const db = await connectToDB();
    const { id } = req.params;

    await db.collection('clients').deleteOne({ clientId: id });
    res.status(204).send();
});

// Start the server
app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});
