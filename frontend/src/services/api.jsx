import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Error handler
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// ==================== AUTH ====================
export const register = (data) => api.post('/auth/register', data);
export const login = (credentials) => api.post('/auth/login', credentials);
export const logout = () => api.post('/auth/logout');
export const getMe = () => api.get('/auth/me');
export const refreshToken = () => api.post('/auth/refresh');
export const updateProfile = (data) => api.put('/auth/profile', data);

// ==================== USERS ====================
export const getUsers = (params) => api.get('/users', { params });
export const createUser = (data) => api.post('/users', data);
export const getUser = (id) => api.get(`/users/${id}`);
export const updateUser = (id, data) => api.put(`/users/${id}`, data);
export const deleteUser = (id) => api.delete(`/users/${id}`);
export const getUsersByRole = (role) => api.get(`/users/role/${role}`);
export const updateUserStatus = (id, status) => api.put(`/users/${id}/status`, { status });

// ==================== DESTINATIONS ====================
export const getDestinations = (params) => api.get('/destinations', { params });
export const createDestination = (data) => api.post('/destinations', data);
export const getDestination = (id) => api.get(`/destinations/${id}`);
export const updateDestination = (id, data) => api.put(`/destinations/${id}`, data);
export const deleteDestination = (id) => api.delete(`/destinations/${id}`);

// ==================== CITIES ====================
export const getCities = (params) => api.get('/cities', { params });
export const createCity = (data) => api.post('/cities', data);
export const getCity = (id) => api.get(`/cities/${id}`);
export const updateCity = (id, data) => api.put(`/cities/${id}`, data);
export const deleteCity = (id) => api.delete(`/cities/${id}`);
export const getCitiesByDestination = (destinationId) => 
  api.get(`/destinations/${destinationId}/cities`);

// ==================== SIGHTSEEINGS ====================
export const getSightseeings = (params) => api.get('/sightseeings', { params });
export const createSightseeing = (data) => api.post('/sightseeings', data);
export const getSightseeing = (id) => api.get(`/sightseeings/${id}`);
export const updateSightseeing = (id, data) => api.put(`/sightseeings/${id}`, data);
export const deleteSightseeing = (id) => api.delete(`/sightseeings/${id}`);
export const getSightseeingsByCity = (cityId) => 
  api.get(`/cities/${cityId}/sightseeings`);

// ==================== HOTELS ====================
export const getHotels = (params) => api.get('/hotels', { params });
export const createHotel = (data) => api.post('/hotels', data);
export const getHotel = (id) => api.get(`/hotels/${id}`);
export const updateHotel = (id, data) => api.put(`/hotels/${id}`, data);
export const deleteHotel = (id) => api.delete(`/hotels/${id}`);
export const getHotelsByCity = (cityId) => 
  api.get(`/cities/${cityId}/hotels`);

// ==================== ITINERARIES ====================
export const getItineraries = (params) => api.get('/itineraries', { params });
export const createItinerary = (data) => api.post('/itineraries', data);
export const getItinerary = (id) => api.get(`/itineraries/${id}`);
export const updateItinerary = (id, data) => api.put(`/itineraries/${id}`, data);
export const deleteItinerary = (id) => api.delete(`/itineraries/${id}`);
export const addItineraryItem = (id, data) => api.post(`/itineraries/${id}/items`, data);
export const removeItineraryItem = (itineraryId, itemId) => 
  api.delete(`/itineraries/${itineraryId}/items/${itemId}`);
export const assignOperator = (id, operatorId) => 
  api.post(`/itineraries/${id}/assign-operator`, { operator_id: operatorId });

// ==================== BOOKINGS ====================
export const getBookings = (params) => api.get('/bookings', { params });
export const createBooking = (data) => api.post('/bookings', data);
export const getBooking = (id) => api.get(`/bookings/${id}`);
export const updateBooking = (id, data) => api.put(`/bookings/${id}`, data);
export const deleteBooking = (id) => api.delete(`/bookings/${id}`);
export const getBookingsByStatus = (status) => api.get(`/bookings/status/${status}`);
export const confirmBooking = (id) => api.put(`/bookings/${id}/confirm`);
export const cancelBooking = (id) => api.put(`/bookings/${id}/cancel`);
export const getBookingStats = () => api.get('/bookings/statistics');

// ==================== SEARCH ====================
export const searchGlobal = (query) => api.get('/search', { params: { q: query } });
export const searchDestinations = (query) => 
  api.get('/search/destinations', { params: { q: query } });
export const searchCities = (query) => 
  api.get('/search/cities', { params: { q: query } });
export const searchHotels = (query) => 
  api.get('/search/hotels', { params: { q: query } });
export const searchSightseeings = (query) => 
  api.get('/search/sightseeings', { params: { q: query } });

// ==================== STATISTICS ====================
export const getStatistics = () => api.get('/statistics');
export const getStatisticsSummary = () => api.get('/statistics/summary');
export const getUserStats = () => api.get('/statistics/users');
export const getBookingStats = () => api.get('/statistics/bookings');
export const getDestinationStats = () => api.get('/statistics/destinations');

export default api;
