document.addEventListener('DOMContentLoaded', async () => {
    const response = await fetch('http://localhost:3000/clients');
    const clients = await response.json();
  
    const tableBody = document.querySelector('#clientsTable tbody');
    
    clients.forEach(client => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${client.clientId}</td>
        <td>${client.clientName}</td>
        <td>${client.email}</td>
        <td>${client.phoneNumber}</td>
        <td>${client.company}</td>
        <td>${client.industry}</td>
        <td>${new Date(client.dateOfFirstContact).toLocaleDateString()}</td>
        <td>${client.status}</td>
      `;
      tableBody.appendChild(row);
    });
  });
  