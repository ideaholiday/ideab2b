import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './hooks/useAuth';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import ItineraryList from './pages/agent/ItineraryList';
import ItineraryBuilder from './components/itinerary/ItineraryBuilder';
import Destinations from './pages/admin/Destinations';
import Inventory from './pages/admin/Inventory';
import Bookings from './pages/operator/Bookings';
import ProtectedRoute from './components/ProtectedRoute';

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={<Login />} />
          
          <Route path="/" element={<Navigate to="/dashboard" />} />
          
          <Route path="/dashboard" element={
            <ProtectedRoute>
              <Dashboard />
            </ProtectedRoute>
          } />

          {/* Admin Routes */}
          <Route path="/destinations" element={
            <ProtectedRoute roles={['admin', 'staff']}>
              <Destinations />
            </ProtectedRoute>
          } />

          <Route path="/inventory" element={
            <ProtectedRoute roles={['admin', 'staff']}>
              <Inventory />
            </ProtectedRoute>
          } />

          {/* Agent Routes */}
          <Route path="/itineraries" element={
            <ProtectedRoute roles={['agent']}>
              <ItineraryList />
            </ProtectedRoute>
          } />

          <Route path="/itineraries/new" element={
            <ProtectedRoute roles={['agent']}>
              <ItineraryBuilder />
            </ProtectedRoute>
          } />

          {/* Operator Routes */}
          <Route path="/bookings" element={
            <ProtectedRoute roles={['operator']}>
              <Bookings />
            </ProtectedRoute>
          } />

          <Route path="*" element={<Navigate to="/dashboard" />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App;
