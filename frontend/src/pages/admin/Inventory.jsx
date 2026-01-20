import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';

export default function Inventory() {
  const navigate = useNavigate();
  const [cities, setCities] = useState([]);
  const [sightseeings, setSightseeings] = useState([]);
  const [hotels, setHotels] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('sightseeing');
  const [selectedCity, setSelectedCity] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [sightForm, setSightForm] = useState({ name: '', description: '', price: 0, city_id: null });
  const [hotelForm, setHotelForm] = useState({ name: '', description: '', price: 0, city_id: null });
  const [error, setError] = useState('');

  useEffect(() => {
    fetchAllData();
  }, []);

  const fetchAllData = async () => {
    try {
      setLoading(true);
      const [citiesRes, sightRes, hotelsRes] = await Promise.all([
        api.get('/cities'),
        api.get('/sightseeings'),
        api.get('/hotels'),
      ]);
      setCities(citiesRes.data.data || []);
      setSightseeings(sightRes.data.data || []);
      setHotels(hotelsRes.data.data || []);
    } catch (err) {
      setError('Failed to load data');
    } finally {
      setLoading(false);
    }
  };

  const handleAddSightseeing = async (e) => {
    e.preventDefault();
    if (!selectedCity) {
      setError('Please select a city');
      return;
    }
    try {
      const response = await api.post('/sightseeings', {
        ...sightForm,
        city_id: selectedCity.id,
      });
      setSightseeings([...sightseeings, response.data.data]);
      setSightForm({ name: '', description: '', price: 0, city_id: null });
      setShowForm(false);
      setError('');
    } catch (err) {
      setError('Failed to create sightseeing');
    }
  };

  const handleAddHotel = async (e) => {
    e.preventDefault();
    if (!selectedCity) {
      setError('Please select a city');
      return;
    }
    try {
      const response = await api.post('/hotels', {
        ...hotelForm,
        city_id: selectedCity.id,
      });
      setHotels([...hotels, response.data.data]);
      setHotelForm({ name: '', description: '', price: 0, city_id: null });
      setShowForm(false);
      setError('');
    } catch (err) {
      setError('Failed to create hotel');
    }
  };

  if (loading) return <div className="p-8 text-center text-lg">Loading...</div>;

  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-3xl font-bold">Inventory Management</h1>
          <button
            onClick={() => navigate(-1)}
            className="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"
          >
            ← Back
          </button>
        </div>

        {error && <div className="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{error}</div>}

        {/* Tabs */}
        <div className="flex gap-2 mb-6">
          <button
            onClick={() => { setActiveTab('sightseeing'); setShowForm(false); }}
            className={`px-6 py-2 rounded font-semibold transition ${
              activeTab === 'sightseeing'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 border'
            }`}
          >
            Sightseeing & Transfers
          </button>
          <button
            onClick={() => { setActiveTab('hotel'); setShowForm(false); }}
            className={`px-6 py-2 rounded font-semibold transition ${
              activeTab === 'hotel'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 border'
            }`}
          >
            Hotels
          </button>
        </div>

        {/* City Selector */}
        <div className="bg-white rounded-lg shadow p-4 mb-6">
          <label className="block text-sm font-semibold mb-2">Select City:</label>
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
            {cities.map(city => (
              <button
                key={city.id}
                onClick={() => {
                  setSelectedCity(city);
                  setShowForm(false);
                }}
                className={`p-3 rounded transition ${
                  selectedCity?.id === city.id
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 hover:bg-gray-200'
                }`}
              >
                {city.name}
              </button>
            ))}
          </div>
        </div>

        {/* Content */}
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-2xl font-bold">
              {activeTab === 'sightseeing' ? 'Sightseeing & Transfers' : 'Hotels'}
              {selectedCity && ` in ${selectedCity.name}`}
            </h2>
            <button
              onClick={() => setShowForm(!showForm)}
              disabled={!selectedCity}
              className={`px-4 py-2 rounded ${
                selectedCity
                  ? 'bg-green-600 text-white hover:bg-green-700'
                  : 'bg-gray-400 text-gray-700 cursor-not-allowed'
              }`}
            >
              {showForm ? 'Cancel' : '+Add ' + (activeTab === 'sightseeing' ? 'Sightseeing' : 'Hotel')}
            </button>
          </div>

          {showForm && selectedCity && (
            <form onSubmit={activeTab === 'sightseeing' ? handleAddSightseeing : handleAddHotel} className="mb-6 p-4 bg-gray-100 rounded">
              <p className="mb-3 font-semibold">Adding to: <span className="text-blue-600">{selectedCity.name}</span></p>
              <input
                type="text"
                placeholder="Name"
                value={activeTab === 'sightseeing' ? sightForm.name : hotelForm.name}
                onChange={(e) => {
                  if (activeTab === 'sightseeing') {
                    setSightForm({ ...sightForm, name: e.target.value });
                  } else {
                    setHotelForm({ ...hotelForm, name: e.target.value });
                  }
                }}
                className="w-full border rounded px-3 py-2 mb-3"
                required
              />
              <textarea
                placeholder="Description"
                value={activeTab === 'sightseeing' ? sightForm.description : hotelForm.description}
                onChange={(e) => {
                  if (activeTab === 'sightseeing') {
                    setSightForm({ ...sightForm, description: e.target.value });
                  } else {
                    setHotelForm({ ...hotelForm, description: e.target.value });
                  }
                }}
                className="w-full border rounded px-3 py-2 mb-3"
                rows="3"
              />
              <input
                type="number"
                placeholder="Price"
                value={activeTab === 'sightseeing' ? sightForm.price : hotelForm.price}
                onChange={(e) => {
                  const price = parseFloat(e.target.value) || 0;
                  if (activeTab === 'sightseeing') {
                    setSightForm({ ...sightForm, price });
                  } else {
                    setHotelForm({ ...hotelForm, price });
                  }
                }}
                className="w-full border rounded px-3 py-2 mb-3"
                min="0"
                step="0.01"
              />
              <button
                type="submit"
                className="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"
              >
                Create {activeTab === 'sightseeing' ? 'Sightseeing' : 'Hotel'}
              </button>
            </form>
          )}

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {activeTab === 'sightseeing' ? (
              sightseeings
                .filter(s => !selectedCity || s.city_id === selectedCity.id)
                .map(sight => (
                  <div key={sight.id} className="border rounded p-4 bg-gradient-to-br from-orange-50 to-yellow-50">
                    <h3 className="font-bold text-lg">{sight.name}</h3>
                    <p className="text-gray-600 text-sm mb-2">{sight.description}</p>
                    <p className="text-lg font-semibold text-green-600">₹{sight.price}</p>
                  </div>
                ))
            ) : (
              hotels
                .filter(h => !selectedCity || h.city_id === selectedCity.id)
                .map(hotel => (
                  <div key={hotel.id} className="border rounded p-4 bg-gradient-to-br from-blue-50 to-cyan-50">
                    <h3 className="font-bold text-lg">{hotel.name}</h3>
                    <p className="text-gray-600 text-sm mb-2">{hotel.description}</p>
                    <p className="text-lg font-semibold text-green-600">₹{hotel.price}/night</p>
                  </div>
                ))
            )}
          </div>

          {(activeTab === 'sightseeing' ? sightseeings : hotels).filter(
            item => !selectedCity || item.city_id === selectedCity.id
          ).length === 0 && (
            <div className="text-center py-12 text-gray-500">
              {selectedCity ? `No ${activeTab === 'sightseeing' ? 'sightseeing' : 'hotels'} in this city.` : 'Select a city to view items.'}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
