import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './hooks/useAuth';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import ItineraryList from './pages/agent/ItineraryList';
import ItineraryBuilder from './components/itinerary/ItineraryBuilder';
import ProtectedRoute from './components/ProtectedRoute';

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={<Login />} />
          
          <Route path="/" element={
            <ProtectedRoute>
              <Dashboard />
            </ProtectedRoute>
          } />

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

          <Route path="*" element={<Navigate to="/" />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App;
