import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import * as api from '../../services/api';

export default function ItineraryList() {
  const [itineraries, setItineraries] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    loadItineraries();
  }, []);

  const loadItineraries = async () => {
    try {
      setLoading(true);
      const res = await api.getItineraries();
      setItineraries(res.data.data || []);
      setError('');
    } catch (err) {
      setError('Failed to load itineraries');
      setItineraries([]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-6xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl font-bold">My Itineraries</h2>
          <Link
            to="/itineraries/new"
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
          >
            + Create New
          </Link>
        </div>

        {error && <div className="bg-red-100 text-red-700 p-3 rounded mb-4">{error}</div>}

        {loading ? (
          <div className="text-center py-10">
            <p>Loading itineraries...</p>
          </div>
        ) : itineraries.length === 0 ? (
          <div className="bg-white rounded-lg shadow p-8 text-center">
            <p className="text-gray-500 mb-4">No itineraries created yet</p>
            <Link
              to="/itineraries/new"
              className="text-blue-600 hover:text-blue-700 font-semibold"
            >
              Create your first itinerary
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {itineraries.map(itinerary => (
              <div key={itinerary.id} className="bg-white rounded-lg shadow hover:shadow-lg transition">
                <div className="p-6">
                  <h3 className="text-lg font-bold mb-2">{itinerary.name}</h3>
                  
                  {itinerary.destination_id && (
                    <p className="text-gray-600 mb-2">
                      <span className="font-semibold">Destination:</span> Destination #{itinerary.destination_id}
                    </p>
                  )}
                  
                  <p className="text-gray-600 mb-2">
                    <span className="font-semibold">Dates:</span> {new Date(itinerary.start_date).toLocaleDateString()} - {new Date(itinerary.end_date).toLocaleDateString()}
                  </p>

            <p>Loading itineraries...</p>
          </div>
        ) : itineraries.length === 0 ? (
          <div className="bg-white rounded-lg shadow p-8 text-center">
            <p className="text-gray-500 mb-4">No itineraries created yet</p>
            <Link
              to="/itineraries/new"
              className="text-blue-600 hover:text-blue-700 font-semibold"
            >
              Create your first itinerary
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {itineraries.map(itinerary => (
              <div key={itinerary.id} className="bg-white rounded-lg shadow hover:shadow-lg transition">
                <div className="p-6">
                  <h3 className="text-lg font-bold mb-2">{itinerary.name}</h3>
                  
                  {itinerary.destination_id && (
                    <p className="text-gray-600 mb-2">
                      <span className="font-semibold">Destination:</span> Destination #{itinerary.destination_id}
                    </p>
                  )}
                  
                  <p className="text-gray-600 mb-2">
                    <span className="font-semibold">Dates:</span> {new Date(itinerary.start_date).toLocaleDateString()} - {new Date(itinerary.end_date).toLocaleDateString()}
                  </p>

                  <p className="text-sm text-gray-500 mb-4">
                    Created: {new Date(itinerary.created_at || Date.now()).toLocaleDateString()}
                  </p>

                  <div className="flex gap-2">
                    <Link
                      to={`/itineraries/${itinerary.id}`}
                      className="flex-1 bg-blue-600 text-white py-2 px-3 rounded text-center hover:bg-blue-700 transition text-sm"
                    >
                      View
                    </Link>
                    <Link
                      to={`/itineraries/${itinerary.id}/edit`}
                      className="flex-1 bg-gray-600 text-white py-2 px-3 rounded text-center hover:bg-gray-700 transition text-sm"
                    >
                      Edit
                    </Link>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
                    <span className={`px-2 py-1 text-xs rounded ${
                      itinerary.status === 'booked' ? 'bg-green-100 text-green-800' :
                      itinerary.status === 'quoted' ? 'bg-blue-100 text-blue-800' :
                      'bg-gray-100 text-gray-800'
                    }`}>
                      {itinerary.status}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    <Link
                      to={`/itineraries/${itinerary.id}`}
                      className="text-blue-600 hover:text-blue-800"
                    >
                      View
                    </Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
