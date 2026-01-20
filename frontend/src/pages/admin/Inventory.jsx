import { Link } from 'react-router-dom';

export default function Inventory() {
  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-6xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl font-bold">Manage Sightseeing & Transfers</h2>
          <Link
            to="/"
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Back to Dashboard
          </Link>
        </div>

        <div className="bg-white rounded-lg shadow p-8">
          <p className="text-gray-600">Sightseeing and transfer inventory management.</p>
        </div>
      </div>
    </div>
  );
}
