import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import * as api from '../../services/api';

export default function Destinations() {
  const navigate = useNavigate();
  const [destinations, setDestinations] = useState([]);
  const [cities, setCities] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showDestForm, setShowDestForm] = useState(false);
  const [showCityForm, setShowCityForm] = useState(false);
  const [editingDest, setEditingDest] = useState(null);
  const [editingCity, setEditingCity] = useState(null);
  const [selectedDestination, setSelectedDestination] = useState(null);
  const [destForm, setDestForm] = useState({ name: '', description: '', country: '' });
  const [cityForm, setCityForm] = useState({ name: '', description: '' });
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  useEffect(() => {
    fetchAllData();
  }, []);

  const fetchAllData = async () => {
    try {
      setLoading(true);
      const [destRes, citiesRes] = await Promise.all([
        api.getDestinations(),
        api.getCities(),
      ]);
      setDestinations(destRes.data.data || []);
      setCities(citiesRes.data.data || []);
    } catch (err) {
      setError('Failed to load data');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleAddDestination = async (e) => {
    e.preventDefault();
    try {
      if (editingDest) {
        const response = await api.updateDestination(editingDest.id, destForm);
        setDestinations(destinations.map(d => d.id === editingDest.id ? response.data.data : d));
        setSuccess('Destination updated successfully');
        setEditingDest(null);
      } else {
        const response = await api.createDestination(destForm);
        setDestinations([...destinations, response.data.data]);
        setSuccess('Destination created successfully');
      }
      setDestForm({ name: '', description: '', country: '' });
      setShowDestForm(false);
      setError('');
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to save destination');
    }
  };

  const handleDeleteDestination = async (id) => {
    if (!window.confirm('Delete this destination?')) return;
    try {
      await api.deleteDestination(id);
      setDestinations(destinations.filter(d => d.id !== id));
      setSuccess('Destination deleted');
    } catch (err) {
      setError('Failed to delete destination');
    }
  };

  const handleAddCity = async (e) => {
    e.preventDefault();
    if (!selectedDestination) {
      setError('Please select a destination');
      return;
    }
    try {
      if (editingCity) {
        const response = await api.updateCity(editingCity.id, cityForm);
        setCities(cities.map(c => c.id === editingCity.id ? response.data.data : c));
        setSuccess('City updated successfully');
        setEditingCity(null);
      } else {
        const response = await api.createCity({
          ...cityForm,
          destination_id: selectedDestination.id,
        });
        setCities([...cities, response.data.data]);
        setSuccess('City created successfully');
      }
      setCityForm({ name: '', description: '' });
      setShowCityForm(false);
      setError('');
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to save city');
    }
  };

  const handleDeleteCity = async (id) => {
    if (!window.confirm('Delete this city?')) return;
    try {
      await api.deleteCity(id);
      setCities(cities.filter(c => c.id !== id));
      setSuccess('City deleted');
    } catch (err) {
      setError('Failed to delete city');
    }
  };

  if (loading) return <div className="p-8 text-center text-lg">Loading...</div>;

  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-3xl font-bold">Destinations & Cities</h1>
          <button
            onClick={() => navigate(-1)}
            className="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"
          >
            ← Back
          </button>
        </div>

        {error && <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{error}</div>}
        {success && <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{success}</div>}

        {/* Destinations Section */}
        <div className="bg-white rounded-lg shadow p-6 mb-8">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-2xl font-bold">Destinations</h2>
            <button
              onClick={() => {
                setShowDestForm(!showDestForm);
                setEditingDest(null);
                setDestForm({ name: '', description: '', country: '' });
              }}
              className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
              {showDestForm ? 'Cancel' : '+ Add Destination'}
            </button>
          </div>

          {showDestForm && (
            <form onSubmit={handleAddDestination} className="mb-6 p-4 bg-gray-100 rounded">
              <input
                type="text"
                placeholder="Destination Name"
                value={destForm.name}
                onChange={(e) => setDestForm({ ...destForm, name: e.target.value })}
                className="w-full border rounded px-3 py-2 mb-3"
                required
              />
              <input
                type="text"
                placeholder="Country"
                value={destForm.country}
                onChange={(e) => setDestForm({ ...destForm, country: e.target.value })}
                className="w-full border rounded px-3 py-2 mb-3"
              />
              <textarea
                placeholder="Description"
                value={destForm.description}
                onChange={(e) => setDestForm({ ...destForm, description: e.target.value })}
                className="w-full border rounded px-3 py-2 mb-3"
                rows="3"
              />
              <button
                type="submit"
                className="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"
              >
                {editingDest ? 'Update' : 'Create'} Destination
              </button>
            </form>
          )}

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {destinations.map(dest => (
              <div
                key={dest.id}
                className="border-2 rounded p-4 bg-gradient-to-br from-purple-50 to-pink-50 hover:shadow-lg transition"
              >
                <h3 className="font-bold text-xl mb-1">{dest.name}</h3>
                {dest.country && <p className="text-sm text-gray-500 mb-2">{dest.country}</p>}
                <p className="text-gray-600 text-sm mb-3">{dest.description}</p>
                <p className="text-xs text-gray-500 mb-3">
                  {cities.filter(c => c.destination_id === dest.id).length} cities
                </p>
                <div className="flex gap-2">
                  <button
                    onClick={() => {
                      setSelectedDestination(dest);
                      setShowCityForm(true);
                    }}
                    className="flex-1 bg-blue-500 text-white text-sm px-2 py-1 rounded hover:bg-blue-600"
                  >
                    View Cities
                  </button>
                  <button
                    onClick={() => {
                      setEditingDest(dest);
                      setDestForm(dest);
                      setShowDestForm(true);
                    }}
                    className="bg-yellow-500 text-white text-sm px-3 py-1 rounded hover:bg-yellow-600"
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => handleDeleteDestination(dest.id)}
                    className="bg-red-500 text-white text-sm px-3 py-1 rounded hover:bg-red-600"
                  >
                    Delete
                  </button>
                </div>
              </div>
            ))}
          </div>

          {destinations.length === 0 && (
            <div className="text-center py-12 text-gray-500">
              No destinations yet. Create one to get started!
            </div>
          )}
        </div>

        {/* Cities Section */}
        {selectedDestination && (
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-2xl font-bold">Cities in {selectedDestination.name}</h2>
              <button
                onClick={() => {
                  setSelectedDestination(null);
                  setShowCityForm(false);
                }}
                className="text-gray-500 hover:text-gray-700 text-sm font-semibold"
              >
                ✕ Close
              </button>
            </div>

            {showCityForm && (
              <form onSubmit={handleAddCity} className="mb-6 p-4 bg-gray-100 rounded">
                <input
                  type="text"
                  placeholder="City Name"
                  value={cityForm.name}
                  onChange={(e) => setCityForm({ ...cityForm, name: e.target.value })}
                  className="w-full border rounded px-3 py-2 mb-3"
                  required
                />
                <textarea
                  placeholder="Description"
                  value={cityForm.description}
                  onChange={(e) => setCityForm({ ...cityForm, description: e.target.value })}
                  className="w-full border rounded px-3 py-2 mb-3"
                  rows="3"
                />
                <div className="flex gap-2">
                  <button
                    type="submit"
                    className="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"
                  >
                    {editingCity ? 'Update' : 'Create'} City
                  </button>
                  <button
                    type="button"
                    onClick={() => {
                      setShowCityForm(false);
                      setEditingCity(null);
                      setCityForm({ name: '', description: '' });
                    }}
                    className="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500"
                  >
                    Cancel
                  </button>
                </div>
              </form>
            )}

            {!showCityForm && (
              <button
                onClick={() => setShowCityForm(true)}
                className="mb-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
              >
                + Add City
              </button>
            )}

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {cities
                .filter(c => c.destination_id === selectedDestination.id)
                .map(city => (
                  <div key={city.id} className="border rounded p-4 bg-gradient-to-br from-green-50 to-teal-50">
                    <h3 className="font-bold text-lg mb-1">{city.name}</h3>
                    <p className="text-gray-600 text-sm mb-3">{city.description}</p>
                    <div className="flex gap-2">
                      <button
                        onClick={() => {
                          setEditingCity(city);
                          setCityForm(city);
                          setShowCityForm(true);
                        }}
                        className="flex-1 bg-yellow-500 text-white text-sm px-2 py-1 rounded hover:bg-yellow-600"
                      >
                        Edit
                      </button>
                      <button
                        onClick={() => handleDeleteCity(city.id)}
                        className="flex-1 bg-red-500 text-white text-sm px-2 py-1 rounded hover:bg-red-600"
                      >
                        Delete
                      </button>
                    </div>
                  </div>
                ))}
            </div>

            {cities.filter(c => c.destination_id === selectedDestination.id).length === 0 && (
              <div className="text-center py-12 text-gray-500">
                No cities in this destination yet.
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
