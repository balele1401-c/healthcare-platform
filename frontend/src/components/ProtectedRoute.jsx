import { Navigate } from 'react-router-dom';
import { useAuthStore } from '../stores/authStore.js';

export default function ProtectedRoute({ children }) {
  const { token } = useAuthStore();

  if (!token) {
    return <Navigate to="/login" replace />;
  }

  return children;
}
