import { useAuth } from '../hooks/useAuth';
import { Link } from 'react-router-dom';

export default function Dashboard() {
  const { user, logout } = useAuth();

  const getRoleDashboard = () => {
    switch (user.role) {
      case 'admin':
      case 'staff':
        return (
          <div className="space-y-4">
            <Link to="/destinations" className="block p-4 bg-white rounded shadow hover:bg-gray-50">
              Manage Destinations & Cities
            </Link>
            <Link to="/inventory" className="block p-4 bg-white rounded shadow hover:bg-gray-50">
              Manage Sightseeing & Transfers
            </Link>
            <Link to="/bookings" className="block p-4 bg-white rounded shadow hover:bg-gray-50">
              View All Bookings
            </Link>
          </div>
        );
      case 'agent':
        return (
          <div className="space-y-4">
            <Link to="/itineraries/new" className="block p-4 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
              Create New Itinerary
            </Link>
            <Link to="/itineraries" className="block p-4 bg-white rounded shadow hover:bg-gray-50">
              My Itineraries
            </Link>
          </div>
        );
      case 'hotel_partner':
        return (
          <div className="space-y-4">
            <Link to="/my-hotels" className="block p-4 bg-white rounded shadow hover:bg-gray-50">
              My Hotels
            </Link>
          </div>
        );
      case 'operator':
        return (
          <div className="space-y-4">
            <Link to="/my-bookings" className="block p-4 bg-white rounded shadow hover:bg-gray-50">
              Assigned Bookings
            </Link>
          </div>
        );
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
          <h1 className="text-xl font-bold">Travel Platform</h1>
          <div className="flex items-center gap-4">
            <span>{user.name} ({user.role})</span>
            <button onClick={logout} className="text-red-600 hover:text-red-700">
              Logout
            </button>
          </div>
        </div>
      </nav>
      <div className="max-w-7xl mx-auto px-4 py-8">
        <h2 className="text-2xl font-bold mb-6">Dashboard</h2>
        {getRoleDashboard()}
      </div>
    </div>
  );
}
