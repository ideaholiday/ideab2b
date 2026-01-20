import { useState, useEffect } from 'react';
import api from '../services/api';
import { useAuth } from '../hooks/useAuth';

export default function Agents() {
  const { user } = useAuth();
  const [agents, setAgents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({ name: '', email: '', role: 'agent' });
  const [error, setError] = useState('');

  useEffect(() => {
    fetchAgents();
  }, []);

  const fetchAgents = async () => {
    try {
      const response = await api.get('/users');
      setAgents(response.data.data.filter(u => u.role !== 'admin'));
    } catch (err) {
      setError('Failed to load agents');
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await api.post('/users', formData);
      setAgents([...agents, response.data.data]);
      setFormData({ name: '', email: '', role: 'agent' });
      setShowForm(false);
      setError('');
    } catch (err) {
      setError('Failed to create agent');
    }
  };

  if (loading) return <div className="p-8 text-center">Loading...</div>;

  return (
    <div className="bg-white rounded-lg shadow p-6">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-bold">Agents & Team Members</h2>
        {user?.role === 'admin' && (
          <button
            onClick={() => setShowForm(!showForm)}
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            {showForm ? 'Cancel' : 'Add New Agent'}
          </button>
        )}
      </div>

      {error && <div className="bg-red-100 text-red-700 p-3 rounded mb-4">{error}</div>}

      {showForm && (
        <form onSubmit={handleSubmit} className="mb-6 p-4 bg-gray-100 rounded">
          <div className="grid grid-cols-3 gap-4">
            <input
              type="text"
              placeholder="Name"
              value={formData.name}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
              className="border rounded px-3 py-2"
              required
            />
            <input
              type="email"
              placeholder="Email"
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
              className="border rounded px-3 py-2"
              required
            />
            <select
              value={formData.role}
              onChange={(e) => setFormData({ ...formData, role: e.target.value })}
              className="border rounded px-3 py-2"
            >
              <option value="agent">Agent</option>
              <option value="operator">Operator</option>
              <option value="staff">Staff</option>
            </select>
          </div>
          <button
            type="submit"
            className="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          >
            Create Agent
          </button>
        </form>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {agents.map(agent => (
          <div key={agent.id} className="border rounded p-4 bg-gradient-to-br from-blue-50 to-indigo-50">
            <h3 className="font-bold text-lg">{agent.name}</h3>
            <p className="text-gray-600">{agent.email}</p>
            <span className="inline-block mt-2 px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-sm font-semibold">
              {agent.role}
            </span>
          </div>
        ))}
      </div>

      {agents.length === 0 && (
        <div className="text-center py-12 text-gray-500">
          No agents created yet. Click "Add New Agent" to create one.
        </div>
      )}
    </div>
  );
}
