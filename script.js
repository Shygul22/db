const clientsTable = document.getElementById('clientsTable').getElementsByTagName('tbody')[0];
let clients = [];
let editingClientIndex = null;

// Fetch all clients from the server on page load
async function fetchClients() {
    const response = await fetch('http://localhost:3000/api/clients');
    clients = await response.json();
    renderTable();
}

// Open modal for adding new client
function openModal() {
    document.getElementById('clientID').value = ''; // Reset client ID for new client
    document.getElementById('clientName').value = '';
    document.getElementById('email').value = '';
    document.getElementById('phoneNumber').value = '';
    document.getElementById('company').value = '';
    document.getElementById('industry').value = '';
    document.getElementById('firstContact').value = '';
    document.getElementById('status').value = 'Active';
    
    document.getElementById('clientModal').style.display = 'block';
}

// Close modal
function closeModal() {
    document.getElementById('clientModal').style.display = 'none';
    editingClientIndex = null; // Reset editing client index
}

// Save client (Add/Edit)
async function saveClient(event) {
    event.preventDefault();

    const clientData = {
        clientName: document.getElementById('clientName').value,
        email: document.getElementById('email').value,
        phoneNumber: document.getElementById('phoneNumber').value,
        company: document.getElementById('company').value,
        industry: document.getElementById('industry').value,
        firstContact: document.getElementById('firstContact').value,
        status: document.getElementById('status').value
    };

    if (editingClientIndex !== null) {
        const clientID = clients[editingClientIndex].clientID;
        const response = await fetch(`http://localhost:3000/api/clients/${clientID}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(clientData)
        });

        if (response.ok) {
            clients[editingClientIndex] = { clientID, ...clientData };
        }
    } else {
        const response = await fetch('http://localhost:3000/api/clients', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(clientData)
        });

        if (response.ok) {
            const newClient = await response.json();
            clients.push(newClient);
        }
    }

    closeModal();
    renderTable();
}

// Render clients table
function renderTable() {
    clientsTable.innerHTML = ''; // Clear current table

    clients.forEach(client => {
        const row = clientsTable.insertRow();
        row.innerHTML = `
            <td>${client.clientID}</td>
            <td>${client.clientName}</td>
            <td>${client.email}</td>
            <td>${client.phoneNumber}</td>
            <td>${client.company}</td>
            <td>${client.industry}</td>
            <td>${client.firstContact}</td>
            <td>${client.status}</td>
            <td>
                <button onclick="editClient('${client.clientID}')">Edit</button>
                <button onclick="deleteClient('${client.clientID}')">Delete</button>
            </td>
        `;
    });
}

function editClient(clientID) {
    console.log('Editing client with ID:', clientID);
    editingClientIndex = clients.findIndex(client => client.clientID === clientID);
    
    // Log the editingClientIndex and the clients array
    console.log('Editing Client Index:', editingClientIndex);
    console.log('Clients Array:', clients);

    if (editingClientIndex === -1) {
        alert('Client not found!');
        return;
    }

    const client = clients[editingClientIndex];

    document.getElementById('clientID').value = client.clientID;
    document.getElementById('clientName').value = client.clientName;
    document.getElementById('email').value = client.email;
    document.getElementById('phoneNumber').value = client.phoneNumber;
    document.getElementById('company').value = client.company;
    document.getElementById('industry').value = client.industry;
    document.getElementById('firstContact').value = client.firstContact;
    document.getElementById('status').value = client.status;

    openModal();
}


// Delete client
async function deleteClient(clientID) {
    const response = await fetch(`http://localhost:3000/api/clients/${clientID}`, {
        method: 'DELETE'
    });

    if (response.ok) {
        clients = clients.filter(client => client.clientID !== clientID);
        renderTable();
    }
}

// Search clients
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const rows = clientsTable.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;

        for (let j = 0; j < cells.length; j++) {
            if (cells[j].innerText.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }
        rows[i].style.display = found ? '' : 'none';
    }
}

// Fetch clients on page load
fetchClients();
