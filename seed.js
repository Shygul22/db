// seed.js
const { MongoClient } = require('mongodb');

// Connection URL
const url = 'mongodb://localhost:27017';
const client = new MongoClient(url);
const dbName = 'consultingDB';

async function run() {
    try {
        await client.connect();
        console.log('Connected to MongoDB');

        const db = client.db(dbName);
        const clients = [
            {
                clientId: 'c001',
                clientName: 'John Doe',
                email: 'john@example.com',
                phoneNumber: '1234567890',
                company: 'Doe Enterprises',
                industry: 'Technology',
                dateOfFirstContact: new Date('2023-05-10'),
                status: 'Active',
            },
            {
                clientId: 'c002',
                clientName: 'Jane Smith',
                email: 'jane@example.com',
                phoneNumber: '0987654321',
                company: 'Smith Solutions',
                industry: 'Finance',
                dateOfFirstContact: new Date('2023-03-15'),
                status: 'Inactive',
            },
            {
                clientId: 'c003',
                clientName: 'Alice Johnson',
                email: 'alice@example.com',
                phoneNumber: '1230984567',
                company: 'Johnson & Co',
                industry: 'Healthcare',
                dateOfFirstContact: new Date('2023-04-20'),
                status: 'Active',
            }
        ];

        const result = await db.collection('clients').insertMany(clients);
        console.log(`${result.insertedCount} clients inserted`);

    } catch (error) {
        console.error('Error inserting data:', error);
    } finally {
        await client.close();
    }
}

run().catch(console.dir);
