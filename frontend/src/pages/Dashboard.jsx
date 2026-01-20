import { useAuth } from '../hooks/useAuth';
import { useNavigate } from 'react-router-dom';
import { useEffect } from 'react';

export default function Dashboard() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!user) {
      navigate('/login');
    }
  }, [user, navigate]);

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const getRoleDashboard = () => {
    if (!user) return null;

    const menuItems = {
      admin: [
        { label: 'Manage Agents', path: '/agents', color: 'blue' },
        { label: 'Manage Destinations & Cities', path: '/destinations', color: 'purple' },
        { label: 'Manage Inventory', path: '/inventory', color: 'indigo' },
        { label: 'View All Bookings', path: '/bookings', color: 'pink' },
      ],
      staff: [
        { label: 'Manage Destinations & Cities', path: '/destinations', color: 'purple' },
        { label: 'Manage Inventory', path: '/inventory', color: 'indigo' },
        { label: 'View All Bookings', path: '/bookings', color: 'pink' },
      ],
      agent: [
        { label: 'Create New Itinerary', path: '/itineraries/new', color: 'green', action: true },
        { label: 'My Itineraries', path: '/itineraries', color: 'blue' },
      ],
      operator: [
        { label: 'Assigned Bookings', path: '/bookings', color: 'orange' },
      ],
      hotel_partner: [
        { label: 'My Hotels', path: '/my-hotels', color: 'cyan' },
      ],
    };

    const items = menuItems[user.role] || [];

    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {items.map((item, idx) => (
          <button
            key={idx}
            onClick={() => navigate(item.path)}
            className={`p-6 bg-gradient-to-br from-${item.color}-50 to-${item.color}-100 border-2 border-${item.color}-200 rounded-lg hover:shadow-lg transition transform hover:scale-105`}
          >
            <div className={`text-${item.color}-700 font-bold text-lg`}>{item.label}</div>
            <div className={`text-${item.color}-600 text-sm mt-2`}>
              {item.action ? '→ Start Now' : '→ View'}
            </div>
          </button>
        ))}
      </div>
    );
  };

  if (!user) return null;

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Navigation Bar */}
      <nav className="bg-white shadow-lg border-b-4 border-blue-600">
        <div className="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold text-gray-800">IdeaB2B Travel Platform</h1>
            <p className="text-sm text-gray-500">{user.name}</p>
          </div>
          <div className="flex items-center gap-4">
            <div className="text-right">
              <p className="font-semibold text-gray-700">{user.name}</p>
              <p className="text-xs uppercase tracking-wide text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                {user.role}
              </p>
            </div>
            <button
              onClick={handleLogout}
              className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition"
            >
              Logout
            </button>
          </div>
        </div>
      </nav>

      {/* Main Content */}
      <div className="max-w-7xl mx-auto px-6 py-12">
        <div className="mb-8">
          <h2 className="text-3xl font-bold text-gray-800 mb-2">Welcome, {user.name}!</h2>
          <p className="text-gray-600">
            {user.role === 'admin' && 'Manage the platform, agents, destinations, and inventory.'}
            {user.role === 'staff' && 'Manage destinations, inventory, and view bookings.'}
            {user.role === 'agent' && 'Create and manage travel itineraries for your clients.'}
            {user.role === 'operator' && 'View and manage your assigned bookings.'}
            {user.role === 'hotel_partner' && 'Manage your hotel properties and bookings.'}
          </p>
        </div>

        {getRoleDashboard()}

        {/* Quick Stats */}
        <div className="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div className="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
            <h3 className="text-gray-600 text-sm font-semibold uppercase">Users</h3>
            <p className="text-3xl font-bold text-blue-600 mt-2">--</p>
          </div>
          <div className="bg-white rounded-lg shadow p-6 border-l-4 border-green-600">
            <h3 className="text-gray-600 text-sm font-semibold uppercase">Itineraries</h3>
            <p className="text-3xl font-bold text-green-600 mt-2">--</p>
          </div>
          <div className="bg-white rounded-lg shadow p-6 border-l-4 border-purple-600">
            <h3 className="text-gray-600 text-sm font-semibold uppercase">Bookings</h3>
            <p className="text-3xl font-bold text-purple-600 mt-2">--</p>
          </div>
          <div className="bg-white rounded-lg shadow p-6 border-l-4 border-orange-600">
            <h3 className="text-gray-600 text-sm font-semibold uppercase">Revenue</h3>
            <p className="text-3xl font-bold text-orange-600 mt-2">₹0</p>
          </div>
        </div>
      </div>
    </div>
  );
}
