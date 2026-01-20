import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import * as api from '../../services/api';

export default function Bookings() {
  const [bookings, setBookings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [filter, setFilter] = useState('all');

  useEffect(() => {
    loadBookings();
  }, []);

  const loadBookings = async () => {
    try {
      setLoading(true);
      const res = await api.getBookings();
      setBookings(res.data.data || []);
      setError('');
    } catch (err) {
      setError('Failed to load bookings');
      setBookings([]);
    } finally {
      setLoading(false);
    }
  };

  const handleConfirmBooking = async (bookingId) => {
    try {
      await api.confirmBooking(bookingId);
      loadBookings();
    } catch (err) {
      setError('Failed to confirm booking');
    }
  };

  const handleCancelBooking = async (bookingId) => {
    if (!window.confirm('Cancel this booking?')) return;
    try {
      await api.cancelBooking(bookingId);
      loadBookings();
    } catch (err) {
      setError('Failed to cancel booking');
    }
  };

  const filteredBookings = filter === 'all' 
    ? bookings 
    : bookings.filter(b => b.status === filter);

  const statusColor = (status) => {
    switch(status) {
      case 'confirmed': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'cancelled': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-6xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl font-bold">My Assigned Bookings</h2>
          <Link
            to="/dashboard"
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
          >
            ← Back to Dashboard
          </Link>
        </div>

        {error && <div className="bg-red-100 text-red-700 p-3 rounded mb-4">{error}</div>}

        {/* Filter */}
        <div className="bg-white rounded-lg shadow p-4 mb-6 flex gap-2">
          <button
            onClick={() => setFilter('all')}
            className={`px-4 py-2 rounded transition ${
              filter === 'all' 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
            }`}
          >
            All Bookings
          </button>
          <button
            onClick={() => setFilter('confirmed')}
            className={`px-4 py-2 rounded transition ${
              filter === 'confirmed' 
                ? 'bg-green-600 text-white' 
                : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
            }`}
          >
            Confirmed
          </button>
          <button
            onClick={() => setFilter('pending')}
            className={`px-4 py-2 rounded transition ${
              filter === 'pending' 
                ? 'bg-yellow-600 text-white' 
                : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
            }`}
          >
            Pending
          </button>
        </div>

        {loading ? (
          <div className="text-center py-10">
            <p>Loading bookings...</p>
          </div>
        ) : filteredBookings.length === 0 ? (
          <div className="bg-white rounded-lg shadow p-8 text-center">
            <p className="text-gray-500 mb-4">No bookings assigned yet</p>
            <Link
              to="/dashboard"
              className="text-blue-600 hover:text-blue-700 font-semibold"
            >
              Back to Dashboard
            </Link>
          </div>
        ) : (
          <div className="space-y-4">
            {filteredBookings.map(booking => (
              <div key={booking.id} className="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <div className="flex justify-between items-start mb-4">
                  <div>
                    <h3 className="text-lg font-bold">{booking.itinerary_name}</h3>
                    <p className="text-gray-600">Guest: <span className="font-semibold">{booking.guest_name}</span></p>
                  </div>
                  <span className={`px-3 py-1 rounded text-sm font-semibold ${statusColor(booking.status)}`}>
                    {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                  </span>
                </div>

                <div className="grid grid-cols-4 gap-4 mb-4 text-sm">
                  <div>
                    <p className="text-gray-500">Destination</p>
                    <p className="font-semibold">{booking.destination}</p>
                  </div>
                  <div>
                    <p className="text-gray-500">Dates</p>
                    <p className="font-semibold">
                      {new Date(booking.start_date).toLocaleDateString()} - 
                      {new Date(booking.end_date).toLocaleDateString()}
                    </p>
                  </div>
                  <div>
                    <p className="text-gray-500">Items</p>
                    <p className="font-semibold">{booking.items || 0} items</p>
                  </div>
                  <div>
                    <p className="text-gray-500">Total Price</p>
                    <p className="font-semibold text-green-600">₹{booking.total_price || 0}</p>
                  </div>
                </div>

                <div className="flex gap-3">
                  <Link to={`/bookings/${booking.id}`} className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
                    View Details
                  </Link>
                  {booking.status === 'pending' && (
                    <button 
                      onClick={() => handleConfirmBooking(booking.id)}
                      className="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm"
                    >
                      Confirm
                    </button>
                  )}
                  {booking.status !== 'cancelled' && (
                    <button 
                      onClick={() => handleCancelBooking(booking.id)}
                      className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm"
                    >
                      Cancel
                    </button>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
