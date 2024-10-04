const mongoose = require('mongoose');

const clientSchema = new mongoose.Schema({
  clientId: { type: String, required: true, unique: true },
  clientName: { type: String, required: true },
  email: { type: String, required: true },
  phoneNumber: { type: String, required: true },
  company: { type: String },
  industry: { type: String },
  dateOfFirstContact: { type: Date, default: Date.now },
  status: { type: String, default: 'Active' }
});

const Client = mongoose.model('Client', clientSchema);

module.exports = Client;
