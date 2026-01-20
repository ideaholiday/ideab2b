import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import * as api from '../../services/api';

export default function ItineraryBuilder() {
  const navigate = useNavigate();
  const [step, setStep] = useState(1);
  const [destinations, setDestinations] = useState([]);
  const [cities, setCities] = useState([]);
  const [hotels, setHotels] = useState([]);
  const [sightseeings, setSightseeings] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const [formData, setFormData] = useState({
    name: 'New Itinerary',
    destination_id: '',
    city_id: '',
    start_date: '',
    end_date: '',
    guest_name: '',
    guest_email: '',
    guest_phone: '',
    budget: '',
  });

  const [itineraryItems, setItineraryItems] = useState([]);
  const [itineraryId, setItineraryId] = useState(null);

  useEffect(() => {
    fetchInitialData();
  }, []);

  const fetchInitialData = async () => {
    try {
      const [destRes, citiesRes, hotelsRes, sightsRes] = await Promise.all([
        api.getDestinations(),
        api.getCities(),
        api.getHotels(),
        api.getSightseeings(),
      ]);
      setDestinations(destRes.data.data || []);
      setCities(citiesRes.data.data || []);
      setHotels(hotelsRes.data.data || []);
      setSightseeings(sightsRes.data.data || []);
    } catch (err) {
      setError('Failed to load data');
    }
  };

  const handleDestinationChange = (destId) => {
    setFormData({ ...formData, destination_id: destId, city_id: '' });
  };

  const handleCreateItinerary = async () => {
    if (!formData.destination_id || !formData.start_date || !formData.end_date) {
      setError('Please fill in destination, start date, and end date');
      return;
    }

    setLoading(true);
    try {
      const response = await api.createItinerary({
        name: formData.name,
        destination_id: formData.destination_id,
        start_date: formData.start_date,
        end_date: formData.end_date,
      });
      setItineraryId(response.data.data.id);
      setStep(2);
      setError('');
    } catch (err) {
      setError('Failed to create itinerary');
    } finally {
      setLoading(false);
    }
  };

  const handleAddItem = async (type, itemId, price) => {
    const newItem = { type, item_id: itemId, price };
    setItineraryItems([...itineraryItems, { ...newItem, id: Date.now() }]);

    if (itineraryId) {
      try {
        await api.addItineraryItem(itineraryId, newItem);
      } catch (err) {
        setError('Failed to add item');
      }
    }
  };

  const handleRemoveItem = (itemId) => {
    setItineraryItems(itineraryItems.filter(i => i.id !== itemId));
  };

  const handleSubmit = async () => {
    if (itineraryItems.length === 0) {
      setError('Add at least one item to the itinerary');
      return;
    }
    navigate('/itineraries');
  };

  const filteredCities = cities.filter(c => c.destination_id === parseInt(formData.destination_id));
  const filteredHotels = hotels.filter(h => !formData.city_id || h.city_id === parseInt(formData.city_id));
  const filteredSightseeings = sightseeings.filter(s => !formData.city_id || s.city_id === parseInt(formData.city_id));

  const totalPrice = itineraryItems.reduce((sum, item) => sum + item.price, 0);

  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-4xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold">Create Itinerary</h1>
          <button
            onClick={() => navigate(-1)}
            className="text-gray-600 hover:text-gray-800"
          >
            ← Back
          </button>
        </div>

        {error && <div className="bg-red-100 text-red-700 p-3 rounded mb-4">{error}</div>}

        {/* Step Indicator */}
        <div className="flex gap-4 mb-8">
          <button
            onClick={() => step === 2 && setStep(1)}
            className={`flex-1 py-2 rounded font-semibold transition ${
              step >= 1
                ? 'bg-blue-600 text-white'
                : 'bg-gray-300 text-gray-600'
            }`}
          >
            Step 1: Basic Info
          </button>
          <button
            onClick={() => step === 1 && setStep(2)}
            disabled={!itineraryId}
            className={`flex-1 py-2 rounded font-semibold transition ${
              step >= 2
                ? 'bg-blue-600 text-white'
                : 'bg-gray-300 text-gray-600'
            } ${!itineraryId ? 'opacity-50 cursor-not-allowed' : ''}`}
          >
            Step 2: Add Items
          </button>
        </div>

        {/* Step 1: Basic Info */}
        {step === 1 && (
          <div className="bg-white rounded-lg shadow p-8">
            <h2 className="text-2xl font-bold mb-6">Trip Details</h2>

            <div className="space-y-4">
              <div>
                <label className="block font-semibold mb-2">Itinerary Name</label>
                <input
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full border rounded px-3 py-2"
                />
              </div>

              <div>
                <label className="block font-semibold mb-2">Destination *</label>
                <select
                  value={formData.destination_id}
                  onChange={(e) => handleDestinationChange(e.target.value)}
                  className="w-full border rounded px-3 py-2"
                  required
                >
                  <option value="">Select Destination</option>
                  {destinations.map(d => (
                    <option key={d.id} value={d.id}>{d.name}</option>
                  ))}
                </select>
              </div>

              {formData.destination_id && (
                <div>
                  <label className="block font-semibold mb-2">City (Optional)</label>
                  <select
                    value={formData.city_id}
                    onChange={(e) => setFormData({ ...formData, city_id: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                  >
                    <option value="">Select City</option>
                    {filteredCities.map(c => (
                      <option key={c.id} value={c.id}>{c.name}</option>
                    ))}
                  </select>
                </div>
              )}

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block font-semibold mb-2">Start Date *</label>
                  <input
                    type="date"
                    value={formData.start_date}
                    onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                    required
                  />
                </div>
                <div>
                  <label className="block font-semibold mb-2">End Date *</label>
                  <input
                    type="date"
                    value={formData.end_date}
                    onChange={(e) => setFormData({ ...formData, end_date: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                    required
                  />
                </div>
              </div>

              <div className="bg-gray-100 p-4 rounded">
                <h3 className="font-bold mb-3">Guest Information (Optional)</h3>
                <div className="space-y-3">
                  <input
                    type="text"
                    placeholder="Guest Name"
                    value={formData.guest_name}
                    onChange={(e) => setFormData({ ...formData, guest_name: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                  />
                  <input
                    type="email"
                    placeholder="Guest Email"
                    value={formData.guest_email}
                    onChange={(e) => setFormData({ ...formData, guest_email: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                  />
                  <input
                    type="tel"
                    placeholder="Guest Phone"
                    value={formData.guest_phone}
                    onChange={(e) => setFormData({ ...formData, guest_phone: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                  />
                  <input
                    type="number"
                    placeholder="Budget (Optional)"
                    value={formData.budget}
                    onChange={(e) => setFormData({ ...formData, budget: e.target.value })}
                    className="w-full border rounded px-3 py-2"
                  />
                </div>
              </div>

              <button
                onClick={handleCreateItinerary}
                disabled={loading}
                className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded disabled:opacity-50 transition"
              >
                {loading ? 'Creating...' : 'Create Itinerary & Continue'}
              </button>
            </div>
          </div>
        )}

        {/* Step 2: Add Items */}
        {step === 2 && (
          <div className="space-y-6">
            {/* Selected Items */}
            {itineraryItems.length > 0 && (
              <div className="bg-white rounded-lg shadow p-6">
                <h3 className="text-xl font-bold mb-4">Selected Items ({itineraryItems.length})</h3>
                <div className="space-y-2 max-h-64 overflow-y-auto">
                  {itineraryItems.map(item => (
                    <div key={item.id} className="flex justify-between items-center p-3 bg-gray-100 rounded">
                      <div>
                        <p className="font-semibold capitalize">{item.type}</p>
                        <p className="text-sm text-gray-600">₹{item.price}</p>
                      </div>
                      <button
                        onClick={() => handleRemoveItem(item.id)}
                        className="text-red-600 hover:text-red-700 font-bold"
                      >
                        Remove
                      </button>
                    </div>
                  ))}
                </div>
                <div className="mt-4 pt-4 border-t">
                  <p className="text-xl font-bold text-green-600">
                    Total: ₹{totalPrice}
                  </p>
                </div>
              </div>
            )}

            {/* Hotels */}
            {filteredHotels.length > 0 && (
              <div className="bg-white rounded-lg shadow p-6">
                <h3 className="text-xl font-bold mb-4">🏨 Hotels</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {filteredHotels.map(hotel => (
                    <div key={hotel.id} className="border rounded p-4 hover:shadow-lg transition bg-gradient-to-br from-blue-50 to-blue-100">
                      <h4 className="font-bold">{hotel.name}</h4>
                      <p className="text-sm text-gray-600 mb-2">{hotel.description}</p>
                      <p className="text-lg font-semibold text-blue-600 mb-3">₹{hotel.price}/night</p>
                      <button
                        onClick={() => handleAddItem('hotel', hotel.id, hotel.price)}
                        className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
                      >
                        + Add Hotel
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Sightseeing */}
            {filteredSightseeings.length > 0 && (
              <div className="bg-white rounded-lg shadow p-6">
                <h3 className="text-xl font-bold mb-4">🎫 Sightseeing & Activities</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {filteredSightseeings.map(sight => (
                    <div key={sight.id} className="border rounded p-4 hover:shadow-lg transition bg-gradient-to-br from-green-50 to-green-100">
                      <h4 className="font-bold">{sight.name}</h4>
                      <p className="text-sm text-gray-600 mb-2">{sight.description}</p>
                      <p className="text-lg font-semibold text-green-600 mb-3">₹{sight.price}</p>
                      <button
                        onClick={() => handleAddItem('sightseeing', sight.id, sight.price)}
                        className="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition"
                      >
                        + Add Activity
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {filteredHotels.length === 0 && filteredSightseeings.length === 0 && (
              <div className="bg-yellow-100 text-yellow-800 p-4 rounded">
                Please select a city to view available hotels and activities.
              </div>
            )}

            {/* Action Buttons */}
            <div className="flex gap-4">
              <button
                onClick={() => setStep(1)}
                className="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded transition"
              >
                ← Back
              </button>
              <button
                onClick={handleSubmit}
                disabled={itineraryItems.length === 0}
                className="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Save Itinerary →
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
