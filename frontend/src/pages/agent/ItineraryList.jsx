import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getItineraries } from '../../services/api';

export default function ItineraryList() {
  const [itineraries, setItineraries] = useState([]);

  useEffect(() => {
    loadItineraries();
  }, []);

  const loadItineraries = async () => {
    const res = await getItineraries();
    setItineraries(res.data);
  };

  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-6xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl font-bold">My Itineraries</h2>
          <Link
            to="/itineraries/new"
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Create New
          </Link>
        </div>

        <div className="bg-white rounded-lg shadow overflow-hidden">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {itineraries.map(itinerary => (
                <tr key={itinerary.id}>
                  <td className="px-6 py-4">{itinerary.client_name}</td>
                  <td className="px-6 py-4">{itinerary.destination?.name}</td>
                  <td className="px-6 py-4">
                    {new Date(itinerary.start_date).toLocaleDateString()} - 
                    {new Date(itinerary.end_date).toLocaleDateString()}
                  </td>
                  <td className="px-6 py-4">
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
