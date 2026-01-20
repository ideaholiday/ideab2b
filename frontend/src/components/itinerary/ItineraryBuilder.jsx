import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { 
  getDestinations, 
  getCitiesByDestination,
  createItinerary,
  getSightseeingsByCity,
  getHotelsByCity,
  addItineraryItem
} from '../../services/api';

export default function ItineraryBuilder() {
  const navigate = useNavigate();
  const [step, setStep] = useState(1);
  const [destinations, setDestinations] = useState([]);
  const [cities, setCities] = useState([]);
  const [hotels, setHotels] = useState([]);
  const [sightseeings, setSightseeings] = useState([]);
  
  const [formData, setFormData] = useState({
    destination_id: '',
    city_id: '',
    client_name: '',
    client_email: '',
    client_phone: '',
    start_date: '',
    end_date: '',
    pax_count: 1,
  });

  const [itinerary, setItinerary] = useState(null);
  const [selectedItems, setSelectedItems] = useState([]);

  useEffect(() => {
    loadDestinations();
  }, []);

  const loadDestinations = async () => {
    const res = await getDestinations();
    setDestinations(res.data);
  };

  const handleDestinationChange = async (destinationId) => {
    setFormData({ ...formData, destination_id: destinationId, city_id: '' });
    const res = await getCitiesByDestination(destinationId);
    setCities(res.data);
  };

  const handleCityChange = async (cityId) => {
    setFormData({ ...formData, city_id: cityId });
    const [hotelsRes, sightseeingsRes] = await Promise.all([
      getHotelsByCity(cityId),
      getSightseeingsByCity(cityId)
    ]);
    setHotels(hotelsRes.data);
    setSightseeings(sightseeingsRes.data);
  };

  const createBasicItinerary = async () => {
    const res = await createItinerary(formData);
    setItinerary(res.data);
    setStep(2);
  };

  const addItem = async (type, itemId, dayNumber) => {
    const itemData = {
      day_number: dayNumber,
      item_type: type,
      hotel_id: type === 'hotel' ? itemId : null,
      sightseeing_id: type === 'sightseeing' || type === 'transfer' ? itemId : null,
      order: selectedItems.filter(i => i.day_number === dayNumber).length
    };

    await addItineraryItem(itinerary.id, itemData);
    setSelectedItems([...selectedItems, itemData]);
  };

  const getTotalDays = () => {
    if (!formData.start_date || !formData.end_date) return 0;
    const start = new Date(formData.start_date);
    const end = new Date(formData.end_date);
    return Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
  };

  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <h2 className="text-2xl font-bold mb-6">Create Itinerary</h2>

        {step === 1 && (
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium mb-2">Destination</label>
              <select
                value={formData.destination_id}
                onChange={(e) => handleDestinationChange(e.target.value)}
                className="w-full border rounded px-3 py-2"
              >
                <option value="">Select Destination</option>
                {destinations.map(d => (
                  <option key={d.id} value={d.id}>{d.name}, {d.country}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium mb-2">City</label>
              <select
                value={formData.city_id}
                onChange={(e) => handleCityChange(e.target.value)}
                className="w-full border rounded px-3 py-2"
                disabled={!formData.destination_id}
              >
                <option value="">Select City</option>
                {cities.map(c => (
                  <option key={c.id} value={c.id}>{c.name}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium mb-2">Client Name</label>
              <input
                type="text"
                value={formData.client_name}
                onChange={(e) => setFormData({ ...formData, client_name: e.target.value })}
                className="w-full border rounded px-3 py-2"
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium mb-2">Start Date</label>
                <input
                  type="date"
                  value={formData.start_date}
                  onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
                  className="w-full border rounded px-3 py-2"
                />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2">End Date</label>
                <input
                  type="date"
                  value={formData.end_date}
                  onChange={(e) => setFormData({ ...formData, end_date: e.target.value })}
                  className="w-full border rounded px-3 py-2"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium mb-2">Number of Passengers</label>
              <input
                type="number"
                min="1"
                value={formData.pax_count}
                onChange={(e) => setFormData({ ...formData, pax_count: parseInt(e.target.value) })}
                className="w-full border rounded px-3 py-2"
              />
            </div>

            <button
              onClick={createBasicItinerary}
              className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
            >
              Continue to Build Itinerary
            </button>
          </div>
        )}

        {step === 2 && (
          <div className="space-y-6">
            <div className="bg-gray-50 p-4 rounded">
              <h3 className="font-bold mb-2">{formData.client_name}</h3>
              <p>{getTotalDays()} days trip</p>
            </div>

            {Array.from({ length: getTotalDays() }, (_, i) => i + 1).map(day => (
              <div key={day} className="border rounded p-4">
                <h4 className="font-bold mb-3">Day {day}</h4>
                
                <div className="space-y-3">
                  <div>
                    <label className="block text-sm font-medium mb-2">Select Hotel</label>
                    <select
                      onChange={(e) => e.target.value && addItem('hotel', parseInt(e.target.value), day)}
                      className="w-full border rounded px-3 py-2"
                    >
                      <option value="">Choose hotel (optional)</option>
                      {hotels.map(h => (
                        <option key={h.id} value={h.id}>
                          {h.name} ({h.category})
                        </option>
                      ))}
                    </select>
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Add Sightseeing</label>
                    <select
                      onChange={(e) => e.target.value && addItem('sightseeing', parseInt(e.target.value), day)}
                      className="w-full border rounded px-3 py-2"
                    >
                      <option value="">Choose sightseeing</option>
                      {sightseeings.filter(s => s.type === 'sightseeing').map(s => (
                        <option key={s.id} value={s.id}>
                          {s.name} - {s.duration}
                        </option>
                      ))}
                    </select>
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Add Transfer</label>
                    <select
                      onChange={(e) => e.target.value && addItem('transfer', parseInt(e.target.value), day)}
                      className="w-full border rounded px-3 py-2"
                    >
                      <option value="">Choose transfer</option>
                      {sightseeings.filter(s => s.type === 'transfer').map(s => (
                        <option key={s.id} value={s.id}>
                          {s.name}
                        </option>
                      ))}
                    </select>
                  </div>
                </div>

                <div className="mt-3 text-sm text-gray-600">
                  {selectedItems.filter(i => i.day_number === day).length} items added
                </div>
              </div>
            ))}

            <div className="flex gap-4">
              <button
                onClick={() => navigate('/itineraries')}
                className="flex-1 bg-green-600 text-white py-2 rounded hover:bg-green-700"
              >
                Save & Continue
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
